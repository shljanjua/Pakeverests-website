-- ==========================================================================
--  Pak-Everests — CMS pages (legal + policy). Import AFTER 02-seed-data.sql
--  Every page below is fully editable from Admin -> Pages, and new pages can
--  be created from there at any time.
-- ==========================================================================

SET NAMES utf8mb4;

INSERT INTO `pages`
(`slug`,`title`,`subtitle`,`content`,`nav_group`,`is_system`,`meta_title`,`meta_description`,`meta_keywords`,`sort_order`,`status`,`created_at`) VALUES

-- --------------------------------------------------------------------------
('privacy-policy','Privacy Policy','How Pak-Everests collects, uses and protects your personal information',
'<h2>1. Introduction</h2>
<p>Pak-Everests Bottled Drinking Water operates the website pakeverests.site and supplies bottled drinking water, dispensers and related services across the Potohar region of Punjab, Pakistan. This privacy policy explains what personal information we collect, why we collect it, how we use and protect it, and what rights you have over it.</p>
<p>By using this website, placing an order, submitting a form or contacting us on WhatsApp, you agree to the practices described here. If you do not agree with any part of this policy, please do not use the website or our services.</p>

<h2>2. Information we collect</h2>
<h3>2.1 Information you give us directly</h3>
<p>When you place an order, request a quotation, apply to be a distributor, request custom label printing, apply for a job or send a message, we collect the information you enter into that form. Depending on the form this may include:</p>
<ul>
<li>Your full name and, where relevant, your company or organisation name</li>
<li>Mobile number, WhatsApp number and landline number</li>
<li>Email address</li>
<li>Complete delivery address, area and city</li>
<li>Delivery instructions, preferred delivery time and any notes you add</li>
<li>Order details such as products, quantities and payment method</li>
<li>Business details for distributor applications, including CNIC, storage capacity, vehicle details and investment capacity</li>
<li>Artwork, logo files and brand information for custom label requests</li>
<li>Curriculum vitae and employment history for job applications</li>
</ul>

<h3>2.2 Information collected automatically</h3>
<p>When you visit the website we automatically record technical information for security and analytics purposes:</p>
<ul>
<li>IP address, and the country, region and city that IP address resolves to</li>
<li>Browser type, operating system and device category</li>
<li>Pages viewed, time of visit and the page that referred you</li>
<li>Session identifier stored in a first party cookie</li>
</ul>
<p>This data is used in aggregate to understand which products and areas generate interest, to detect abuse, and to improve the website. We do not attempt to identify individual visitors from this data.</p>

<h3>2.3 Information from third party tools</h3>
<p>If enabled, Google Analytics, Google Search Console, Meta Pixel and advertising networks may set their own cookies and collect usage data under their own privacy policies. Links to those policies are given in section 8.</p>

<h2>3. How we use your information</h2>
<p>We use the information we collect only for these purposes:</p>
<ul>
<li><strong>Fulfilling your order.</strong> Processing, scheduling, delivering and invoicing your order, and contacting you about it by phone or WhatsApp.</li>
<li><strong>Managing bottle deposits.</strong> Recording which bottles are held at your address so that your refundable deposit can be tracked and returned correctly.</li>
<li><strong>Responding to enquiries.</strong> Answering questions, issuing quotations and following up on complaints.</li>
<li><strong>Assessing applications.</strong> Reviewing distributor applications and job applications.</li>
<li><strong>Producing custom labels.</strong> Preparing artwork proofs and printing labels to your specification.</li>
<li><strong>Service communication.</strong> Sending order confirmations, delivery updates and, if you subscribed, occasional offers and news. You may opt out of marketing messages at any time.</li>
<li><strong>Improving the website and service.</strong> Understanding demand by area and product, and fixing problems.</li>
<li><strong>Legal and regulatory compliance.</strong> Meeting record keeping obligations under applicable Pakistani law and food authority requirements.</li>
</ul>

<h2>4. Legal basis and consent</h2>
<p>We process your data on the basis of the contract between us when you place an order, on the basis of your consent when you subscribe to updates or submit an optional form, and on the basis of our legitimate business interest when we secure the website and analyse aggregate usage. Where we rely on consent, you may withdraw it at any time by contacting us.</p>

<h2>5. Sharing your information</h2>
<p><strong>We do not sell your personal data to anyone, under any circumstances.</strong></p>
<p>We share information only where it is necessary to deliver the service you asked for:</p>
<ul>
<li><strong>Delivery staff and appointed distributors</strong> receive your name, address, phone number and delivery instructions so that they can complete your delivery.</li>
<li><strong>Printing partners</strong> receive only the artwork and quantity needed for a custom label order.</li>
<li><strong>Payment channels</strong> such as EasyPaisa, JazzCash and our bank process transaction information under their own terms.</li>
<li><strong>Hosting and email providers</strong> store website and email data on our behalf under confidentiality obligations.</li>
<li><strong>Government authorities</strong> where we are required to disclose information by law, court order or a lawful request from the Punjab Food Authority or another regulator.</li>
</ul>

<h2>6. Data retention</h2>
<p>We keep personal data only as long as there is a reason to keep it:</p>
<ul>
<li>Order and delivery records, including bottle deposit records, are kept for seven years for accounting and dispute purposes</li>
<li>Contact messages and enquiries are kept for two years</li>
<li>Distributor applications are kept for three years</li>
<li>Unsuccessful job applications are kept for one year unless you ask us to delete them sooner</li>
<li>Analytics page view records are kept for twenty four months and then deleted</li>
<li>Newsletter subscriptions are kept until you unsubscribe</li>
</ul>

<h2>7. Data security</h2>
<p>We protect your information with the following measures:</p>
<ul>
<li>The entire website is served over encrypted HTTPS connections</li>
<li>Administrative access is password protected with hashed credentials and login attempt limiting</li>
<li>Database access is restricted to the application account only</li>
<li>All form submissions are protected against cross site request forgery and are validated on the server</li>
<li>Uploaded files are restricted by type and are stored outside the executable path</li>
</ul>
<p>No system can be guaranteed completely secure. If we ever become aware of a breach affecting your personal data, we will notify affected customers without undue delay.</p>

<h2>8. Cookies and tracking</h2>
<p>This website uses a small number of cookies. A strictly necessary session cookie keeps your form data and security token valid. A preference cookie remembers whether you chose light or dark mode. A first party analytics identifier lets us count unique visits without identifying you.</p>
<p>If third party services are enabled by the site owner, those services set their own cookies. You can review their policies at the Google Privacy Policy, the Meta Privacy Policy and the privacy policy of any advertising network in use. You can block or delete cookies through your browser settings, although the site may not function fully without the necessary cookies.</p>

<h2>9. Children</h2>
<p>This website is not directed at children under the age of thirteen and we do not knowingly collect personal information from them. If you believe a child has provided us with personal data, please contact us and we will delete it.</p>

<h2>10. Your rights</h2>
<p>You may at any time ask us to:</p>
<ul>
<li>Tell you what personal data we hold about you</li>
<li>Correct data that is inaccurate or out of date</li>
<li>Delete data we no longer need to keep for legal or accounting reasons</li>
<li>Stop sending you marketing messages</li>
<li>Provide a copy of the data you gave us</li>
</ul>
<p>Send any such request to info@pakeverests.site or WhatsApp 0333 5592206. We will respond within thirty days. We may ask you to verify your identity before acting on a request.</p>

<h2>11. Changes to this policy</h2>
<p>We may update this policy as our services or legal obligations change. The revised version takes effect when it is published on this page. Material changes will be announced on the website news ticker.</p>

<h2>12. Contact</h2>
<p>Questions about this policy should be sent to:</p>
<p><strong>Pak-Everests Bottled Drinking Water</strong><br>
Main G.T. Road, Gujar Khan, District Rawalpindi, Punjab, Pakistan<br>
Email: info@pakeverests.site<br>
WhatsApp: 0333 5592206 and 0332 2901309</p>',
'legal',1,'Privacy Policy | Pak-Everests Bottled Drinking Water',
'How Pak-Everests collects, uses, shares and protects the personal information of customers, distributors and website visitors.',
'privacy policy, data protection, Pak-Everests privacy',1,'published',NOW()),

