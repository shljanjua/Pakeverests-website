<?php
/**
 * Public form handlers. Every submission is validated, stored in the database,
 * emailed to the admin inbox and pushed to WhatsApp via a pre-filled link.
 */

declare(strict_types=1);

/** Build the wa.me link the "Send on WhatsApp" button uses after submission. */
function whatsapp_order_link(string $message): string
{
    $numbers = array_values(array_filter(whatsapp_numbers(), fn($n) => (int) ($n['receives_orders'] ?? 1) === 1));
    $number  = $numbers[0]['number'] ?? primary_whatsapp();
    return wa_link($number, $message);
}

/** Shared spam / CSRF / rate-limit gate. Returns an error string or null. */
function form_gate(string $bucket, int $max = 6): ?string
{
    if (!csrf_verify()) {
        return 'Your session expired. Please refresh the page and submit the form again.';
    }
    if (is_spam_submission()) {
        return 'Your submission was blocked by our spam filter.';
    }
    if (rate_limited($bucket, $max)) {
        return 'Too many submissions from this device. Please wait a few minutes and try again.';
    }
    return null;
}

function valid_phone(string $phone): bool
{
    $digits = preg_replace('/\D+/', '', $phone);
    return strlen($digits) >= 10 && strlen($digits) <= 15;
}

/* =========================================================================
 |  ORDER
 * ====================================================================== */
