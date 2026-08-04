<?php
/** Order form page. */
declare(strict_types=1);

$products = fetch_all('SELECT * FROM products WHERE status = "published" AND is_coming_soon = 0 ORDER BY sort_order ASC');
$areas    = coverage_areas();
$preselect = get('product');
$result   = [];

if (is_post() && post('form_type') === 'order') {
    if (!setting_bool('orders_enabled', true)) {
        $result = ['errors' => ['Online ordering is temporarily unavailable. Please send us a WhatsApp message instead.']];
    } else {
        $result = handle_order_form();
    }
}

seo_set([
    'title'       => 'Order Water Online',
    'description' => 'Order Pak-Everests mineral water online. 19L refills, bottles, packs and dispensers with free delivery and cash on delivery across Rawalpindi.',
    'keywords'    => 'order water online Pakistan, water delivery Rawalpindi, book water bottle, 19 liters water bottle order',
    'breadcrumbs' => ['Order Water' => '/order'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Place Your Water Order';
$heroSubtitle = 'Fill in the form and your order reaches our admin panel, our email inbox and our WhatsApp immediately. We confirm every order by phone or WhatsApp before dispatch.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
  <?php if (!empty($result['success'])):
      $successTitle = 'Order received. Thank you.';
      $successBody  = 'Our team will call or WhatsApp you shortly to confirm your delivery slot. Orders placed before 4:00 PM in daily route areas are normally delivered the same day.';
      require PE_ROOT . '/app/partials/form-success.php';
  else: ?>

    <?php require PE_ROOT . '/app/partials/form-errors.php'; ?>

    <form id="orderForm" class="grid" method="post" action="<?= e(url('order')) ?>" data-guard="true"
          style="grid-template-columns:minmax(0,1.7fr) minmax(280px,1fr);gap:32px;align-items:start;">
      <?= csrf_field() ?>
      <input type="hidden" name="form_type" value="order">
      <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

      <div>
        <!-- ---------- Products ---------- -->
        <div class="form-card" style="margin-bottom:24px;">
          <h2 style="font-size:1.25rem;">1. Choose your products</h2>
          <p class="form-hint" style="margin-bottom:18px;">Set a quantity against everything you need. Your total updates as you type.</p>

          <div class="order-items">
            <?php foreach ($products as $p):
              $qty = ($preselect === $p['slug']) ? 1 : (int) ($_POST['qty'][$p['id']] ?? 0); ?>
            <div class="order-item<?= $qty > 0 ? ' is-selected' : '' ?>"
                 data-price="<?= (float) $p['price'] ?>" data-deposit="<?= (float) $p['security_deposit'] ?>"
                 data-name="<?= e($p['short_name'] ?: $p['name']) ?>">
              <div class="order-item-thumb">
                <img src="<?= e(media_url($p['main_image'], 'product')) ?>" alt="<?= e($p['name']) ?>" loading="lazy" width="64" height="64">
              </div>
              <div class="order-item-info">
                <strong><?= e($p['name']) ?></strong>
                <span>
                  <?= money($p['price']) ?> <?= e($p['price_unit']) ?>
                  <?php if ((float) $p['security_deposit'] > 0): ?>
                    &middot; + <?= money($p['security_deposit']) ?> refundable deposit
                  <?php endif; ?>
                  <?php if ($p['rent_price'] !== null && (float) $p['rent_price'] > 0): ?>
                    &middot; rent <?= money($p['rent_price']) ?> <?= e((string) $p['rent_unit']) ?>
                  <?php endif; ?>
                </span>
              </div>
              <div class="qty-control">
                <button type="button" data-step="-1" aria-label="Decrease quantity of <?= e($p['name']) ?>">&minus;</button>
                <label class="sr-only" for="qty<?= (int) $p['id'] ?>">Quantity of <?= e($p['name']) ?></label>
                <input type="number" id="qty<?= (int) $p['id'] ?>" name="qty[<?= (int) $p['id'] ?>]" value="<?= $qty ?>" min="0" max="999" inputmode="numeric">
                <button type="button" data-step="1" aria-label="Increase quantity of <?= e($p['name']) ?>">+</button>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ---------- Customer details ---------- -->
        <div class="form-card" style="margin-bottom:24px;">
          <h2 style="font-size:1.25rem;">2. Your details</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="customer_name">Full name <span class="req">*</span></label>
              <input type="text" id="customer_name" name="customer_name" required value="<?= e(post('customer_name')) ?>" autocomplete="name" placeholder="e.g. Muhammad Asif">
            </div>
            <div class="form-group">
              <label for="phone">Mobile number <span class="req">*</span></label>
              <input type="tel" id="phone" name="phone" required value="<?= e(post('phone')) ?>" autocomplete="tel" placeholder="03XX XXXXXXX">
            </div>
            <div class="form-group">
              <label for="whatsapp">WhatsApp number</label>
              <input type="tel" id="whatsapp" name="whatsapp" value="<?= e(post('whatsapp')) ?>" placeholder="Leave blank if same as above">
            </div>
            <div class="form-group">
              <label for="email">Email address</label>
              <input type="email" id="email" name="email" value="<?= e(post('email')) ?>" autocomplete="email" placeholder="For your order confirmation">
            </div>
          </div>

          <div class="form-group">
            <label for="address">Complete delivery address <span class="req">*</span></label>
            <textarea id="address" name="address" required placeholder="House or shop number, street, block, landmark, town" autocomplete="street-address"><?= e(post('address')) ?></textarea>
            <span class="form-hint">The more precise the address, the faster our rider finds you. A nearby landmark helps a great deal.</span>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="area">Area <span class="req">*</span></label>
              <select id="area" name="area" required>
                <option value="">Select your area</option>
                <?php foreach ($areas as $a): ?>
                <option value="<?= e($a['area_name']) ?>" <?= post('area') === $a['area_name'] ? 'selected' : '' ?>><?= e($a['area_name']) ?></option>
                <?php endforeach; ?>
                <option value="Other" <?= post('area') === 'Other' ? 'selected' : '' ?>>Other (we will confirm coverage)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="city">City</label>
              <input type="text" id="city" name="city" value="<?= e(post('city')) ?>" placeholder="e.g. Rawalpindi">
            </div>
            <div class="form-group">
              <label for="customer_type">Delivery location type</label>
              <select id="customer_type" name="customer_type">
                <?php foreach (['Home / Residence', 'Office / Corporate', 'School / College', 'Hospital / Clinic', 'Shop / Mart', 'Mosque', 'Factory / Site', 'Event / Function', 'Other'] as $ct): ?>
                <option value="<?= e($ct) ?>" <?= post('customer_type') === $ct ? 'selected' : '' ?>><?= e($ct) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="preferred_time">Preferred delivery time</label>
              <select id="preferred_time" name="preferred_time">
                <?php foreach (['Any time', 'Morning (8 AM - 12 PM)', 'Afternoon (12 PM - 4 PM)', 'Evening (4 PM - 9 PM)'] as $t): ?>
                <option value="<?= e($t) ?>" <?= post('preferred_time') === $t ? 'selected' : '' ?>><?= e($t) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- ---------- Preferences ---------- -->
        <div class="form-card">
          <h2 style="font-size:1.25rem;">3. Delivery and payment</h2>

          <div class="form-group">
            <label>How often do you need this order?</label>
            <div class="radio-cards">
              <?php foreach ([['one_time', 'One time order'], ['weekly', 'Every week'], ['fortnightly', 'Every 2 weeks'], ['monthly', 'Every month']] as [$val, $label]):
                $checked = (post('order_type', 'one_time') === $val) ? 'checked' : ''; ?>
              <label class="radio-card">
                <input type="radio" name="order_type" value="<?= e($val) ?>" <?= $checked ?>>
                <span><?= e($label) ?></span>
              </label>
              <?php endforeach; ?>
            </div>
            <span class="form-hint">Standing orders get route priority over one off orders.</span>
          </div>

          <div class="form-group">
            <label>Payment method</label>
            <div class="radio-cards">
              <?php
              $methods = ['Cash on Delivery'];
              if (setting_bool('easypaisa_enabled', true)) { $methods[] = 'EasyPaisa'; }
              if (setting_bool('jazzcash_enabled', true))  { $methods[] = 'JazzCash'; }
              if (setting_bool('bank_enabled', true))      { $methods[] = 'Bank Transfer'; }
              foreach ($methods as $m):
                $checked = (post('payment_method', 'Cash on Delivery') === $m) ? 'checked' : ''; ?>
              <label class="radio-card">
                <input type="radio" name="payment_method" value="<?= e($m) ?>" <?= $checked ?>>
                <span><?= e($m) ?></span>
              </label>
              <?php endforeach; ?>
            </div>
            <span class="form-hint"><?= e((string) setting('payment_note')) ?></span>
          </div>

          <div class="form-group">
            <label for="delivery_note">Delivery note</label>
            <textarea id="delivery_note" name="delivery_note" placeholder="Gate code, floor number, best time to ring the bell, where to leave bottles, and so on" style="min-height:90px;"><?= e(post('delivery_note')) ?></textarea>
          </div>

          <div class="form-group">
            <label for="message">Message for our team</label>
            <textarea id="message" name="message" placeholder="Anything else we should know about this order" style="min-height:90px;"><?= e(post('message')) ?></textarea>
          </div>

          <div class="check-row">
            <input type="checkbox" id="agree" required>
            <label for="agree">
              I have read and accept the <a href="<?= e(url('terms-and-conditions')) ?>" target="_blank">terms and conditions</a>,
              the <a href="<?= e(url('delivery-policy')) ?>" target="_blank">delivery policy</a> and the
              <a href="<?= e(url('refund-policy')) ?>" target="_blank">refund policy</a>, including the refundable bottle deposit terms.
            </label>
          </div>
        </div>
      </div>

      <!-- ---------- Summary ---------- -->
      <aside>
        <div class="order-summary">
          <h2 style="font-size:1.15rem;">Order summary</h2>
          <div id="summaryLines"></div>

          <div class="summary-row"><span>Water total</span><strong id="sumWater">Rs 0</strong></div>
          <div class="summary-row" id="depositRow"><span>Refundable deposit</span><strong id="sumDeposit">Rs 0</strong></div>
          <div class="summary-row"><span>Delivery</span><strong style="color:var(--success);">Free</strong></div>
          <div class="summary-row summary-total"><span>Total payable</span><strong id="sumTotal">Rs 0</strong></div>

          <p class="form-hint" style="margin:14px 0;">
            The security deposit is a one time refundable amount for returnable bottles. It is refunded in full when
            the bottles are returned in a usable condition.
          </p>

          <button type="submit" class="btn btn-primary btn-block btn-lg">Submit Order</button>

          <a class="btn btn-whatsapp btn-block" style="margin-top:10px;"
             href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I would like to place an order.')) ?>" target="_blank" rel="noopener">
            Or order on WhatsApp
          </a>

          <p class="form-hint" style="margin-top:14px;text-align:center;">
            Questions? Call <?= e(primary_whatsapp()) ?>
          </p>
        </div>
      </aside>
    </form>
  <?php endif; ?>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="grid grid-3">
      <article class="card">
        <h2 style="font-size:1.1rem;">Free delivery, no minimum</h2>
        <p>Every price includes delivery to your door anywhere in our coverage area. No fuel surcharge, no small order fee.</p>
      </article>
      <article class="card">
        <h2 style="font-size:1.1rem;">Same day before 4:00 PM</h2>
        <p>Orders placed before the cut off on a daily route area are normally delivered the same day. Other areas run on scheduled alternate day routes.</p>
      </article>
      <article class="card">
        <h2 style="font-size:1.1rem;">Inspect before you accept</h2>
        <p>Check the seal, cap, batch code and filling date at the door. Our team is instructed to wait while you do.</p>
      </article>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