-- --------------------------------------------------------------------------
('terms-and-conditions','Terms and Conditions','The terms that govern the use of this website and the supply of our products',
'<h2>1. Agreement</h2>
<p>These terms and conditions govern your use of pakeverests.site and the supply of bottled drinking water, dispensers, custom label services and related products by Pak-Everests Bottled Drinking Water. By using this website or placing an order with us through any channel, you accept these terms in full.</p>

<h2>2. About us</h2>
<p>Pak-Everests Bottled Drinking Water is a bottled drinking water manufacturer approved by the Punjab Food Authority, operating a filling plant at Main G.T. Road, Gujar Khan, District Rawalpindi, Punjab, Pakistan. References to we, us and our mean Pak-Everests. References to you and your mean the customer.</p>

<h2>3. Orders</h2>
<h3>3.1 How an order is formed</h3>
<p>An order placed through the website order form, by WhatsApp, by phone or by email is an offer to buy. A contract is formed only when we confirm the order and the delivery slot by phone, WhatsApp or email. We may decline any order.</p>
<h3>3.2 Order accuracy</h3>
<p>You are responsible for the accuracy of the name, phone number and complete address you provide. Deliveries that fail because of an incorrect or incomplete address may be rescheduled, and repeated failures may result in a delivery charge.</p>
<h3>3.3 Order changes and cancellation</h3>
<p>You may change or cancel an order at no cost until the delivery vehicle has been loaded for your route. After that point, cancellation of a scheduled delivery may be treated as a failed delivery.</p>

<h2>4. Prices and payment</h2>
<h3>4.1 Published prices</h3>
<p>Prices shown on this website are in Pakistan Rupees and are inclusive of delivery within our published coverage area, unless the product page states otherwise. Facility filling at the plant is a collection service and free delivery does not apply to it.</p>
<h3>4.2 Price changes</h3>
<p>We may revise prices at any time. The price that applies to your order is the price confirmed at the time we accept the order. Standing orders and distributor pricing are governed by the relevant signed agreement.</p>
<h3>4.3 Payment methods</h3>
<p>We accept cash on delivery, EasyPaisa, JazzCash and direct bank transfer. Where payment is made in advance, please send the transaction record to our WhatsApp so it can be matched to your order.</p>
<h3>4.4 Overdue accounts</h3>
<p>Credit accounts issued to corporate, institutional and distributor customers are payable within the period stated on the invoice. We may suspend supply on any account that remains overdue.</p>

<h2>5. Bottle security deposit</h2>
<p>Returnable 19 litre bottles are supplied against a refundable security deposit of Rs 1,500 per bottle. The deposit secures the bottle only and is not an advance against the price of water. The bottle remains the property of Pak-Everests at all times. The deposit is refunded when bottles are returned in a usable condition in accordance with our refund policy and damage policy.</p>

<h2>6. Dispensers</h2>
<p>Dispensers may be purchased outright at the published price or taken on monthly rental. A rented dispenser remains the property of Pak-Everests and must be returned in working condition on termination of the rental. Rental terms, the security deposit and the responsibilities of each party are set out in the dispenser agreement issued at installation and in our damage policy.</p>

<h2>7. Delivery</h2>
<p>Delivery timing, coverage areas, failed delivery handling and force majeure are governed by our delivery policy, which forms part of these terms. Delivery times given are estimates and are not guaranteed to the minute.</p>

<h2>8. Custom label services</h2>
<h3>8.1 Your artwork</h3>
<p>By submitting artwork, a logo or brand material you confirm that you own it or hold the rights to use it, and you indemnify Pak-Everests against any third party claim arising from printing it. We may refuse artwork that is unlawful, misleading, offensive, or that infringes a trademark.</p>
<h3>8.2 Approval and production</h3>
<p>Production begins only after you approve a digital proof in writing or on WhatsApp. Once approved, the order cannot be cancelled and the printed labels cannot be returned, because they have no resale value to any other customer.</p>
<h3>8.3 Regulatory content</h3>
<p>Every custom label must carry the statutory information required by the Punjab Food Authority, including the plant name, licence details, batch code and filling date. This information cannot be removed or obscured by customer artwork.</p>

<h2>9. Product quality and food safety</h2>
<p>We warrant that our water is produced under Punjab Food Authority approved conditions and meets applicable PSQCA drinking water requirements at the moment it leaves our plant. This warranty is conditional on correct storage by the customer. Water stored in direct sunlight, near heat, near chemicals or fuel, or decanted into unclean containers is outside our control and outside this warranty.</p>

<h2>10. Limitation of liability</h2>
<p>To the maximum extent permitted by Pakistani law, our total liability arising out of any order is limited to the value of that order plus any refundable deposit held. We are not liable for indirect or consequential loss, including loss of profit, loss of business or loss of opportunity. Nothing in these terms excludes liability that cannot lawfully be excluded, including liability for death or personal injury caused by our negligence.</p>

<h2>11. Intellectual property</h2>
<p>The Pak-Everests name, logo, label designs, product photography, website design and website content are the property of Pak-Everests. You may not copy, reproduce or use them without written permission. Content on this website is provided for information and does not constitute medical advice.</p>

<h2>12. Acceptable use of this website</h2>
<p>You may not use this website to submit false orders, to transmit malicious code, to attempt unauthorised access to the administration area, to scrape content in bulk, or for any unlawful purpose. We may block any IP address that abuses the website or its forms.</p>

<h2>13. Suspension and termination of supply</h2>
<p>We may suspend or terminate supply where an account is overdue, where bottles or equipment are not returned, where our staff are subjected to abuse, or where a customer repeatedly refuses scheduled deliveries. Any refundable deposit held will be settled in accordance with the refund policy.</p>

<h2>14. Complaints and disputes</h2>
<p>We take complaints seriously and aim to resolve every one quickly. The full escalation route, response timelines and the arbitration procedure are set out in our dispute resolution page.</p>

<h2>15. Governing law and jurisdiction</h2>
<p>These terms are governed by the laws of the Islamic Republic of Pakistan. The courts at Gujar Khan and Rawalpindi, District Rawalpindi, Punjab, have exclusive jurisdiction over any dispute arising out of them.</p>

<h2>16. Changes to these terms</h2>
<p>We may update these terms from time to time. The version published on this page at the time your order is accepted is the version that applies to that order.</p>

<h2>17. Contact</h2>
<p>Email info@pakeverests.site or WhatsApp 0333 5592206 and 0332 2901309.</p>',
'legal',1,'Terms and Conditions | Pak-Everests Bottled Drinking Water',
'The terms and conditions that govern orders, bottle deposits, dispenser rental, custom label printing and use of the Pak-Everests website.',
'terms and conditions, terms of service, Pak-Everests terms',2,'published',NOW()),