function handle_order_form(): array
{
    $errors = [];
    $gate   = form_gate('order', 8);
    if ($gate) {
        return ['errors' => [$gate]];
    }

    $name    = post('customer_name');
    $phone   = post('phone');
    $address = post('address');

    if (mb_strlen($name) < 3)        $errors[] = 'Please enter your full name.';
    if (!valid_phone($phone))        $errors[] = 'Please enter a valid mobile number, for example 0333 5592206.';
    if (mb_strlen($address) < 10)    $errors[] = 'Please enter your complete delivery address including house or shop number, street and area.';

    $email = post('email');
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address or leave the field blank.';
    }

    $rawItems = $_POST['qty'] ?? [];
    $items    = [];
    $subtotal = 0.0;
    $deposits = 0.0;

    if (is_array($rawItems)) {
        foreach ($rawItems as $productId => $qty) {
            $qty = (int) $qty;
            if ($qty < 1) {
                continue;
            }
            $product = fetch_one('SELECT * FROM products WHERE id = ? AND status = "published"', [(int) $productId]);
            if (!$product || (int) $product['is_coming_soon'] === 1) {
                continue;
            }
            $unit     = (float) $product['price'];
            $deposit  = (float) $product['security_deposit'] * $qty;
            $line     = $unit * $qty;
            $subtotal += $line;
            $deposits += $deposit;
            $items[]  = [
                'product_id'   => (int) $product['id'],
                'product_name' => $product['name'],
                'quantity'     => $qty,
                'unit_price'   => $unit,
                'deposit'      => $deposit,
                'line_total'   => $line,
            ];
        }
    }

    if (!$items) {
        $errors[] = 'Please choose at least one product and enter a quantity.';
    }

    if ($errors) {
        return ['errors' => $errors];
    }

    $ref   = generate_ref('PE');
    $total = $subtotal + $deposits;

    $orderId = db_insert('orders', [
        'order_ref'      => $ref,
        'customer_name'  => $name,
        'phone'          => $phone,
        'whatsapp'       => post('whatsapp') ?: $phone,
        'email'          => $email,
        'address'        => $address,
        'area'           => post('area'),
        'city'           => post('city'),
        'customer_type'  => post('customer_type'),
        'order_type'     => post('order_type', 'one_time'),
        'preferred_time' => post('preferred_time'),
        'payment_method' => post('payment_method', 'Cash on Delivery'),
        'message'        => post('message'),
        'delivery_note'  => post('delivery_note'),
        'subtotal'       => $subtotal,
        'deposit_total'  => $deposits,
        'total'          => $total,
        'status'         => 'new',
        'source'         => 'website',
        'ip_address'     => client_ip(),
        'created_at'     => date('Y-m-d H:i:s'),
    ]);

    foreach ($items as $item) {
        $item['order_id'] = $orderId;
        db_insert('order_items', $item);
    }

    /* ---- Build the message used for both email and WhatsApp -------------- */
    $lines = [];
    foreach ($items as $item) {
        $lines[] = sprintf(
            '%s x %d = %s%s',
            $item['product_name'],
            $item['quantity'],
            money($item['line_total']),
            $item['deposit'] > 0 ? ' (+ ' . money($item['deposit']) . ' refundable deposit)' : ''
        );
    }

    $waText = "*NEW ORDER - " . $ref . "*\n"
        . "-------------------------\n"
        . "Name: $name\n"
        . "Phone: $phone\n"
        . ($email !== '' ? "Email: $email\n" : '')
        . "Address: $address\n"
        . (post('area') !== '' ? "Area: " . post('area') . "\n" : '')
        . "-------------------------\n"
        . implode("\n", $lines) . "\n"
        . "-------------------------\n"
        . 'Water total: ' . money($subtotal) . "\n"
        . ($deposits > 0 ? 'Refundable deposit: ' . money($deposits) . "\n" : '')
        . 'GRAND TOTAL: ' . money($total) . "\n"
        . 'Payment: ' . post('payment_method', 'Cash on Delivery') . "\n"
        . (post('preferred_time') !== '' ? 'Preferred time: ' . post('preferred_time') . "\n" : '')
        . (post('delivery_note') !== '' ? 'Delivery note: ' . post('delivery_note') . "\n" : '')
        . (post('message') !== '' ? 'Message: ' . post('message') . "\n" : '')
        . "-------------------------\nSent from pakeverests.site";

    $adminHtml = '<p>A new order has been received on the website.</p>'
        . email_table([
            'Order reference' => $ref,
            'Customer'        => $name,
            'Phone'           => $phone,
            'WhatsApp'        => post('whatsapp') ?: $phone,
            'Email'           => $email,
            'Address'         => $address,
            'Area'            => post('area'),
            'City'            => post('city'),
            'Customer type'   => post('customer_type'),
            'Order type'      => post('order_type'),
            'Preferred time'  => post('preferred_time'),
            'Payment method'  => post('payment_method'),
            'Products'        => implode("\n", $lines),
            'Water total'     => money($subtotal),
            'Refundable deposit' => money($deposits),
            'Grand total'     => money($total),
            'Delivery note'   => post('delivery_note'),
            'Message'         => post('message'),
            'IP address'      => client_ip(),
        ])
        . '<p style="margin-top:18px;"><a href="' . SITE_URL . '/admin/orders" style="background:#0b6fa4;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;display:inline-block;">Open in admin panel</a></p>';

    send_mail((string) setting('order_notify_email', contact_email()), 'New order ' . $ref . ' from ' . $name, $adminHtml, $email, 'order');

    if ($email !== '') {
        $customerHtml = '<p>Dear ' . e($name) . ',</p>'
            . '<p>Thank you for your order. We have received it and our team will confirm your delivery slot on WhatsApp or by phone shortly.</p>'
            . email_table([
                'Order reference' => $ref,
                'Products'        => implode("\n", $lines),
                'Water total'     => money($subtotal),
                'Refundable deposit' => money($deposits),
                'Grand total'     => money($total),
                'Delivery address' => $address,
                'Payment method'  => post('payment_method'),
            ])
            . '<p>If anything above is incorrect, reply to this email or message us on WhatsApp at ' . e(primary_whatsapp()) . '.</p>';
        send_mail($email, 'Your Pak-Everests order ' . $ref, $customerHtml, contact_email(), 'order_customer');
    }

    return [
        'success'  => true,
        'ref'      => $ref,
        'wa_link'  => whatsapp_order_link($waText),
        'total'    => $total,
    ];
}

/* =========================================================================
 |  CONTACT
 * ====================================================================== */
function handle_contact_form(string $formType = 'contact'): array
{
    $gate = form_gate('contact', 6);
    if ($gate) {
        return ['errors' => [$gate]];
    }

    $errors  = [];
    $name    = post('name');
    $email   = post('email');
    $phone   = post('phone');
    $message = post('message');

    if (mb_strlen($name) < 3)     $errors[] = 'Please enter your name.';
    if (mb_strlen($message) < 10) $errors[] = 'Please write your message, at least a sentence.';
    if ($email === '' && $phone === '') {
        $errors[] = 'Please provide either an email address or a phone number so we can reply.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($errors) {
        return ['errors' => $errors];
    }

    $ref = generate_ref('MSG');
    db_insert('contact_messages', [
        'ref'        => $ref,
        'name'       => $name,
        'email'      => $email,
        'phone'      => $phone,
        'subject'    => post('subject', 'Website enquiry'),
        'message'    => $message,
        'area'       => post('area'),
        'form_type'  => $formType,
        'status'     => 'new',
        'ip_address' => client_ip(),
        'created_at' => date('Y-m-d H:i:s'),
    ]);

    $html = '<p>A new message has been submitted through the website.</p>' . email_table([
        'Reference' => $ref,
        'Name'      => $name,
        'Email'     => $email,
        'Phone'     => $phone,
        'Area'      => post('area'),
        'Subject'   => post('subject'),
        'Message'   => $message,
        'Form'      => $formType,
        'IP address' => client_ip(),
    ]);
    send_mail(contact_email(), 'New website message ' . $ref . ' from ' . $name, $html, $email, 'contact');

    $waText = "*WEBSITE MESSAGE - $ref*\nName: $name\nPhone: $phone\nEmail: $email\nSubject: " . post('subject') . "\n\n$message";

    return ['success' => true, 'ref' => $ref, 'wa_link' => whatsapp_order_link($waText)];
}