-- --------------------------------------------------------------------------
('refund-policy','Refund Policy','Deposit refunds, product refunds and how we put things right',
'<h2>1. Our commitment</h2>
<p>Pak-Everests sells drinking water, which is a food product. We cannot resell a bottle once it has left our chain of custody, so our refund policy is built around two things instead: fast replacement when something is genuinely wrong with the product, and a clean, predictable process for returning bottle security deposits.</p>

<h2>2. Bottle security deposit refunds</h2>
<h3>2.1 What the deposit is</h3>
<p>A refundable security deposit of <strong>Rs 1,500 per 19 litre bottle</strong> is collected on your first delivery. It secures the bottle itself. It is not an advance payment for water and it is never adjusted against the price of refills.</p>
<h3>2.2 How to claim your deposit back</h3>
<ol>
<li>Tell us on WhatsApp or by phone that you wish to close your account, and confirm how many bottles you are returning.</li>
<li>Hand the empty bottles to our delivery team on the next scheduled route, or return them to the plant at Gujar Khan.</li>
<li>Our team inspects each bottle at the point of collection and issues a collection receipt stating the number of bottles received and their condition.</li>
<li>The refund is paid in cash at the door, or transferred to EasyPaisa, JazzCash or your bank account, whichever you prefer.</li>
</ol>
<h3>2.3 Refund timeline</h3>
<p>Cash refunds are normally settled at the time of collection. Bank or mobile wallet transfers are completed within <strong>seven working days</strong> of collection. If a bottle condition is disputed, the timeline pauses until the dispute is resolved under our dispute resolution process.</p>
<h3>2.4 Deductions from the deposit</h3>
<p>The full deposit is returned for bottles showing ordinary wear. Deductions apply only in the cases listed in our <a href="/damage-policy">bottle and equipment damage policy</a>, principally where a bottle is lost, cracked, burnt, deformed by heat, or contaminated by chemicals, fuel or paint. Scratches, faded printing and general marks from normal use are never charged.</p>
<h3>2.5 Proof of deposit</h3>
<p>Keep your deposit receipt. If you cannot produce it, we will refund on the basis of the bottle count recorded against your name, address and phone number in our system, which is the record we maintain for every deposit customer.</p>

<h2>3. Product refunds and replacements</h2>
<h3>3.1 When we replace free of charge</h3>
<p>Contact us within <strong>twenty four hours of delivery</strong> and we will replace the product at no cost, with no argument, if:</p>
<ul>
<li>The seal or cap was broken or missing on arrival</li>
<li>The bottle was leaking, cracked or damaged in transit</li>
<li>The water has an unusual taste, smell, colour or visible particles</li>
<li>You received the wrong product, the wrong size or the wrong quantity</li>
<li>The product was past its printed shelf life on the day of delivery</li>
</ul>
<h3>3.2 How to report it</h3>
<p>Send a WhatsApp message to <strong>0333 5592206</strong> or <strong>0332 2901309</strong> with a photograph of the bottle, the batch code and filling date printed on it, and a short description. A photograph lets us trace the batch immediately and check the production record for that day.</p>
<h3>3.3 Replacement or refund</h3>
<p>Our first response is always a free replacement on the next delivery run, or the same day where the route allows. If you prefer a refund instead of a replacement, we will refund the full price of the affected product.</p>
<h3>3.4 What we retain</h3>
<p>We collect the affected product when we deliver the replacement. This is not a formality. It goes to our laboratory so the batch can be checked and the cause identified, which is how quality problems get fixed rather than repeated.</p>

<h2>4. Orders cancelled before delivery</h2>
<p>An order cancelled before the delivery vehicle is loaded is refunded in full where payment was made in advance. Advance payments are returned by the same channel used to pay, within seven working days.</p>

<h2>5. Dispenser refunds</h2>
<h3>5.1 Purchased dispensers</h3>
<p>A purchased dispenser found to be faulty on installation or within seven days of installation is replaced free of charge. After that period, the manufacturer warranty applies and we will arrange service under it. Purchased dispensers are not returnable for a refund once they have been installed and used, unless the unit is found to be defective.</p>
<h3>5.2 Rented dispensers</h3>
<p>Monthly rental is charged in advance and is not refundable for a part month. The rental security deposit is refunded within seven working days of the unit being collected, subject to the deductions set out in the damage policy.</p>

<h2>6. Custom label orders</h2>
<p>Custom printed labels are made to your specification and carry your branding, so they cannot be resold or reused. For that reason:</p>
<ul>
<li>Before you approve the digital proof, you may cancel at any time and receive a full refund of anything paid.</li>
<li>After you approve the proof, the order cannot be cancelled or refunded, because printing begins immediately.</li>
<li>If we print something different from the proof you approved, we reprint the entire order at our cost. This is our error and you pay nothing.</li>
</ul>

<h2>7. What is not refundable</h2>
<ul>
<li>Water that has been consumed, decanted or stored incorrectly by the customer</li>
<li>Product reported more than twenty four hours after delivery, where the cause cannot be traced to our plant</li>
<li>Delivery attempts that failed because of an incorrect address or an unavailable recipient after repeated attempts</li>
<li>Approved custom label orders already in production</li>
<li>Part months of dispenser rental</li>
</ul>

<h2>8. Refund methods</h2>
<p>Refunds are issued by the same method used for payment wherever possible: cash for cash on delivery, EasyPaisa to EasyPaisa, JazzCash to JazzCash, and bank transfer to the originating account. We do not issue refunds to third party accounts.</p>

<h2>9. If you are not satisfied with a refund decision</h2>
<p>Escalate it. Our <a href="/dispute-resolution">dispute resolution process</a> sets out exactly who to contact at each stage and the timeline for a response. Every escalated case is reviewed by management with the production record for the batch in question.</p>

<h2>10. Contact</h2>
<p>WhatsApp 0333 5592206 or 0332 2901309, or email info@pakeverests.site with the word REFUND in the subject line.</p>',
'legal',1,'Refund Policy | Bottle Deposit and Product Refunds | Pak-Everests',
'How Pak-Everests refunds the Rs 1,500 bottle security deposit, replaces faulty product, and handles dispenser and custom label refunds.',
'refund policy, water bottle deposit refund, Pak-Everests refund',3,'published',NOW()),

-- --------------------------------------------------------------------------
('delivery-policy','Delivery Policy','Coverage, timing, free delivery and how deliveries are handled',
'<h2>1. Free delivery</h2>
<p>Delivery is <strong>free of charge across our entire published coverage area</strong>, with no minimum order value and no minimum bottle count. We do not add a fuel surcharge, a small order fee or a stair carrying fee. The price you see on the product page is the price you pay at the door.</p>
<p>The one exception is facility filling at the plant, which is a collection service by design and is priced at Rs 6 per litre without delivery.</p>

<h2>2. Coverage area</h2>
<p>We deliver to the following areas:</p>
<ul>
<li><strong>Daily routes:</strong> Gujar Khan, Mandra, Habib Chowk, Rawat, Adiala Road, Bahria Town Rawalpindi, DHA Islamabad, Rawalpindi city and Cantt</li>
<li><strong>Alternate day routes:</strong> Daultala, Bewal, Kallar Syedan</li>
<li><strong>Wider region:</strong> Other localities across the Rawalpindi region are served on scheduled runs. Send your address on WhatsApp and we will confirm the schedule for it.</li>
</ul>
<p>If you are just outside a listed area, ask us anyway. Our routes extend regularly and we will tell you honestly whether we can serve you reliably rather than promising and failing.</p>

<h2>3. Delivery timing</h2>
<h3>3.1 Same day delivery</h3>
<p>Orders placed <strong>before 4:00 PM</strong> for an area on that day route are normally delivered the same day. Orders placed after the cut off move to the next scheduled run for that area.</p>
<h3>3.2 Delivery hours</h3>
<p>Deliveries run from <strong>8:00 AM to 9:00 PM, Monday to Sunday</strong>, including public holidays except where stated otherwise on the website news ticker.</p>
<h3>3.3 Preferred time slots</h3>
<p>Tell us your preferred window in the delivery note field of the order form and we will do our best to route accordingly. Time slots are an aim, not a guarantee, because route timing depends on traffic and on the deliveries ahead of yours.</p>
<h3>3.4 Standing orders</h3>
<p>Offices, schools, hospitals and shops on a standing order are assigned a fixed day and approximate time each week or fortnight. Standing order customers get route priority over one off orders.</p>

<h2>4. Order confirmation</h2>
<p>Every order is confirmed by phone or WhatsApp before dispatch. If we cannot reach you on the number provided after two attempts, the order is held rather than dispatched, because sending a vehicle to an unconfirmed address wastes a delivery slot that another customer needs.</p>

<h2>5. At the point of delivery</h2>
<h3>5.1 Please inspect before accepting</h3>
<p>Check the seal, the cap, the batch code and the filling date before accepting the bottle. Our delivery staff are instructed to wait while you do this. Report any concern immediately rather than after the vehicle has left.</p>
<h3>5.2 Bottle exchange</h3>
<p>On refill deliveries, hand over the empty bottle at the door. The delivery team records the exchange so your deposit count stays accurate.</p>
<h3>5.3 Payment on delivery</h3>
<p>Cash on delivery is settled at the door and a receipt is issued. Please keep the receipt, particularly the first one, because it records your bottle deposit.</p>
<h3>5.4 Carrying bottles inside</h3>
<p>Our team will carry bottles into your kitchen, office or dispenser location at no charge. For upper floors without a lift our team will still carry, but please tell us when ordering so the route allows the extra time.</p>

<h2>6. Failed deliveries</h2>
<p>A delivery is recorded as failed when nobody is available to receive it at the confirmed address, when the address cannot be located from the details given, or when access is refused.</p>
<ul>
<li>The <strong>first failed attempt</strong> is rescheduled free of charge.</li>
<li>A <strong>second failed attempt</strong> on the same order is also rescheduled free of charge, and we will call to agree a workable time.</li>
<li>After a <strong>third failed attempt</strong> the order is cancelled. Any advance payment is refunded in full. A delivery charge may apply to future orders to that address.</li>
</ul>

<h2>7. Delays and force majeure</h2>
<p>Deliveries may be delayed by circumstances outside our control, including road closures, political processions, security restrictions, severe weather, flooding, fuel shortages, strikes and vehicle breakdown. Where a delay is expected we will inform affected customers by WhatsApp as early as we can. We are not liable for loss arising from a delay caused by such events, but we will always prioritise catching up on affected routes before taking new orders.</p>

<h2>8. Bulk, corporate and institutional deliveries</h2>
<p>Large volume deliveries to schools, hospitals, factories, marts and event venues are scheduled in advance with a named contact at your end. For events we recommend confirming at least forty eight hours ahead so stock and vehicles can be reserved. Loading and unloading arrangements at your premises should be confirmed at the time of booking.</p>

<h2>9. Storage advice after delivery</h2>
<p>Once a bottle is delivered, storage is in your hands and it genuinely matters:</p>
<ul>
<li>Keep bottles out of direct sunlight and away from heaters and cooking areas</li>
<li>Never store bottles near detergents, paint, fuel, pesticide or any strong smelling chemical, because plastic transmits odour over time</li>
<li>Store on a clean, dry, flat surface, and do not stack 19 litre bottles more than two high</li>
<li>Wipe the neck and cap before placing a bottle on a dispenser</li>
<li>Use an opened bottle within three to four days for the best taste</li>
</ul>

<h2>10. Contact</h2>
<p>For anything related to a delivery, WhatsApp 0333 5592206 or 0332 2901309, or email info@pakeverests.site.</p>',
'legal',1,'Delivery Policy | Free Water Delivery Areas and Timing | Pak-Everests',
'Pak-Everests delivery policy covering free delivery areas, same day cut off times, failed deliveries, bulk deliveries and storage advice.',
'delivery policy, free water delivery, water delivery Rawalpindi, delivery areas',4,'published',NOW()),