/* =========================================================================
 |  DISTRIBUTOR APPLICATION
 * ====================================================================== */
function handle_distributor_form(): array
{
    $gate = form_gate('distributor', 4);
    if ($gate) {
        return ['errors' => [$gate]];
    }

    $errors = [];
    $name   = post('applicant_name');
    $phone  = post('phone');
    $area   = post('area_requested');

    if (mb_strlen($name) < 3)  $errors[] = 'Please enter your full name.';
    if (!valid_phone($phone))  $errors[] = 'Please enter a valid mobile number.';
    if (mb_strlen($area) < 3)  $errors[] = 'Please tell us which area or territory you want to cover.';

    $email = post('email');
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($errors) {
        return ['errors' => $errors];
    }

    $docPath = '';
    if (!empty($_FILES['document']['name'])) {
        $up = handle_upload($_FILES['document'], 'distributors', array_merge(PE_IMAGE_TYPES, PE_DOC_TYPES));
        if ($up['ok']) {
            $docPath = $up['path'];
            media_record($up, 'Distributor document', 'distributors');
        } else {
            return ['errors' => [$up['error']]];
        }
    }

    $ref = generate_ref('DIST');
    db_insert('distributor_applications', [
        'ref'              => $ref,
        'applicant_name'   => $name,
        'business_name'    => post('business_name'),
        'cnic'             => post('cnic'),
        'phone'            => $phone,
        'whatsapp'         => post('whatsapp') ?: $phone,
        'email'            => $email,
        'city'             => post('city'),
        'area_requested'   => $area,
        'address'          => post('address'),
        'business_type'    => post('business_type'),
        'experience_years' => post('experience_years'),
        'has_vehicle'      => post('has_vehicle'),
        'vehicle_details'  => post('vehicle_details'),
        'has_storage'      => post('has_storage'),
        'storage_details'  => post('storage_details'),
        'investment_range' => post('investment_range'),
        'monthly_target'   => post('monthly_target'),
        'message'          => post('message'),
        'document_path'    => $docPath,
        'status'           => 'new',
        'ip_address'       => client_ip(),
        'created_at'       => date('Y-m-d H:i:s'),
    ]);

    $rows = [
        'Reference'        => $ref,
        'Applicant'        => $name,
        'Business name'    => post('business_name'),
        'CNIC'             => post('cnic'),
        'Phone'            => $phone,
        'WhatsApp'         => post('whatsapp'),
        'Email'            => $email,
        'City'             => post('city'),
        'Territory requested' => $area,
        'Address'          => post('address'),
        'Business type'    => post('business_type'),
        'Experience'       => post('experience_years'),
        'Vehicle'          => post('has_vehicle') . ' ' . post('vehicle_details'),
        'Storage'          => post('has_storage') . ' ' . post('storage_details'),
        'Investment range' => post('investment_range'),
        'Monthly target'   => post('monthly_target'),
        'Message'          => post('message'),
    ];
    send_mail(contact_email(), 'New distributor application ' . $ref . ' - ' . $area, '<p>A new distributor application has been received.</p>' . email_table($rows), $email, 'distributor');

    $waText = "*DISTRIBUTOR APPLICATION - $ref*\nName: $name\nBusiness: " . post('business_name')
        . "\nPhone: $phone\nCity: " . post('city') . "\nTerritory: $area\nVehicle: " . post('has_vehicle')
        . "\nStorage: " . post('has_storage') . "\nInvestment: " . post('investment_range')
        . "\nTarget: " . post('monthly_target') . "\n\n" . post('message');

    return ['success' => true, 'ref' => $ref, 'wa_link' => whatsapp_order_link($waText)];
}