-- --------------------------------------------------------------------------
('damage-policy','Bottle, Dispenser and Equipment Damage Policy','What counts as fair wear, what is chargeable, and how assessments are made',
'<h2>1. Purpose of this policy</h2>
<p>Bottles, dispensers, crates and delivery vehicles are the working assets of a water supply business. This policy sets out clearly, and in advance, what happens when one of them is damaged or lost while in a customer or distributor possession. Publishing it openly means nobody is surprised by a deduction, and everyone knows exactly where they stand.</p>

<h2>2. Ownership</h2>
<p>Returnable 19 litre bottles, rented dispensers, crates, pallets and delivery vehicles remain the <strong>property of Pak-Everests at all times</strong>. A security deposit gives you the right to use a bottle. It does not transfer ownership of it. The same applies to a rented dispenser.</p>

<h2>3. Fair wear and tear, which is never charged</h2>
<p>We expect assets in daily use to show it. The following are treated as normal and are <strong>never</strong> deducted from a deposit:</p>
<ul>
<li>Surface scratches and scuff marks on a bottle</li>
<li>Faded or partly worn printing and labels</li>
<li>Minor discolouration from age and repeated washing</li>
<li>Ordinary wear on a dispenser tap, drip tray or body panel</li>
<li>Loss of gloss on the bottle surface</li>
</ul>

<h2>4. Bottle damage schedule</h2>
<table>
<thead><tr><th>Condition of the 19 litre bottle</th><th>Deduction from the Rs 1,500 deposit</th></tr></thead>
<tbody>
<tr><td>Fair wear and tear as described above</td><td>Nil</td></tr>
<tr><td>Cap or handle missing or broken</td><td>Rs 100</td></tr>
<tr><td>Deep gouging or cuts that prevent proper sanitisation</td><td>Rs 500</td></tr>
<tr><td>Deformation caused by heat, direct sunlight or hot water</td><td>Rs 750</td></tr>
<tr><td>Persistent odour from storing chemicals, fuel, paint or pesticide in the bottle</td><td>Rs 1,500 (full deposit)</td></tr>
<tr><td>Cracked, punctured or leaking bottle body</td><td>Rs 1,500 (full deposit)</td></tr>
<tr><td>Bottle lost, stolen, sold or not returned</td><td>Rs 1,500 (full deposit)</td></tr>
</tbody>
</table>
<p>Contaminated bottles are the one category we are strict about. A bottle that has held kerosene, paint thinner or pesticide cannot be brought back into a food grade fleet by any washing process, and it is destroyed rather than reissued.</p>

<h2>5. Dispenser damage schedule</h2>
<h3>5.1 Rented dispensers</h3>
<p>A rental security deposit is collected at installation and stated in the dispenser agreement. Deductions from it are assessed as follows:</p>
<table>
<thead><tr><th>Condition</th><th>Treatment</th></tr></thead>
<tbody>
<tr><td>Normal wear, and any fault in the cooling or heating system arising during normal use</td><td>Repaired or replaced by us at no cost to you</td></tr>
<tr><td>Broken or missing tap, drip tray or cover</td><td>Charged at the cost of the part plus fitting</td></tr>
<tr><td>Damage from power surge where no stabiliser was used</td><td>Charged at repair cost</td></tr>
<tr><td>Damage from unauthorised repair or opening by a third party</td><td>Charged at repair cost, and the warranty is void</td></tr>
<tr><td>Water damage from running the unit dry or filling it from an unapproved source</td><td>Charged at repair cost</td></tr>
<tr><td>Physical damage, burning, or damage from fire, flood or transport by the customer</td><td>Charged at repair cost or replacement value</td></tr>
<tr><td>Unit lost, stolen, sold or not returned</td><td>Full replacement value of Rs 42,000</td></tr>
</tbody>
</table>
<h3>5.2 Purchased dispensers</h3>
<p>A purchased dispenser belongs to you. Manufacturing defects are covered by the manufacturer warranty for the warranty period. Damage from misuse, power surge, unauthorised repair or physical impact is not covered, and repairs are chargeable at cost.</p>
<h3>5.3 Required care for all dispensers</h3>
<ul>
<li>Use a voltage stabiliser. Power fluctuation is the single most common cause of dispenser failure in our service area.</li>
<li>Never run the hot tank dry. Switch off the hot function when a bottle is empty.</li>
<li>Do not open, modify or repair the unit yourself or through an outside technician.</li>
<li>Keep the unit out of direct sunlight, away from heat sources and on a level floor.</li>
<li>Allow our team to carry out the quarterly sanitisation on rented units.</li>
</ul>

<h2>6. Crates, pallets and other property</h2>
<p>Crates and pallets issued to shops, distributors and event customers remain our property and are collected on the next visit. Crates that are lost or broken beyond use are charged at replacement cost, which is stated on the delivery challan at the time of issue.</p>

<h2>7. Vehicles and third party property</h2>
<h3>7.1 Damage caused by our vehicle at your premises</h3>
<p>If a Pak-Everests vehicle or a member of our delivery team causes damage to your gate, wall, vehicle or other property, report it immediately with photographs. We investigate every such report and, where our team is at fault, we repair the damage or compensate the loss. We do not deflect responsibility onto individual staff members.</p>
<h3>7.2 Damage caused to our vehicle at your premises</h3>
<p>Where a customer or distributor causes damage to our vehicle or equipment on their premises, the repair cost is recoverable.</p>
<h3>7.3 Distributor held vehicles and assets</h3>
<p>Where a vehicle, freezer, cooler, rack, signage or branded equipment is issued to an appointed distributor, the distributor is responsible for its custody, insurance where applicable, and its return in working condition. Terms are set out in the distributor agreement and the security deposit held under that agreement covers loss and damage.</p>

<h2>8. How damage is assessed</h2>
<ol>
<li><strong>Inspection at collection.</strong> The delivery team inspects the item at the door in your presence, not later at the plant behind closed doors.</li>
<li><strong>Photographic record.</strong> Any item proposed for a deduction is photographed at the point of collection, with the customer name and date.</li>
<li><strong>Written assessment.</strong> You receive the proposed deduction in writing on WhatsApp or on the collection receipt, with the reason and the schedule item it falls under.</li>
<li><strong>Your response.</strong> You have seven days to accept or dispute the assessment.</li>
<li><strong>Settlement.</strong> Once accepted, the balance of your deposit is refunded within seven working days.</li>
</ol>

<h2>9. Disputing an assessment</h2>
<p>If you believe an assessment is wrong, say so. Send your objection with any photographs of your own to WhatsApp 0333 5592206 or email info@pakeverests.site within seven days. Management reviews the item, the collection photographs and the customer history, and issues a written decision within seven working days. If you remain dissatisfied, the matter proceeds under our <a href="/dispute-resolution">dispute resolution process</a>. Deposits are never adjusted while a dispute is open.</p>

<h2>10. Insurance and liability limit</h2>
<p>Our liability under this policy is limited to the repair or replacement value of the asset concerned. We are not liable for indirect or consequential loss. Customers are advised to keep bottles and dispensers within their own property insurance where they hold such cover.</p>

<h2>11. Contact</h2>
<p>WhatsApp 0333 5592206 or 0332 2901309, or email info@pakeverests.site with the word DAMAGE in the subject line.</p>',
'legal',1,'Bottle and Dispenser Damage Policy | Pak-Everests Water',
'What counts as fair wear, the exact deduction schedule for damaged or lost 19 litre bottles and dispensers, and how damage assessments can be disputed.',
'damage policy, bottle damage charges, dispenser damage, water bottle deposit deduction',5,'published',NOW()),

-- --------------------------------------------------------------------------
('dispute-resolution','Dispute Resolution','How complaints and disputes are escalated, reviewed and settled',
'<h2>1. Our approach</h2>
<p>Most disputes in this business come down to one of three things: a delivery that did not arrive as expected, a deposit deduction the customer disagrees with, or an account balance that does not match. All three are resolvable with records, and we keep records specifically so they can be resolved rather than argued about.</p>
<p>This page sets out exactly who to contact, what happens at each stage, and how long each stage takes.</p>

<h2>2. Who this process covers</h2>
<p>This process is available to household customers, corporate and institutional customers, appointed distributors, custom label clients, and job applicants who wish to raise a concern.</p>

<h2>3. Stage one - Direct resolution</h2>
<p><strong>Timeline: response within 24 hours, resolution target 3 working days.</strong></p>
<p>Contact our customer support team first:</p>
<ul>
<li>WhatsApp <strong>0333 5592206</strong> or <strong>0332 2901309</strong></li>
<li>Email <strong>info@pakeverests.site</strong></li>
<li>Or use the contact form on this website</li>
</ul>
<p>Please include your name, delivery address, phone number, order reference if you have one, and the batch code and filling date printed on the bottle where the issue relates to product quality. A photograph resolves most product complaints on the same day, because it lets us pull the production record for that exact batch.</p>
<p>The large majority of issues end here, usually with a replacement or a corrected account entry.</p>

<h2>4. Stage two - Management review</h2>
<p><strong>Timeline: acknowledgement within 2 working days, written decision within 7 working days.</strong></p>
<p>If stage one does not resolve the matter, ask for it to be escalated to management review. Send your escalation to <strong>info@pakeverests.site</strong> with the subject line ESCALATION and include:</p>
<ul>
<li>A summary of the complaint and what has happened so far</li>
<li>Dates, order references and the names of anyone you spoke to</li>
<li>Any photographs, receipts or messages that support your position</li>
<li>The outcome you are asking for</li>
</ul>
<p>Management reviews the file independently of the staff member who handled stage one, examines the delivery log, the production batch record and any collection photographs, and issues a written decision with reasons. If we were wrong, we say so plainly and put it right.</p>

<h2>5. Stage three - Independent mediation</h2>
<p><strong>Timeline: 30 days from the date mediation is agreed.</strong></p>
<p>If the written decision does not settle the matter, either side may propose mediation by a neutral third party acceptable to both. This may be a respected local trade body member, a chamber of commerce representative, or a mutually agreed independent professional. Each side bears its own costs and the mediator fee is shared equally.</p>
<p>Mediation is not binding. Its purpose is to reach a practical settlement without the cost and delay of formal proceedings, and in practice it usually does.</p>

<h2>6. Stage four - Arbitration</h2>
<p>Where mediation does not produce a settlement, the dispute is referred to arbitration by a sole arbitrator under the <strong>Arbitration Act 1940</strong> of Pakistan.</p>
<ul>
<li>The arbitrator is appointed by agreement between the parties. Failing agreement within fifteen days, either party may apply to the competent court at Rawalpindi for an appointment.</li>
<li>The seat of arbitration is Gujar Khan or Rawalpindi, District Rawalpindi, Punjab.</li>
<li>Proceedings are conducted in English or Urdu as the parties agree.</li>
<li>The arbitration award is final and binding on both parties.</li>
<li>Costs are borne as directed in the award.</li>
</ul>

<h2>7. Jurisdiction</h2>
<p>Nothing in this process prevents either party from approaching the competent courts at Gujar Khan or Rawalpindi, Punjab, Pakistan, which have exclusive jurisdiction over any matter arising from our contracts. Consumers retain every right available to them under the <strong>Punjab Consumer Protection Act 2005</strong> and may approach the relevant Consumer Court, and may report food safety concerns directly to the <strong>Punjab Food Authority</strong> at any time. We will never ask a customer to waive those rights.</p>

<h2>8. Distributor disputes</h2>
<p>Disputes with appointed distributors regarding territory, pricing, credit limits, security deposits, stock returns or termination follow the same four stage process, subject to any specific mechanism in the signed distributor agreement, which takes precedence where the two differ.</p>

<h2>9. What we will not do</h2>
<ul>
<li>We will not suspend an active supply while a dispute is genuinely under review, unless there is a food safety reason to do so.</li>
<li>We will not adjust or withhold a security deposit while a dispute over it is open.</li>
<li>We will not require a customer to waive statutory consumer rights as a condition of settlement.</li>
</ul>

<h2>10. Record keeping</h2>
<p>Every complaint is logged with a reference number, the date received, the stage reached and the outcome. Records are retained for three years. Trends are reviewed by management each quarter, because a complaint that repeats is a process problem, not a customer problem.</p>

<h2>11. Contact for disputes</h2>
<p><strong>Pak-Everests Bottled Drinking Water</strong><br>
Main G.T. Road, Gujar Khan, District Rawalpindi, Punjab, Pakistan<br>
Email: info@pakeverests.site<br>
WhatsApp: 0333 5592206 and 0332 2901309</p>',
'legal',1,'Dispute Resolution Process | Pak-Everests Bottled Drinking Water',
'The four stage dispute resolution process at Pak-Everests, covering direct resolution, management review, mediation and arbitration under Pakistani law.',
'dispute resolution, complaint process, consumer rights Pakistan, arbitration',6,'published',NOW()),