/* =========================================================================
 |  CUSTOM LABEL REQUEST
 * ====================================================================== */
function handle_label_form(): array
{
    $gate = form_gate('label', 5);
    if ($gate) {
        return ['errors' => [$gate]];
    }

    $errors = [];
    $name   = post('contact_name');
    $phone  = post('phone');

    if (mb_strlen($name) < 3) $errors[] = 'Please enter your name.';
    if (!valid_phone($phone)) $errors[] = 'Please enter a valid mobile number.';
    if (post('bottle_size') === '') $errors[] = 'Please choose a bottle size.';
    if (post('quantity') === '')    $errors[] = 'Please tell us the approximate quantity you need.';

    $email = post('email');
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($errors) {
        return ['errors' => $errors];
    }

    $artwork = '';
    $logo    = '';
    foreach ([['artwork', 'artwork'], ['logo', 'logo']] as [$field, $label]) {
        if (!empty($_FILES[$field]['name'])) {
            $up = handle_upload($_FILES[$field], 'labels', array_merge(PE_IMAGE_TYPES, PE_DOC_TYPES));
            if (!$up['ok']) {
                return ['errors' => ['Problem with the ' . $label . ' file: ' . $up['error']]];
            }
            media_record($up, 'Custom label ' . $label, 'labels');
            if ($field === 'artwork') { $artwork = $up['path']; } else { $logo = $up['path']; }
        }
    }

    $ref = generate_ref('LBL');
    db_insert('label_requests', [
        'ref'           => $ref,
        'contact_name'  => $name,
        'company_name'  => post('company_name'),
        'designation'   => post('designation'),
        'phone'         => $phone,
        'whatsapp'      => post('whatsapp') ?: $phone,
        'email'         => $email,
        'city'          => post('city'),
        'address'       => post('address'),
        'bottle_size'   => post('bottle_size'),
        'quantity'      => post('quantity'),
        'occasion'      => post('occasion'),
        'required_date' => post('required_date') ?: null,
        'has_artwork'   => post('has_artwork'),
        'artwork_path'  => $artwork,
        'logo_path'     => $logo,
        'brand_colors'  => post('brand_colors'),
        'label_text'    => post('label_text'),
        'design_help'   => post('design_help'),
        'message'       => post('message'),
        'status'        => 'new',
        'ip_address'    => client_ip(),
        'created_at'    => date('Y-m-d H:i:s'),
    ]);

    $rows = [
        'Reference'     => $ref,
        'Contact'       => $name,
        'Company'       => post('company_name'),
        'Designation'   => post('designation'),
        'Phone'         => $phone,
        'Email'         => $email,
        'City'          => post('city'),
        'Bottle size'   => post('bottle_size'),
        'Quantity'      => post('quantity'),
        'Occasion'      => post('occasion'),
        'Required by'   => post('required_date'),
        'Has artwork'   => post('has_artwork'),
        'Artwork file'  => $artwork ? SITE_URL . '/' . $artwork : '',
        'Logo file'     => $logo ? SITE_URL . '/' . $logo : '',
        'Brand colours' => post('brand_colors'),
        'Label text'    => post('label_text'),
        'Design help'   => post('design_help'),
        'Message'       => post('message'),
    ];
    send_mail(contact_email(), 'New custom label request ' . $ref . ' - ' . post('company_name', $name), '<p>A new custom label request has been received.</p>' . email_table($rows), $email, 'label');

    $waText = "*CUSTOM LABEL REQUEST - $ref*\nName: $name\nCompany: " . post('company_name')
        . "\nPhone: $phone\nBottle: " . post('bottle_size') . "\nQuantity: " . post('quantity')
        . "\nOccasion: " . post('occasion') . "\nRequired by: " . post('required_date')
        . "\n\n" . post('message');

    return ['success' => true, 'ref' => $ref, 'wa_link' => whatsapp_order_link($waText)];
}

/* =========================================================================
 |  REVIEW
 * ====================================================================== */