-- --------------------------------------------------------------------------
('distributor-terms','Distributor Terms and Conditions','The commercial terms that apply to appointed Pak-Everests distributors',
'<h2>1. Scope</h2>
<p>These terms apply to every party appointed as an area distributor of Pak-Everests Bottled Drinking Water. They are supplemented by the individual signed distributor agreement, which prevails where the two differ.</p>

<h2>2. Appointment and territory</h2>
<h3>2.1 Exclusive territory</h3>
<p>Each distributor is appointed for a defined geographical territory described in the agreement. Within that territory the distributor is our sole appointed distributor for the listed product range during the term of the agreement, provided targets are met.</p>
<h3>2.2 Territory discipline</h3>
<p>A distributor may not sell, supply or solicit customers inside the territory of another appointed distributor. Territory encroachment is the most common cause of distributor disputes and is treated as a material breach.</p>
<h3>2.3 Direct accounts</h3>
<p>Pak-Everests reserves the right to serve national corporate accounts, government institutions and existing direct customers within any territory. Such accounts are identified in the agreement schedule at the time of appointment.</p>

<h2>3. Term and renewal</h2>
<p>The initial term is <strong>twelve months</strong> from the date of appointment, renewable annually by written agreement subject to performance review. Either party may terminate with <strong>thirty days written notice</strong>. Pak-Everests may terminate immediately for a material breach, including territory encroachment, non payment, adulteration or tampering with product, or any act that damages the brand.</p>

<h2>4. Security deposit</h2>
<p>A refundable security deposit is required at appointment. The amount is set according to territory size, credit limit and the value of assets issued, and is stated in the agreement. The deposit secures stock on credit, returnable bottles, crates and any equipment or vehicle issued. It is refunded within <strong>thirty days</strong> of termination, after reconciliation of all accounts, stock, bottles and assets.</p>

<h2>5. Pricing and margins</h2>
<ul>
<li>Distributor pricing is set out in the price schedule attached to the agreement and is confidential.</li>
<li>Distributors must observe the maximum retail price published by Pak-Everests. Selling above the published retail price is a breach, because it damages the brand and the customer relationship in the territory.</li>
<li>Pak-Everests may revise pricing with <strong>fifteen days written notice</strong>. Stock already purchased at the old price is not repriced.</li>
<li>Volume rebates and seasonal incentives, where offered, are stated in writing and paid on verified sell out volume.</li>
</ul>

<h2>6. Minimum order and targets</h2>
<p>Each agreement states a minimum order quantity per lifting and a monthly volume target. Targets are set in consultation with the distributor and reviewed quarterly. Sustained failure to meet target over two consecutive quarters may lead to territory reduction or termination, following a written performance discussion.</p>

<h2>7. Payment terms</h2>
<ul>
<li>Opening orders are supplied against advance payment.</li>
<li>A credit limit may be extended after a satisfactory trading history of not less than three months.</li>
<li>Credit invoices are payable within the period stated on the invoice. Supply is suspended on any account exceeding its limit or overdue period.</li>
<li>Payments are accepted by cash, EasyPaisa, JazzCash or bank transfer to the company account only. Payments must never be made to an individual staff account.</li>
</ul>

<h2>8. Bottles, crates and equipment</h2>
<p>Returnable bottles, crates, pallets, racks, coolers, signage and any vehicle issued remain the property of Pak-Everests. The distributor is responsible for their custody, safe use and return in working condition. Loss and damage are governed by our <a href="/damage-policy">damage policy</a> and recoverable against the security deposit.</p>

<h2>9. Storage, handling and food safety</h2>
<p>The distributor undertakes to:</p>
<ul>
<li>Store stock in a clean, dry, covered warehouse away from direct sunlight, heat and any chemical, fuel, paint or pesticide</li>
<li>Observe stock rotation on a first in first out basis and never supply stock past its printed shelf life</li>
<li>Transport product in clean, covered vehicles used only for food grade goods</li>
<li>Never open, decant, refill, relabel, dilute or tamper with any Pak-Everests product under any circumstance</li>
<li>Permit inspection of storage premises and vehicles by Pak-Everests or by the Punjab Food Authority on reasonable notice</li>
<li>Hold any trade licence or registration required by local law for the distribution business</li>
</ul>
<p>Tampering with product is an immediate termination event and will be reported to the Punjab Food Authority.</p>

<h2>10. Branding and marketing</h2>
<p>The distributor may use the Pak-Everests name and logo only for the purpose of distributing our product, and only in the form supplied by us. Marketing material, signage, banners and vehicle branding require written approval before use. The distributor may not register any domain name, social media handle or trademark containing the Pak-Everests name.</p>

<h2>11. Reporting</h2>
<p>Distributors submit a monthly report covering sell out volume by product, active retail outlets served, bottle stock on hand, and market feedback. Reports may be submitted through the distributor portal, WhatsApp or email.</p>

<h2>12. Support from Pak-Everests</h2>
<p>In return, we commit to:</p>
<ul>
<li>Reliable supply against confirmed orders, with priority over one off customers</li>
<li>A named account manager as a single point of contact</li>
<li>Point of sale material, banners and branding support</li>
<li>Product training for the distributor delivery team</li>
<li>Territory protection against other appointed distributors</li>
<li>Prompt replacement of any product found defective at our end</li>
</ul>

<h2>13. Confidentiality</h2>
<p>Pricing schedules, customer lists, margin structures and any commercial information shared during the relationship are confidential and remain so for two years after termination.</p>

<h2>14. Termination and settlement</h2>
<p>On termination for any reason:</p>
<ol>
<li>All outstanding invoices become immediately payable.</li>
<li>Unsold stock in saleable condition and within shelf life may be returned for credit at the price paid, subject to inspection.</li>
<li>All bottles, crates, equipment, signage and branded material are returned.</li>
<li>Use of the Pak-Everests name and logo ceases immediately.</li>
<li>The security deposit is reconciled and the balance refunded within thirty days.</li>
</ol>

<h2>15. Disputes</h2>
<p>Distributor disputes follow the four stage process on our <a href="/dispute-resolution">dispute resolution page</a>, subject to any specific mechanism in the signed agreement. Governing law is the law of Pakistan and the courts at Gujar Khan and Rawalpindi have exclusive jurisdiction.</p>

<h2>16. Apply</h2>
<p>To apply for a territory, complete the <a href="/distributor-application">distributor application form</a> or WhatsApp 0332 2901309.</p>',
'legal',1,'Distributor Terms and Conditions | Pak-Everests Water Distribution',
'Territory, pricing, security deposit, targets, payment terms, food safety and termination conditions for appointed Pak-Everests water distributors.',
'distributor terms, water distribution agreement, become a distributor Pakistan',7,'published',NOW()),

-- --------------------------------------------------------------------------
('cookie-policy','Cookie Policy','The cookies this website uses and how to control them',
'<h2>1. What cookies are</h2>
<p>A cookie is a small text file that a website stores on your device. It lets the site remember things between page loads, such as whether you chose dark mode or whether your form security token is still valid.</p>

<h2>2. Cookies we set ourselves</h2>
<table>
<thead><tr><th>Cookie</th><th>Purpose</th><th>Duration</th></tr></thead>
<tbody>
<tr><td>PEV_SESSID</td><td>Strictly necessary. Maintains your session so forms, security tokens and the admin login work.</td><td>Until the browser closes</td></tr>
<tr><td>pe_theme</td><td>Preference. Remembers whether you selected light or dark mode.</td><td>1 year</td></tr>
<tr><td>Analytics session identifier</td><td>First party analytics. Counts a visit once rather than on every page, so visitor numbers are accurate.</td><td>Until the browser closes</td></tr>
</tbody>
</table>
<p>None of these cookies track you across other websites, and none are shared with advertisers.</p>

<h2>3. Third party cookies</h2>
<p>Where the site owner has enabled them, the following third party services may set cookies under their own policies:</p>
<ul>
<li><strong>Google Analytics</strong> for aggregate traffic reporting</li>
<li><strong>Google Search Console</strong> verification, which sets no cookie by itself</li>
<li><strong>Meta Pixel</strong> for advertising measurement, where a Facebook or Instagram campaign is running</li>
<li><strong>Google AdSense, Adsterra or Monetag</strong> where advertising is enabled on the site</li>
<li><strong>Google Maps</strong> when the embedded map on the contact page loads</li>
</ul>

<h2>4. Controlling cookies</h2>
<p>Every major browser lets you view, block and delete cookies from its settings menu. You can also browse in a private or incognito window, which discards cookies when you close it. Blocking the strictly necessary cookie will prevent forms and the admin panel from working correctly.</p>

<h2>5. Do Not Track</h2>
<p>Our first party analytics respects a Do Not Track signal where your browser sends one. Third party services follow their own policies on this.</p>

<h2>6. Changes</h2>
<p>If we add or remove cookies, this page is updated. It was last reviewed on the date shown at the foot of this page.</p>

<h2>7. Contact</h2>
<p>Email info@pakeverests.site with any question about cookies or tracking on this website.</p>',
'legal',1,'Cookie Policy | Pak-Everests Bottled Drinking Water',
'The cookies used on pakeverests.site, what each one does, how long it lasts and how to control or block them.',
'cookie policy, cookies, tracking, privacy',8,'published',NOW()),

-- --------------------------------------------------------------------------
('disclaimer','Disclaimer','The limits of the information published on this website',
'<h2>1. General information only</h2>
<p>The content on pakeverests.site is published for general information about our products and services. While we take care to keep it accurate and current, we make no warranty that every detail is complete or free of error at every moment.</p>

<h2>2. Not medical advice</h2>
<p>Pages describing minerals, hydration and the health benefits of drinking water are general educational content. <strong>They are not medical advice.</strong> Mineral requirements vary between individuals, and anyone with a kidney condition, a heart condition, high blood pressure, a sodium or potassium restricted diet, or any other medical condition should follow the guidance of a qualified physician rather than anything written here. Mineral values quoted are typical target ranges, not a prescription.</p>

<h2>3. Product specifications</h2>
<p>Mineral values, pH, total dissolved solids and shelf life figures are typical values from routine batch testing. Natural variation occurs within the published ranges. Current laboratory reports are available on our documents page and on request.</p>

<h2>4. Prices and availability</h2>
<p>Prices, pack sizes, coverage areas and delivery schedules published on this site may change without notice. The price that applies to your order is the one confirmed when we accept the order. Products marked coming soon are not yet available and no delivery date is guaranteed for them.</p>

<h2>5. Images</h2>
<p>Product photographs, plant photographs and gallery images are representative. Packaging, labels and bottle appearance may differ from the images shown as designs are updated.</p>

<h2>6. External links</h2>
<p>This website may link to other websites, including social media platforms and mapping services. We do not control those sites and are not responsible for their content, accuracy or privacy practices. A link is not an endorsement.</p>

<h2>7. Reviews and testimonials</h2>
<p>Customer reviews published on this site reflect the personal experience of the individual reviewer. They are not a guarantee that every customer will have the same experience. Reviews are moderated before publication to remove spam and abuse, and we do not edit the substance of a genuine review to make it more favourable.</p>

<h2>8. Website availability</h2>
<p>We do not warrant that this website will be available without interruption or free of technical error. Access may be suspended for maintenance, upgrades or reasons beyond our control.</p>

<h2>9. Limitation of liability</h2>
<p>To the maximum extent permitted by law, Pak-Everests is not liable for any loss arising from reliance on information published on this website. Our liability in connection with any product supplied is governed by our terms and conditions.</p>

<h2>10. Contact</h2>
<p>To report an inaccuracy on this website, email info@pakeverests.site and we will correct it.</p>',
'legal',1,'Disclaimer | Pak-Everests Bottled Drinking Water',
'The limits of information published on pakeverests.site, including health content, product specifications, pricing and third party links.',
'disclaimer, legal notice, website terms',9,'published',NOW()),

-- --------------------------------------------------------------------------
('quality-assurance-policy','Quality Assurance Policy','How Pak-Everests controls quality from the bore to the sealed bottle',
'<h2>1. Our quality commitment</h2>
<p>Pak-Everests exists to supply drinking water that is safe, consistent and pleasant to drink, every single day, to every customer. That is not achieved by intention. It is achieved by a documented process, measured at every stage, with records that can be checked afterwards.</p>

<h2>2. Regulatory framework</h2>
<p>We operate as a bottled drinking water establishment approved by the <strong>Punjab Food Authority</strong> and follow the drinking water requirements set by the <strong>Pakistan Standards and Quality Control Authority</strong>, together with World Health Organization drinking water quality guidance where it is stricter. Our licence and certification documents are published on the documents page of this website.</p>

<h2>3. Source control</h2>
<p>Raw water is drawn from a protected deep bore inside the plant boundary. The bore head is sealed and raised, the surrounding area is kept clear of drainage and animal access, and raw water is sampled and logged before every production run for turbidity, pH, total dissolved solids and odour.</p>

<h2>4. Process control</h2>
<p>Every batch passes through the full eight stage purification process. At each stage a defined parameter is measured and recorded:</p>
<ul>
<li>Turbidity before and after media filtration</li>
<li>Free chlorine after carbon filtration, which must read zero before water reaches the membranes</li>
<li>Hardness after softening</li>
<li>Pressure differential across the micron cartridge bank</li>
<li>Feed pressure, permeate flow, reject flow and TDS on both sides of the reverse osmosis membranes</li>
<li>Calcium, magnesium, potassium, sodium, alkalinity and final TDS after re-mineralisation</li>
<li>Ultraviolet lamp hours and ozone concentration at the point of filling</li>
</ul>
<p>Any reading outside its control limit stops the line. Production does not resume until the cause is corrected and the parameter is back in range.</p>

<h2>5. Laboratory testing</h2>
<h3>5.1 In-house testing on every batch</h3>
<p>pH, total dissolved solids, turbidity, taste and odour are tested in our in-house laboratory for every production batch before release.</p>
<h3>5.2 Microbiological testing</h3>
<p>Batches are tested for total plate count, total coliforms, E. coli and Pseudomonas aeruginosa. The requirement is absence of coliforms and E. coli in every sample. Product is not released until microbiological clearance is obtained.</p>
<h3>5.3 Independent laboratory testing</h3>
<p>A full physical, chemical and microbiological analysis is carried out periodically by an independent accredited laboratory, including heavy metals. The most recent report is published on our documents page.</p>

<h2>6. Bottle and packaging hygiene</h2>
<h3>6.1 Returnable 19 litre bottles</h3>
<p>Every returned bottle is inspected under light, sniff checked for odour, pre-rinsed, washed internally with food grade caustic solution, rinsed with treated water and sanitised before it reaches the filling head. Bottles showing cracks, deep scratches, deformation, staining or any odour are permanently retired and destroyed, not reissued.</p>
<h3>6.2 PET bottles</h3>
<p>Preforms are stored covered and clean, blown, air rinsed and filled in a single enclosed sequence to prevent any handling between forming and filling.</p>
<h3>6.3 Closures</h3>
<p>Caps are single use, tamper evident, and stored in sealed bags until the moment of use.</p>

<h2>7. Traceability</h2>
<p>Every bottle carries a batch number and filling date. From that code we can identify the production date and shift, the raw water log, every process parameter recorded that day, the laboratory results for that batch, and the delivery route it went out on. This is why we ask for a photograph of the code when a quality concern is reported: it makes the investigation exact rather than approximate.</p>

<h2>8. Plant hygiene and personnel</h2>
<ul>
<li>The filling room is enclosed and maintained under positive pressure</li>
<li>Production staff wear clean uniforms, head covers, masks and gloves in the filling area</li>
<li>Medical fitness certificates are maintained for food handling staff</li>
<li>Cleaning and sanitisation schedules for tanks, lines and the filling room are documented and signed off</li>
<li>Pest control is carried out on a fixed schedule with records maintained</li>
</ul>

<h2>9. Complaint handling as a quality input</h2>
<p>Every quality complaint is logged, traced to its batch, and investigated against the production record for that batch. Complaints are reviewed by management each quarter to identify patterns. A complaint that repeats is treated as a process failure to be corrected, not as an isolated customer issue.</p>

<h2>10. Continuous improvement</h2>
<p>Filter media, membranes, cartridges and ultraviolet lamps are replaced on scheduled service life rather than on visible failure. Equipment performance trends are reviewed monthly. Staff receive refresher training on hygiene and process control.</p>

<h2>11. Contact our quality team</h2>
<p>Quality questions, test report requests and plant visit requests may be sent to info@pakeverests.site or WhatsApp 0333 5592206.</p>',
'legal',0,'Quality Assurance Policy | Pak-Everests Mineral Water Plant',
'How we control drinking water quality: source protection, process parameters, laboratory testing, bottle hygiene and full batch traceability.',
'water quality policy, quality assurance, water testing, Punjab Food Authority approved water',10,'published',NOW());