function handle_review_form(): array
{
    if (!setting_bool('reviews_open', true)) {
        return ['errors' => ['Review submissions are currently closed.']];
    }
    $gate = form_gate('review', 3);
    if ($gate) {
        return ['errors' => [$gate]];
    }

    $errors = [];
    $name   = post('reviewer_name');
    $body   = post('body');
    $rating = (int) post('rating', 5);

    if (mb_strlen($name) < 3)  $errors[] = 'Please enter your name.';
    if (mb_strlen($body) < 15) $errors[] = 'Please write a little more about your experience.';
    if ($rating < 1 || $rating > 5) $errors[] = 'Please select a star rating between 1 and 5.';

    if ($errors) {
        return ['errors' => $errors];
    }

    $productId = (int) post('product_id', 0);
    $ref = db_insert('reviews', [
        'product_id'     => $productId ?: null,
        'reviewer_name'  => $name,
        'reviewer_email' => post('reviewer_email'),
        'reviewer_role'  => post('reviewer_role'),
        'location'       => post('location'),
        'rating'         => $rating,
        'title'          => post('title'),
        'body'           => $body,
        'status'         => 'pending',
        'ip_address'     => client_ip(),
        'created_at'     => date('Y-m-d H:i:s'),
    ]);

    send_mail(contact_email(), 'New review awaiting approval (#' . $ref . ')', '<p>A customer has submitted a review. It will stay hidden until you approve it in the admin panel.</p>' . email_table([
        'Name'     => $name,
        'Location' => post('location'),
        'Rating'   => $rating . ' out of 5',
        'Title'    => post('title'),
        'Review'   => $body,
    ]), '', 'review');

    return ['success' => true, 'pending' => true];
}

/* =========================================================================
 |  JOB APPLICATION
 * ====================================================================== */
function handle_job_form(): array
{
    $gate = form_gate('career', 4);
    if ($gate) {
        return ['errors' => [$gate]];
    }

    $errors = [];
    $name   = post('name');
    $phone  = post('phone');
    if (mb_strlen($name) < 3) $errors[] = 'Please enter your full name.';
    if (!valid_phone($phone)) $errors[] = 'Please enter a valid mobile number.';
    if (post('job_title') === '') $errors[] = 'Please select the position you are applying for.';

    if ($errors) {
        return ['errors' => $errors];
    }

    $cv = '';
    if (!empty($_FILES['cv']['name'])) {
        $up = handle_upload($_FILES['cv'], 'careers', array_merge(PE_DOC_TYPES, ['jpg', 'jpeg', 'png', 'webp']));
        if (!$up['ok']) {
            return ['errors' => [$up['error']]];
        }
        $cv = $up['path'];
        media_record($up, 'CV - ' . $name, 'careers');
    }

    $ref = generate_ref('JOB');
    db_insert('job_applications', [
        'ref'        => $ref,
        'job_id'     => (int) post('job_id', 0) ?: null,
        'job_title'  => post('job_title'),
        'name'       => $name,
        'email'      => post('email'),
        'phone'      => $phone,
        'city'       => post('city'),
        'experience' => post('experience'),
        'education'  => post('education'),
        'cover_note' => post('cover_note'),
        'cv_path'    => $cv,
        'status'     => 'new',
        'created_at' => date('Y-m-d H:i:s'),
    ]);

    send_mail(contact_email(), 'New job application ' . $ref . ' - ' . post('job_title'), '<p>A new job application has been received.</p>' . email_table([
        'Reference'  => $ref,
        'Position'   => post('job_title'),
        'Name'       => $name,
        'Phone'      => $phone,
        'Email'      => post('email'),
        'City'       => post('city'),
        'Experience' => post('experience'),
        'Education'  => post('education'),
        'Cover note' => post('cover_note'),
        'CV'         => $cv ? SITE_URL . '/' . $cv : 'Not attached',
    ]), post('email'), 'career');

    return ['success' => true, 'ref' => $ref];
}

/* =========================================================================
 |  NEWSLETTER SUBSCRIBE  (handled globally from index.php)
 * ====================================================================== */
function handle_subscribe_form(): void
{
    $email = post('subscribe_email');
    if (!csrf_verify() || is_spam_submission()) {
        flash('error', 'Your session expired. Please try subscribing again.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Please enter a valid email address to subscribe.');
    } elseif (rate_limited('subscribe', 5)) {
        flash('error', 'Too many attempts. Please try again later.');
    } else {
        try {
            q(
                'INSERT INTO subscribers (email, name, source, status, ip_address, created_at)
                 VALUES (?, ?, ?, "active", ?, NOW())
                 ON DUPLICATE KEY UPDATE status = "active"',
                [$email, post('subscribe_name'), post('subscribe_source', 'footer'), client_ip()]
            );
            flash('success', 'Thank you. You are subscribed to Pak-Everests updates and offers.');
        } catch (Throwable $e) {
            flash('error', 'We could not save your subscription. Please try again.');
        }
    }
    redirect(($_SERVER['HTTP_REFERER'] ?? '/') . '#subscribe');
}
