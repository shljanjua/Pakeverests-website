-- ==========================================================================
--  Pak-Everests Bottled Drinking Water — Seed data
--  Import AFTER sql/01-schema.sql
--
--  DEFAULT ADMIN LOGIN  ->  https://pakeverests.site/admin
--      Username : admin
--      Password : PakEverests@2026
--  CHANGE THIS PASSWORD IMMEDIATELY under Admin -> Users.
-- ==========================================================================

SET NAMES utf8mb4;

-- --------------------------------------------------------------------------
--  Admin user
-- --------------------------------------------------------------------------
INSERT INTO `admin_users` (`name`,`email`,`username`,`password_hash`,`role`,`is_active`,`created_at`) VALUES
('Site Administrator','info@pakeverests.site','admin','$2y$12$L2xM6ypA4BXN/dGvK38JSu8BENB6fT/RaxgXNyDttuQforxnxolJO','super_admin',1,NOW());

-- --------------------------------------------------------------------------
--  Settings
-- --------------------------------------------------------------------------
INSERT INTO `settings` (`setting_key`,`setting_value`,`setting_group`,`updated_at`) VALUES
-- General ------------------------------------------------------------------
('site_name','Pak-Everests Bottled Drinking Water','general',NOW()),
('brand_suffix','Pak-Everests','general',NOW()),
('site_tagline','Pure Mineral Water from the Heart of Potohar','general',NOW()),
('logo_path','/assets/img/logo.webp','general',NOW()),
('logo_dark_path','/assets/img/logo-dark.webp','general',NOW()),
('favicon_path','/assets/img/favicon.png','general',NOW()),
('og_image','/assets/img/og-default.jpg','general',NOW()),
('contact_email','info@pakeverests.site','general',NOW()),
('sales_email','info@pakeverests.site','general',NOW()),
('order_notify_email','info@pakeverests.site','general',NOW()),
('phone_display','0333 5592206','general',NOW()),
('address_street','Main G.T. Road, Gujar Khan','general',NOW()),
('address_city','Gujar Khan','general',NOW()),
('address_region','Punjab','general',NOW()),
('address_postal','47850','general',NOW()),
('address_full','Pak-Everests Water Plant, Main G.T. Road, Gujar Khan, District Rawalpindi, Punjab 47850, Pakistan','general',NOW()),
('hours_open','08:00','general',NOW()),
('hours_close','21:00','general',NOW()),
('hours_display','Monday to Sunday, 8:00 AM to 9:00 PM','general',NOW()),
('license_authority','Punjab Food Authority','general',NOW()),
('license_number','PFA/RWP/GK/PE-2024','general',NOW()),
('psqca_number','PSQCA / PS-4639 compliant','general',NOW()),
('per_litre_rate','6','general',NOW()),
('free_delivery_note','Free delivery on 19L refills across our full coverage area','general',NOW()),
('footer_about','Pak-Everests is a Punjab Food Authority approved mineral water plant based in Gujar Khan, serving homes, offices, schools, hospitals, mosques and businesses across the Potohar region with an eight stage purification process and mineral balanced drinking water.','general',NOW()),
('company_founded','2019','general',NOW()),

-- Social -------------------------------------------------------------------
('social_facebook','https://facebook.com/pakeverests','social',NOW()),
('social_instagram','https://instagram.com/pakeverests','social',NOW()),
('social_youtube','https://youtube.com/@pakeverests','social',NOW()),
('social_tiktok','https://tiktok.com/@pakeverests','social',NOW()),
('social_linkedin','https://linkedin.com/company/pakeverests','social',NOW()),
('social_twitter','https://x.com/pakeverests','social',NOW()),
('social_whatsapp_channel','','social',NOW()),

-- SEO ----------------------------------------------------------------------
('meta_title','Pak-Everests Mineral Water | Water Plant in Gujar Khan, Potohar','seo',NOW()),
('meta_description','Punjab Food Authority approved mineral water plant in Gujar Khan. Order 19 litre refills at Rs 250 with free delivery across Rawalpindi and Islamabad.','seo',NOW()),
('meta_keywords','water plant near me, mineral water, 19 liters water bottle, mineral water plant, water plant in Gujar Khan, best water plant in Gujar Khan, water delivery Rawalpindi, drinking water Islamabad, water supplier Potohar, bottled water Pakistan','seo',NOW()),
('google_site_verification','','seo',NOW()),
('bing_site_verification','','seo',NOW()),
('yandex_verification','','seo',NOW()),
('pinterest_verification','','seo',NOW()),
('google_analytics_id','','seo',NOW()),
('google_tag_manager_id','','seo',NOW()),
('meta_pixel_id','','seo',NOW()),
('tiktok_pixel_id','','seo',NOW()),
('custom_head_code','','seo',NOW()),
('custom_body_code','','seo',NOW()),
('robots_txt','','seo',NOW()),
('ads_txt','','seo',NOW()),
('sitemap_enabled','1','seo',NOW()),

-- Maps ---------------------------------------------------------------------
('map_lat','33.2544','maps',NOW()),
('map_lng','73.3047','maps',NOW()),
('map_zoom','14','maps',NOW()),
('map_embed_code','<iframe src="https://www.google.com/maps?q=Gujar%20Khan%2C%20Punjab%2C%20Pakistan&output=embed" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Pak-Everests Water Plant location"></iframe>','maps',NOW()),
('map_directions_url','https://www.google.com/maps/dir/?api=1&destination=Gujar+Khan+Punjab+Pakistan','maps',NOW()),

-- Ticker -------------------------------------------------------------------
('ticker_enabled','1','ticker',NOW()),
('ticker_label','LATEST','ticker',NOW()),
('ticker_speed','45','ticker',NOW()),
('ticker_font_size','14','ticker',NOW()),
('ticker_font_weight','600','ticker',NOW()),
('ticker_font_family','inherit','ticker',NOW()),
('ticker_bg','#0b6fa4','ticker',NOW()),
('ticker_color','#ffffff','ticker',NOW()),
('ticker_highlight_color','#7fe3ff','ticker',NOW()),
('ticker_pause_on_hover','1','ticker',NOW()),

-- Payments -----------------------------------------------------------------
('payment_cod_enabled','1','payment',NOW()),
('payment_note','Cash on delivery is available across the full coverage area. For advance or online payment please use any of the accounts below and share the screenshot on WhatsApp.','payment',NOW()),
('easypaisa_enabled','1','payment',NOW()),
('easypaisa_title','Pak-Everests','payment',NOW()),
('easypaisa_number','0333 5592206','payment',NOW()),
('jazzcash_enabled','1','payment',NOW()),
('jazzcash_title','Pak-Everests','payment',NOW()),
('jazzcash_number','0332 2901309','payment',NOW()),
('bank_enabled','1','payment',NOW()),
('bank_name','Meezan Bank Limited','payment',NOW()),
('bank_branch','Gujar Khan Branch','payment',NOW()),
('bank_account_title','Pak-Everests Bottled Drinking Water','payment',NOW()),
('bank_account_number','0000-0000000000','payment',NOW()),
('bank_iban','PK00MEZN0000000000000000','payment',NOW()),
('bank_swift','MEZNPKKA','payment',NOW()),

-- SMTP ---------------------------------------------------------------------
('smtp_enabled','0','smtp',NOW()),
('smtp_host','smtp.hostinger.com','smtp',NOW()),
('smtp_port','465','smtp',NOW()),
('smtp_encryption','ssl','smtp',NOW()),
('smtp_username','info@pakeverests.site','smtp',NOW()),
('smtp_password','','smtp',NOW()),
('smtp_from_email','info@pakeverests.site','smtp',NOW()),
('smtp_from_name','Pak-Everests Water','smtp',NOW()),

-- Ads ----------------------------------------------------------------------
('ads_enabled','0','ads',NOW()),
('adsense_publisher_id','','ads',NOW()),
('adsense_auto_ads','0','ads',NOW()),

-- Analytics ----------------------------------------------------------------
('analytics_tracking_enabled','1','analytics',NOW()),
('analytics_track_bots','0','analytics',NOW()),
('geo_lookup_enabled','1','analytics',NOW()),

-- Website behaviour --------------------------------------------------------
('orders_enabled','1','website',NOW()),
('reviews_open','1','website',NOW()),
('default_theme','light','website',NOW()),
('whatsapp_float_enabled','1','website',NOW()),
('announcement_bar','','website',NOW()),
('maintenance_mode','0','website',NOW());

-- --------------------------------------------------------------------------
--  WhatsApp numbers
-- --------------------------------------------------------------------------
INSERT INTO `whatsapp_numbers` (`label`,`number`,`department`,`receives_orders`,`is_primary`,`is_active`,`sort_order`,`created_at`) VALUES
('Orders and Delivery','0333 5592206','Sales',1,1,1,1,NOW()),
('Support and Distribution','0332 2901309','Support',1,0,1,2,NOW());

-- --------------------------------------------------------------------------
--  Coverage areas
-- --------------------------------------------------------------------------
INSERT INTO `coverage_areas` (`area_name`,`district`,`description`,`delivery_days`,`min_order`,`is_free_delivery`,`is_active`,`sort_order`) VALUES
('Gujar Khan','Rawalpindi','Our home town and the location of the Pak-Everests filling plant. Same day delivery for orders placed before 4:00 PM.','Daily','1 bottle',1,1,1),
('Mandra','Rawalpindi','Daily route covering Mandra bazaar, the Chakwal road junction and surrounding villages.','Daily','1 bottle',1,1,2),
('Daultala','Rawalpindi','Scheduled route covering Daultala town and adjoining residential colonies.','Alternate days','1 bottle',1,1,3),
('Bewal','Rawalpindi','Covered on the Kallar Syedan corridor route with same week scheduling.','Alternate days','1 bottle',1,1,4),
('Habib Chowk','Rawalpindi','Shops, marts and residential blocks around Habib Chowk are served on the daily Gujar Khan route.','Daily','1 bottle',1,1,5),
('Kallar Syedan','Rawalpindi','Full town coverage including main bazaar, schools and clinics.','Alternate days','1 bottle',1,1,6),
('Rawat','Rawalpindi','Industrial units, marts and housing schemes along the Rawat and G.T. Road belt.','Daily','1 bottle',1,1,7),
('DHA Islamabad','Islamabad','Phase I to Phase V residential and commercial deliveries with scheduled time slots.','Daily','1 bottle',1,1,8),
('Bahria Town Rawalpindi','Rawalpindi','All phases including Bahria Enclave routes, offices, clinics and residences.','Daily','1 bottle',1,1,9),
('Adiala Road','Rawalpindi','Housing societies, schools and shops along the full Adiala Road stretch.','Daily','1 bottle',1,1,10),
('Rawalpindi City and Cantt','Rawalpindi','Saddar, Chaklala, Peshawar Road, Morgah, Westridge, Satellite Town and the wider Rawalpindi region.','Daily','1 bottle',1,1,11);

-- --------------------------------------------------------------------------
--  Products
-- --------------------------------------------------------------------------
INSERT INTO `products`
(`slug`,`name`,`short_name`,`category`,`pack_size`,`volume_label`,`price`,`price_unit`,`security_deposit`,`rent_price`,`rent_unit`,`sku`,`tagline`,`short_description`,`long_description`,`features`,`specifications`,`best_for`,`free_delivery`,`is_coming_soon`,`is_featured`,`is_bestseller`,`stock_status`,`rating`,`rating_count`,`meta_title`,`meta_description`,`meta_keywords`,`sort_order`,`status`,`created_at`) VALUES

('19-litre-refill-bottle','19 Litre Refill Water Bottle','19L Refill','refill','Single bottle','19 Litres',250.00,'per refill',1500.00,NULL,NULL,'PE-19L',
'The household and office favourite. Rs 250 per refill with free delivery and a one time refundable bottle deposit.',
'<p>The Pak-Everests 19 litre refill bottle is the backbone of our service and the most requested pack across Gujar Khan, Rawalpindi and Islamabad. Every bottle is washed, sanitised, inspected and filled inside our automated plant, then sealed and delivered straight to your door at no extra charge.</p><p>You pay a one time refundable security deposit of Rs 1,500 for each bottle you keep at your premises. After that, each refill costs only Rs 250. Return the bottle in a sound condition whenever you stop the service and the full deposit is returned to you.</p>',
'<h2>Why families and offices choose the 19 litre refill</h2><p>A single 19 litre bottle carries roughly 95 glasses of water, which is enough for a family of five for two to three days or for a small office of ten people for a full working day. It sits on any standard hot and cold dispenser, on a table top dispenser, or on a manual pump, so you do not need to change your existing setup.</p><p>Every bottle we deliver has passed through our full eight stage purification line and has been re-mineralised so the water stays naturally sweet rather than flat. The finished water carries balanced calcium, magnesium, potassium and sodium, which is what gives Pak-Everests its clean taste.</p><h2>How the deposit and refill system works</h2><h3>Step 1 - Your first delivery</h3><p>On your first order you pay Rs 1,500 as a refundable security deposit for each bottle, plus Rs 250 for the water inside it. The deposit is recorded against your name, address and phone number and a receipt is issued at the door.</p><h3>Step 2 - Every refill after that</h3><p>From your second delivery onwards you simply hand over the empty bottle and pay Rs 250 for the fresh one. There is no delivery charge anywhere in our coverage area, and no minimum order.</p><h3>Step 3 - When you stop the service</h3><p>Return the empty bottles in a usable condition and the full deposit is refunded. Please see our refund policy and bottle damage policy for the small number of cases where a deduction applies.</p><h2>Hygiene at every step</h2><p>Returned bottles are never simply topped up. Each one is emptied, visually inspected under light, pre-rinsed, scrubbed inside with food grade caustic solution, rinsed with treated water, sanitised, and only then moved to the filling head. Bottles that show scratches, odour, deep staining or deformation are permanently retired from the fleet.</p><h2>Ideal for</h2><p>Homes, corporate offices, schools and colleges, hospitals and clinics, mosques, shops, marts and shopping centres, construction site offices, and any workplace that runs a water dispenser.</p>',
'Free delivery across the entire coverage area
Refundable security deposit of Rs 1,500 per bottle
Food grade, BPA free polycarbonate bottle
Tamper evident cap with a heat shrink seal
Fits every standard hot and cold dispenser
Bottle washed and sanitised before every fill
Same day delivery for orders placed before 4:00 PM
Batch number and filling date printed on every bottle',
'Volume: 19 Litres (approximately 5 gallons)
Refill price: Rs 250
Security deposit: Rs 1,500 per bottle (refundable)
Delivery: Free
Bottle material: Food grade BPA free polycarbonate
Cap: Tamper evident, single use
Shelf life: 6 months from the filling date, sealed
Neck size: Standard 55 mm dispenser neck',
'Homes, offices, schools, hospitals, mosques, shops and marts',
1,0,1,1,'in_stock',4.90,186,
'19 Litre Refill Water Bottle Rs 250 | Free Delivery | Pak-Everests',
'Order a 19 litre mineral water refill for Rs 250 with free delivery in Gujar Khan, Rawalpindi and Islamabad. Refundable Rs 1,500 bottle deposit.',
'19 liters water bottle, 19 litre refill, water bottle price in Pakistan, mineral water 19 litre, water plant near me',
1,'published',NOW()),

('12-litre-water-bottle','12 Litre Water Bottle','12L Bottle','bottles','Single bottle','12 Litres',230.00,'per bottle',0.00,NULL,NULL,'PE-12L',
'A lighter bottle for smaller families and shops. Rs 230 delivered, easy to lift and store.',
'<p>The 12 litre Pak-Everests bottle is built for households where a full 19 litre bottle is more than needed, and for shops that want a fast moving pack on the counter. It is filled on the same automated line as our 19 litre bottles and carries the same mineral profile.</p>',
'<h2>The convenient middle size</h2><p>At 12 litres this bottle holds around 60 glasses of drinking water. It weighs noticeably less than a 19 litre bottle when full, which makes it far easier for elderly customers, students in hostels and small households to lift and pour without a dispenser stand.</p><h2>Where the 12 litre bottle works best</h2><h3>Small households</h3><p>Two to three person households usually finish a 12 litre bottle in two to three days, so the water is always fresh and the bottle never sits open for long.</p><h3>Shops and counters</h3><p>Retailers stock the 12 litre size because customers can carry it home on a motorcycle without difficulty, which turns it into a steady repeat seller.</p><h3>Offices with a table top dispenser</h3><p>Many compact table top dispensers accept the 12 litre neck directly, so a small office of four to six staff can run comfortably on one bottle a day.</p><h2>Same water, same standards</h2><p>The 12 litre bottle draws from the same eight stage purification line as every other Pak-Everests pack. Sediment filtration, activated carbon, softening, reverse osmosis, ultraviolet sterilisation, ozonation and controlled re-mineralisation are all applied before filling, and every batch is checked in our in-house laboratory.</p>',
'Free delivery across the entire coverage area
Lighter and easier to handle than a 19 litre bottle
Food grade BPA free bottle
Tamper evident cap and heat shrink seal
Ideal for small families, hostels and shop counters
Same eight stage purification as every Pak-Everests product',
'Volume: 12 Litres
Price: Rs 230
Delivery: Free
Bottle material: Food grade BPA free
Cap: Tamper evident
Shelf life: 6 months from the filling date, sealed',
'Small families, hostels, shops, small offices',
1,0,1,0,'in_stock',4.80,94,
'12 Litre Water Bottle Rs 230 | Mineral Water Delivery | Pak-Everests',
'Buy the Pak-Everests 12 litre mineral water bottle for Rs 230 with free delivery across Gujar Khan, Rawalpindi and Islamabad.',
'12 litre water bottle, 12 liter water bottle price, mineral water bottle, water delivery Gujar Khan',
2,'published',NOW()),

('6-litre-water-bottle','6 Litre Water Bottle','6L Bottle','bottles','Single bottle','6 Litres',130.00,'per bottle',0.00,NULL,NULL,'PE-6L',
'Compact, carry friendly and priced at Rs 130. Perfect for kitchens, cars and small counters.',
'<p>The 6 litre bottle is the most portable member of the Pak-Everests family. It fits in a refrigerator door shelf, in a car boot and on a kitchen counter, and it is priced so that it works as an everyday purchase rather than an occasional one.</p>',
'<h2>Small pack, full mineral profile</h2><p>Do not let the size mislead you. The 6 litre bottle receives exactly the same treatment as our 19 litre refill: eight stages of purification followed by controlled re-mineralisation that restores calcium, magnesium, potassium and sodium to a balanced level.</p><h2>Everyday uses</h2><h3>In the kitchen</h3><p>Six litres is enough for a day of cooking, tea and drinking for a small family, and the narrow footprint means it stores easily beside the sink.</p><h3>On the road</h3><p>Drivers, delivery riders and field teams keep a 6 litre bottle in the vehicle because it is the largest size one hand can comfortably lift and pour.</p><h3>For events and gatherings</h3><p>Small majlis gatherings, tuition centres and prayer rooms use the 6 litre size where a dispenser is not available.</p><h2>Storage advice</h2><p>Keep the bottle out of direct sunlight and away from strong smelling items such as detergents and fuel. Once opened, finish within three to four days for the best taste.</p>',
'Free delivery across the entire coverage area
Easy to lift and pour with one hand
Fits in a refrigerator door and car boot
Food grade BPA free bottle
Tamper evident cap and seal
Great for kitchens, vehicles, tuition centres and prayer rooms',
'Volume: 6 Litres
Price: Rs 130
Delivery: Free
Bottle material: Food grade BPA free
Cap: Tamper evident
Shelf life: 6 months from the filling date, sealed',
'Kitchens, cars, small counters, tuition centres',
1,0,1,0,'in_stock',4.80,72,
'6 Litre Water Bottle Rs 130 | Portable Mineral Water | Pak-Everests',
'Order the Pak-Everests 6 litre mineral water bottle for Rs 130 with free delivery in Gujar Khan, Rawalpindi and Islamabad. Punjab Food Authority approved.',
'6 litre water bottle, 6 liter water bottle price, small mineral water bottle, water delivery Rawalpindi',
3,'published',NOW()),

('1-5-litre-pack-of-6-pure','1.5 Litre Pure Pack of 6','1.5L Pure x6','pet','Pack of 6 bottles','1.5 Litres',400.00,'per pack of 6',0.00,NULL,NULL,'PE-15-PURE-6',
'Our premium 1.5 litre PET line, six bottles to a shrink wrapped pack at Rs 400.',
'<p>The 1.5 litre Pure pack is the Pak-Everests premium PET offering. Six crystal clear bottles are shrink wrapped into a single carry pack, filled on a fully enclosed line and sealed with a tamper evident ring that shows instantly if a bottle has ever been opened.</p>',
'<h2>What makes the Pure line premium</h2><p>The Pure line runs on a tighter mineral specification and a higher grade preform than our standard pack. The bottle wall is thicker, the clarity is higher and the cap ring is heavier, which together produce a bottle that holds its shape on a dining table, in a conference room and in a retail chiller.</p><h2>Where the 1.5 litre Pure pack is used</h2><h3>Dining tables and family meals</h3><p>A 1.5 litre bottle is the natural size for a family dining table, and a pack of six covers a normal week for most households.</p><h3>Corporate meetings and conference rooms</h3><p>Companies across Bahria Town, DHA Islamabad and the Rawalpindi commercial belt keep the Pure pack for boardrooms and reception areas, and many of them move on to custom printed labels for their own brand.</p><h3>Marts, shops and shopping centres</h3><p>The shrink wrapped six pack is designed for shelf stacking, and the pack price of Rs 400 leaves a healthy retail margin.</p><h2>Handling and storage</h2><p>Store packs flat, away from direct sunlight, and away from chemicals or fuel. Stack no more than four packs high to protect the bottles at the bottom of the stack.</p>',
'Six 1.5 litre bottles per shrink wrapped pack
Premium clarity PET with a thicker bottle wall
Tamper evident cap ring on every bottle
Balanced mineral profile for a naturally sweet taste
Shelf ready packaging for marts and shops
Available with custom printed labels for events and companies',
'Pack: 6 x 1.5 Litres (9 litres total)
Pack price: Rs 400
Per bottle: approximately Rs 67
Bottle material: Food grade PET
Cap: Tamper evident ring
Shelf life: 9 months from the filling date, sealed',
'Dining tables, boardrooms, marts, hotels and events',
1,0,1,1,'in_stock',4.90,131,
'1.5 Litre Pure Water Pack of 6 Rs 400 | Pak-Everests Mineral Water',
'Buy the Pak-Everests 1.5 litre Pure mineral water pack of 6 for Rs 400. Premium PET bottles with tamper evident caps, free delivery across Rawalpindi and Islamabad.',
'1.5 litre water bottle, pack of 6 water bottles, mineral water pack, PET water bottle Pakistan',
4,'published',NOW()),

('1-5-litre-pack-of-6-mix','1.5 Litre Mix Pack of 6','1.5L Mix x6','pet','Pack of 6 bottles','1.5 Litres',350.00,'per pack of 6',0.00,NULL,NULL,'PE-15-MIX-6',
'The everyday 1.5 litre six pack at Rs 350. Same purified water, value focused packaging.',
'<p>The 1.5 litre Mix pack delivers the same purified and mineral balanced Pak-Everests water in a value focused pack. It is the pack our retail partners move in the highest volume, because the Rs 350 price point works for daily household purchases.</p>',
'<h2>Pure or Mix, which one should you order?</h2><p>Both packs contain the same eight stage purified, re-mineralised Pak-Everests water. The difference is in the packaging grade. The Pure pack uses a heavier premium preform with higher clarity, while the Mix pack uses our standard commercial preform. If the bottle will sit on a boardroom table or carry your company label, choose Pure. If it is going into a household refrigerator, a school canteen or a busy shop, the Mix pack gives you the same water at a lower price.</p><h2>Best suited for</h2><h3>Households buying weekly</h3><p>A six pack covers a typical family week of drinking water alongside a dispenser bottle.</p><h3>Canteens and cafeterias</h3><p>School, college and factory canteens use the Mix pack because the per bottle cost of roughly Rs 58 keeps resale pricing comfortable.</p><h3>Shops, marts and general stores</h3><p>Fast turnover, easy stacking and a familiar price point.</p><h2>Bulk and standing orders</h2><p>Retailers, canteens and offices placing standing weekly orders receive route priority and volume pricing. Contact us on WhatsApp for a formal quotation.</p>',
'Six 1.5 litre bottles per shrink wrapped pack
Value pricing for households, canteens and shops
Same eight stage purified and re-mineralised water
Tamper evident cap ring on every bottle
Easy to stack shelf ready packaging
Volume pricing available on standing orders',
'Pack: 6 x 1.5 Litres (9 litres total)
Pack price: Rs 350
Per bottle: approximately Rs 58
Bottle material: Food grade PET
Cap: Tamper evident ring
Shelf life: 9 months from the filling date, sealed',
'Households, canteens, shops, general stores',
1,0,0,1,'in_stock',4.70,118,
'1.5 Litre Mix Water Pack of 6 Rs 350 | Pak-Everests Mineral Water',
'Order the Pak-Everests 1.5 litre pack of 6 for Rs 350 with free delivery. Purified, mineral balanced drinking water for homes, canteens and shops.',
'1.5 litre water pack, cheap mineral water pack, water bottle pack of 6, water supplier Rawalpindi',
5,'published',NOW()),

('500ml-pack-of-12-pure','500 ml Pure Pack of 12','500ml Pure x12','pet','Pack of 12 bottles','500 ml',400.00,'per pack of 12',0.00,NULL,NULL,'PE-500-PURE-12',
'Twelve premium 500 ml bottles at Rs 400. The pack of choice for events, offices and hospitality.',
'<p>The 500 ml Pure pack contains twelve premium PET bottles, shrink wrapped and ready for a reception desk, a meeting room, a mosque, an event hall or a hotel minibar. It is also our most popular base for custom printed labels.</p>',
'<h2>The single serve standard</h2><p>A 500 ml bottle is the size people actually finish in one sitting, which means no half empty bottles left behind and no waste. For events, seminars, weddings and corporate functions this is the only practical size to serve.</p><h2>Popular applications</h2><h3>Corporate offices and reception areas</h3><p>Keep a chilled pack at reception and in meeting rooms. Companies frequently move to a custom label so their brand is on every table.</p><h3>Weddings, seminars and conferences</h3><p>Event managers across Rawalpindi and Islamabad order the 500 ml Pure pack in bulk with a printed label carrying the family name, company logo or event branding.</p><h3>Hospitals, clinics and pharmacies</h3><p>Sealed single serve bottles are the hygienic standard for patient rooms and waiting areas.</p><h3>Mosques and religious gatherings</h3><p>Easy to distribute, sealed until the moment it is opened, and simple to store.</p><h2>Custom labelling</h2><p>The 500 ml Pure bottle is our best selling custom label format. Minimum order quantities, artwork requirements and turnaround times are set out on our custom label bottles page, and you can submit your design brief through the customisation form.</p>',
'Twelve 500 ml bottles per shrink wrapped pack
Premium clarity PET with tamper evident caps
The most requested size for custom printed labels
Perfect single serve size with no waste
Ideal for events, offices, clinics and mosques
Bulk and event pricing available',
'Pack: 12 x 500 ml (6 litres total)
Pack price: Rs 400
Per bottle: approximately Rs 33
Bottle material: Food grade PET
Cap: Tamper evident ring
Shelf life: 9 months from the filling date, sealed',
'Events, weddings, offices, hospitals, mosques, hotels',
1,0,1,1,'in_stock',4.90,142,
'500 ml Pure Water Pack of 12 Rs 400 | Event Water | Pak-Everests',
'Buy the Pak-Everests 500 ml Pure pack of 12 for Rs 400. Premium single serve mineral water for events, offices and clinics, with custom label printing available.',
'500ml water bottle, pack of 12 water bottles, event water bottles, custom label water bottle Pakistan',
6,'published',NOW()),

('500ml-pack-of-12-mix','500 ml Mix Pack of 12','500ml Mix x12','pet','Pack of 12 bottles','500 ml',350.00,'per pack of 12',0.00,NULL,NULL,'PE-500-MIX-12',
'Twelve 500 ml bottles at Rs 350. Everyday value for shops, canteens and daily use.',
'<p>The 500 ml Mix pack brings the Pak-Everests single serve bottle to a value price point. Twelve bottles per pack at Rs 350 makes it the natural stock item for shops, canteens, tuck shops and daily household use.</p>',
'<h2>Value without compromise on the water</h2><p>The water inside a Mix pack is identical to the water inside a Pure pack. Both come off the same eight stage line, both are re-mineralised to the same profile, and both are tested in the same laboratory. The Mix pack simply uses our standard commercial preform instead of the heavier premium one.</p><h2>Who buys the 500 ml Mix pack</h2><h3>Shops and tuck shops</h3><p>A fast moving, low ticket item that customers buy without thinking twice.</p><h3>School and college canteens</h3><p>Single serve sealed bottles are the safest option for students, and the pack economics work for canteen resale.</p><h3>Construction sites and field teams</h3><p>Site supervisors keep cartons on hand so every worker has a sealed bottle rather than a shared cooler.</p><h3>Households</h3><p>Handy for school bags, lunch boxes, travel and day trips.</p>',
'Twelve 500 ml bottles per shrink wrapped pack
Value pricing for shops, canteens and daily use
Same purified, mineral balanced Pak-Everests water
Tamper evident cap ring on every bottle
Sealed single serve hygiene for schools and sites
Carton and bulk pricing available',
'Pack: 12 x 500 ml (6 litres total)
Pack price: Rs 350
Per bottle: approximately Rs 29
Bottle material: Food grade PET
Cap: Tamper evident ring
Shelf life: 9 months from the filling date, sealed',
'Shops, canteens, schools, construction sites, travel',
1,0,0,1,'in_stock',4.70,103,
'500 ml Water Pack of 12 Rs 350 | Pak-Everests Mineral Water',
'Order the Pak-Everests 500 ml pack of 12 for Rs 350 with free delivery in Gujar Khan, Rawalpindi and Islamabad. Sealed single serve mineral water.',
'500ml water bottle price, pack of 12 water, canteen water bottles, cheap water bottles Rawalpindi',
7,'published',NOW()),

('350ml-pack-of-24','350 ml Pack of 24','350ml x24','pet','Pack of 24 bottles','350 ml',0.00,'pricing to be announced',0.00,NULL,NULL,'PE-350-24',
'A compact 350 ml single serve bottle in a 24 pack. Launching soon.',
'<p>The 350 ml pack of 24 is currently in final packaging trials at our Gujar Khan plant. It is designed for airline style service, school lunch boxes, children, hospital trays and high volume events where a 500 ml bottle is more than a guest will finish.</p>',
'<h2>Launching soon</h2><p>The 350 ml format completes our single serve range. At 24 bottles per pack it is built for caterers, event managers, schools and hospitals that serve large numbers of people in a short window and want to eliminate the waste that comes with half finished bottles.</p><h2>What to expect at launch</h2><h3>The right size for children</h3><p>A 350 ml bottle fits a school lunch box and a child can finish it in one break, so nothing is thrown away.</p><h3>Event and catering economics</h3><p>Caterers report that guests leave roughly a third of a 500 ml bottle unfinished at seated events. The 350 ml bottle removes most of that loss.</p><h3>Custom labels from day one</h3><p>The 350 ml bottle will be available with custom printed labels on the same terms as our 500 ml and 1.5 litre bottles.</p><h2>Register your interest</h2><p>Send us a message on WhatsApp or through the contact form and we will notify you the day the 350 ml pack goes on sale, along with launch pricing for standing orders.</p>',
'Twenty four 350 ml bottles per pack
Sized for children, lunch boxes and hospital trays
Reduces unfinished bottle waste at catered events
Custom label printing available at launch
Same eight stage purified Pak-Everests water
Register your interest for launch pricing',
'Pack: 24 x 350 ml (8.4 litres total)
Status: Coming soon
Bottle material: Food grade PET
Cap: Tamper evident ring',
'Schools, hospitals, caterers, events',
1,1,0,0,'coming_soon',5.00,0,
'350 ml Water Pack of 24 Coming Soon | Pak-Everests Mineral Water',
'The Pak-Everests 350 ml pack of 24 is launching soon. Register your interest for launch pricing on single serve mineral water for schools, hospitals and events.',
'350ml water bottle, small water bottle pack, school water bottles, event water Pakistan',
8,'published',NOW()),

('glass-water-bottles','Glass Water Bottles','Glass Bottles','glass','Returnable crate','Glass range',0.00,'pricing to be announced',0.00,NULL,NULL,'PE-GLASS',
'Premium returnable glass bottles for restaurants, hotels and fine dining. Launching soon.',
'<p>Pak-Everests glass bottles are in development for the hospitality segment. Returnable, sterilised and elegant on a table, they are intended for restaurants, hotels, banquet halls and executive offices that want a premium presentation and a lower packaging footprint.</p>',
'<h2>Why glass</h2><p>Glass is chemically inert. It adds nothing to the water and takes nothing from it, which is why fine dining venues prefer it. A returnable glass bottle also removes single use plastic from the table entirely, which matters increasingly to hotels and corporate clients with sustainability commitments.</p><h2>Planned formats</h2><h3>Table service bottles</h3><p>Slim profile bottles sized for restaurant table service, supplied in returnable crates on a deposit basis similar to our 19 litre bottles.</p><h3>Executive and banquet bottles</h3><p>Larger shared bottles for boardroom tables and banquet settings.</p><h2>Returnable crate system</h2><p>Glass will operate on a crate deposit model. Empty bottles are collected on the next scheduled delivery, returned to the plant, washed and sterilised in a dedicated glass line, then refilled. Full deposit terms will be published with the launch.</p><h2>Register your interest</h2><p>Hotels, restaurants and event venues can register now to be included in the first supply round.</p>',
'Returnable and reusable glass bottles
Chemically inert, adds nothing to the taste
Premium presentation for restaurants and hotels
Crate based deposit and collection system
Lower single use plastic footprint
Launching soon, register your interest',
'Format: Returnable glass, multiple sizes
Status: Coming soon
System: Crate deposit with scheduled collection',
'Restaurants, hotels, banquet halls, executive offices',
1,1,0,0,'coming_soon',5.00,0,
'Glass Water Bottles Coming Soon | Pak-Everests Mineral Water',
'Premium returnable glass mineral water bottles for restaurants, hotels and banquet halls are launching soon from Pak-Everests. Register your interest today.',
'glass water bottles, restaurant water bottles, hotel mineral water, premium water Pakistan',
9,'published',NOW()),

('water-pouches','Water Pouches','Water Pouches','pouch','Carton','Pouch range',0.00,'pricing to be announced',0.00,NULL,NULL,'PE-POUCH',
'Sealed single serve water pouches for events, relief work and large gatherings. Launching soon.',
'<p>Pak-Everests water pouches are being developed for high volume distribution where cost per serve and rapid handling matter most. Think religious gatherings, langar service, sports events, relief distribution and outdoor labour.</p>',
'<h2>Built for volume</h2><p>A sealed pouch is the lowest cost way to hand safe drinking water to a large number of people quickly. Pouches stack densely, weigh very little as packaging, and can be distributed by hand at speed without cups, dispensers or coolers.</p><h2>Planned uses</h2><h3>Religious gatherings and langar</h3><p>Mosques, shrines and community kitchens serve thousands of people in a short window, and pouches remove the need for shared glasses entirely.</p><h3>Relief and emergency distribution</h3><p>Compact, sealed and easy to transport in bulk.</p><h3>Sports events and outdoor work</h3><p>Easy to carry in quantity, easy to hand out, and no bottle to collect afterwards.</p><h2>Same water, different packaging</h2><p>The water inside the pouch will come from the same eight stage purification line as every other Pak-Everests product, with the same laboratory testing on every batch.</p><h2>Register your interest</h2><p>Organisations planning large gatherings can contact us now to be included in the launch supply.</p>',
'Sealed single serve pouches
Lowest cost per serve for large gatherings
Dense stacking and fast hand distribution
Same eight stage purified water
Suitable for mosques, events and relief work
Launching soon, register your interest',
'Format: Sealed single serve pouch
Status: Coming soon
Packing: Carton',
'Mosques, langar service, events, relief distribution',
1,1,0,0,'coming_soon',5.00,0,
'Water Pouches Coming Soon | Pak-Everests Bottled Drinking Water',
'Sealed single serve water pouches for mosques, events, langar service and relief distribution are launching soon from Pak-Everests. Register your interest.',
'water pouches, sealed water pouch, event water Pakistan, langar water supply',
10,'published',NOW()),

('water-dispenser','Water Dispenser','Dispenser','equipment','Single unit','Hot and cold',42000.00,'outright purchase',0.00,2000.00,'per month on rent','PE-DISP',
'Hot and cold dispenser. Buy outright at Rs 42,000 or rent for Rs 2,000 per month, on demand.',
'<p>A Pak-Everests hot and cold water dispenser turns your 19 litre bottle into a complete drinking water station. Buy it outright for Rs 42,000 or take it on rent for Rs 2,000 per month with servicing included. Supplied on demand.</p>',
'<h2>Two ways to get a dispenser</h2><h3>Outright purchase - Rs 42,000</h3><p>The unit is yours. It is delivered, installed and demonstrated at your premises, and carries the manufacturer warranty. This is the sensible option for offices, schools and clinics that will run a dispenser for years.</p><h3>Monthly rental - Rs 2,000 per month</h3><p>Rental suits temporary sites, seasonal offices, construction site offices, event venues and anyone who wants a dispenser without capital outlay. Rental includes routine servicing and sanitisation on schedule. A refundable security deposit applies to rented units and is set out in the dispenser agreement.</p><h2>What the dispenser gives you</h2><p>Instant hot water for tea and coffee, chilled water on demand, and a normal temperature tap, all from the same 19 litre bottle. Stainless steel tanks on the hot side, a compressor cooling circuit on the cold side, and a child safety lock on the hot tap.</p><h2>Installation and service</h2><h3>Installation</h3><p>Our team delivers the unit, positions it, connects it to power, purges the lines and runs a full demonstration before leaving. Allow the unit thirty minutes on power before drawing hot water for the first time.</p><h3>Sanitisation schedule</h3><p>Dispensers accumulate biofilm over time regardless of how clean the water going in is. We recommend a full internal sanitisation every three months. This is included for rented units and available as a paid service for purchased units.</p><h3>Care between services</h3><p>Wipe the taps and drip tray daily, keep the unit out of direct sunlight, and never let the bottle run completely dry on the hot tank.</p><h2>Damage and liability</h2><p>Rented dispensers remain the property of Pak-Everests. Responsibility for loss, misuse and damage, and the deductions that apply, are set out in full in our damage policy and in the dispenser agreement issued at installation.</p>',
'Outright purchase at Rs 42,000
Monthly rental at Rs 2,000 with servicing included
Hot, cold and normal temperature taps
Stainless steel hot tank
Child safety lock on the hot water tap
Free delivery, installation and demonstration
Quarterly sanitisation service available
Supplied on demand across the coverage area',
'Purchase price: Rs 42,000
Rental: Rs 2,000 per month
Taps: Hot, cold and normal
Bottle compatibility: 19 litre and 12 litre neck
Power: 220V AC, 50Hz
Availability: On demand
Rental security deposit: As per dispenser agreement',
'Offices, schools, hospitals, mosques, shops and homes',
1,0,1,0,'on_demand',4.80,57,
'Water Dispenser Rs 42,000 or Rs 2,000 Monthly Rent | Pak-Everests',
'Buy a hot and cold water dispenser for Rs 42,000 or rent one for Rs 2,000 per month, with free installation and quarterly sanitisation.',
'water dispenser price in Pakistan, water dispenser on rent, hot and cold dispenser, office water dispenser Rawalpindi',
11,'published',NOW()),

('bulk-water-filling','Bulk Water Filling Station','Bulk Filling','bulk','Per litre','Any volume',6.00,'per litre',0.00,NULL,NULL,'PE-BULK',
'Bring your own container and fill at Rs 6 per litre at our Gujar Khan facility.',
'<p>Our facility filling counter lets you fill your own clean container with fully purified Pak-Everests water at a flat rate of Rs 6 per litre. It is the most economical way to buy water in volume, and it is popular with caterers, canteens, construction sites, tanker operators and households in the immediate Gujar Khan area.</p>',
'<h2>How facility filling works</h2><h3>Step 1 - Bring a clean, food grade container</h3><p>Containers must be food grade, sound and free of any previous chemical, fuel or paint contents. Our counter staff inspect every container before filling and will politely refuse any container that is not suitable for drinking water.</p><h3>Step 2 - We rinse and inspect</h3><p>Containers are rinsed with treated water at the counter before filling. If you would like a full sanitisation rather than a rinse, tell the counter staff and we will arrange it.</p><h3>Step 3 - Fill and pay by volume</h3><p>Filling is metered. You pay exactly Rs 6 for every litre dispensed, with no minimum and no maximum.</p><h2>Use the calculator</h2><p>The bulk water calculator on this site works out your cost instantly for any volume, daily consumption or monthly requirement. It is on the facility filling page and on this product page.</p><h2>Who uses bulk filling</h2><p>Caterers and marquee operators, school and factory canteens, construction site managers, tanker operators serving housing schemes, and households in Gujar Khan who prefer to collect rather than take delivery.</p><h2>Important notes</h2><p>Facility filling is a collection service at the plant. It is not delivered, and free delivery does not apply to it. Water dispensed into a customer container leaves our chain of custody at the moment of filling, so storage hygiene afterwards is the customer responsibility.</p>',
'Flat rate of Rs 6 per litre
No minimum and no maximum volume
Metered filling, you pay for exactly what you take
Container inspection and rinse included
Fully purified, mineral balanced water
Collection at the Gujar Khan plant
Instant online cost calculator',
'Rate: Rs 6 per litre
Minimum order: None
Service type: Collection at plant, not delivered
Container: Customer supplied, food grade only
Timings: Monday to Sunday, 8:00 AM to 9:00 PM',
'Caterers, canteens, construction sites, tanker operators',
0,0,1,0,'in_stock',4.80,46,
'Bulk Water Filling Rs 6 Per Litre | Water Plant in Gujar Khan',
'Fill your own container with purified mineral water at Rs 6 per litre at the Pak-Everests plant in Gujar Khan. Free online calculator, no minimum volume.',
'bulk water filling, water per litre rate, water plant in Gujar Khan, water filling station near me, cheap drinking water Rawalpindi',
12,'published',NOW());

-- --------------------------------------------------------------------------
--  Eight stage purification process
-- --------------------------------------------------------------------------
INSERT INTO `process_stages` (`stage_no`,`title`,`subtitle`,`icon`,`summary`,`details`,`what_it_removes`,`is_active`) VALUES
(1,'Raw Water Intake and Storage','Deep bore extraction and first settlement','intake',
'Water is drawn from a protected deep bore inside our plant boundary and held in food grade storage where heavy particles settle out naturally.',
'<p>Everything begins at the source. Pak-Everests draws its raw water from a protected deep bore located inside the plant boundary at Gujar Khan. The bore head is sealed and raised above ground level so that surface run off, animal contact and casual contamination cannot reach it.</p><p>Water is lifted into food grade storage tanks that are covered, vented through filtered breathers and cleaned on a fixed schedule. During the holding period the heaviest suspended particles simply fall out of suspension under gravity, which lightens the load on every filter that follows. Raw water is sampled at this point for turbidity, pH, total dissolved solids and odour, and the readings are recorded in the batch log before treatment starts.</p>',
'Heavy suspended solids, settleable sand and grit',1),

(2,'Multi Media Sand and Gravel Filtration','Graded bed depth filtration','sand',
'Water passes down through graded layers of gravel, coarse sand and fine sand that trap suspended solids and reduce turbidity.',
'<p>The first active treatment stage is a pressure vessel packed with graded media. Coarse gravel sits at the bottom, then progressively finer sand towards the top. Water enters at the top and travels down through the bed, and the particles it carries are trapped at the depth where the media becomes too fine for them to pass.</p><p>This is depth filtration rather than surface filtration, which means the bed holds a large quantity of solids before it needs attention. The vessel is back washed on schedule, reversing the flow to lift and flush the trapped material to drain, then rinsed forward before it is returned to service. Turbidity is measured before and after the vessel and logged for every batch.</p>',
'Suspended solids, silt, clay, turbidity and visible cloudiness',1),

(3,'Activated Carbon Filtration','Chlorine, odour and organic removal','carbon',
'A deep bed of granular activated carbon adsorbs chlorine, organic compounds, pesticide residues and anything that affects taste or smell.',
'<p>Activated carbon is produced by treating carbon rich material at high temperature so that its internal surface area becomes enormous. A single gram can carry hundreds of square metres of adsorption surface. As water passes slowly through the bed, dissolved organic molecules, chlorine, chloramines, pesticide residues and the compounds responsible for unpleasant taste and smell attach themselves to that surface and stay there.</p><p>This stage matters for two reasons. It is what gives the finished water a clean, neutral taste, and it protects the reverse osmosis membranes downstream, because free chlorine damages thin film composite membrane material. Carbon media is monitored for exhaustion and replaced on a defined schedule rather than being run until performance visibly drops.</p>',
'Chlorine, chloramines, organic compounds, pesticide residues, colour, taste and odour',1),

(4,'Water Softening and Antiscalant Dosing','Hardness control and membrane protection','softener',
'Ion exchange resin swaps calcium and magnesium hardness ions for sodium, protecting the membranes from scale formation.',
'<p>Groundwater across the Potohar belt carries significant hardness. Left untreated, calcium and magnesium precipitate as scale on membrane surfaces and inside pipework, which destroys performance and shortens equipment life.</p><p>Our softening vessel is packed with strong acid cation exchange resin. As hard water passes through, calcium and magnesium ions are held by the resin and an equivalent charge of sodium is released in their place. The resin is regenerated on schedule with a brine solution that strips the accumulated hardness and recharges the exchange sites. In parallel, a metered antiscalant dose provides a second layer of protection at the membrane face.</p><p>It is worth being clear about what happens here. Softening deliberately removes hardness minerals for the sake of the equipment. The calcium and magnesium in the water you finally drink are added back under precise control at stage seven, which is why our finished water carries a balanced mineral profile rather than the raw and highly variable hardness of the aquifer.</p>',
'Calcium and magnesium hardness, scale forming ions, iron traces',1),

(5,'Micron Cartridge Filtration','Five micron and one micron polishing','micron',
'Sequential five micron and one micron cartridge filters catch any fine particle that survived the earlier stages.',
'<p>Before water reaches the membranes it passes through a sequential cartridge bank. A five micron cartridge comes first, followed by a one micron cartridge. Together they act as the final mechanical guard, removing carbon fines carried over from stage three, resin fragments from stage four and any remaining fine particulate.</p><p>Cartridge pressure differential is monitored continuously. When the pressure drop across the bank rises beyond the set threshold the cartridges are replaced, not cleaned. This is a consumable stage by design, and treating it as one is what keeps the membranes behind it working at full efficiency.</p>',
'Fine particulate down to one micron, carbon fines, resin fragments',1),

(6,'Reverse Osmosis Membrane Treatment','The heart of the plant','ro',
'High pressure forces water through a semi permeable membrane that rejects dissolved salts, heavy metals, nitrates, arsenic and microorganisms.',
'<p>Reverse osmosis is the stage that defines the quality of the finished water. A high pressure pump pushes water against a thin film composite membrane whose pores are so small that water molecules pass while almost everything larger is rejected and carried away to drain in the reject stream.</p><p>The membrane rejects dissolved salts, heavy metals including lead and arsenic, nitrates, fluoride excess, sulphates, bacteria, viruses and protozoan cysts. Total dissolved solids are reduced dramatically at this point. Feed pressure, permeate flow, reject flow and TDS on both sides are logged so that any decline in membrane performance is visible early rather than after it has affected product quality.</p><p>The result of this stage is extremely pure but mineral poor water, which is exactly the intended state. Purity first, then controlled mineral addition. That sequence is what allows us to guarantee a consistent mineral profile in every bottle regardless of seasonal changes in the aquifer.</p>',
'Dissolved salts, heavy metals, arsenic, lead, nitrates, sulphates, bacteria, viruses and cysts',1),

(7,'Controlled Re-mineralisation','Calcium, magnesium, potassium and sodium restored','mineral',
'Precisely measured mineral dosing restores calcium, magnesium, potassium and sodium to a balanced, health supporting profile.',
'<p>Water that has just left a reverse osmosis membrane is pure, but it is also flat to the palate and stripped of minerals the body uses. Stage seven puts back exactly what is wanted, in exactly the quantity wanted.</p><p>Food grade mineral salts are dosed into the permeate stream under metered control and blended in a contact vessel. The target profile is calcium for bones and teeth, magnesium for muscle and nerve function, potassium for fluid balance and cardiac rhythm, and a small controlled quantity of sodium for electrolyte balance and taste. Bicarbonate alkalinity is set so that the finished pH sits in a comfortable, slightly alkaline range rather than the mildly acidic range that unmineralised RO water tends towards.</p><p>Because this is a controlled addition rather than whatever the ground happened to provide, every bottle of Pak-Everests carries the same mineral profile in July as it does in January. That consistency is not achievable with simple filtration.</p>',
'Nothing is removed here. Calcium, magnesium, potassium, sodium and bicarbonate alkalinity are added under metered control',1),

(8,'Ultraviolet Sterilisation, Ozonation and Sealed Filling','Final disinfection and tamper evident sealing','uv',
'Ultraviolet light and ozone provide final disinfection, then bottles are washed, filled and sealed inside an enclosed filling room.',
'<p>The final stage is disinfection followed by protected filling. Water passes through an ultraviolet chamber where a 254 nanometre lamp delivers a germicidal dose that disrupts the DNA of any surviving microorganism so it cannot reproduce. Ultraviolet treatment adds nothing to the water and changes neither taste nor chemistry.</p><p>Ozone is then injected as a residual disinfectant. Unlike chlorine, ozone reverts to ordinary oxygen within a few hours, so it protects the water in the sealed bottle during the critical first hours and then simply disappears, leaving no taste and no by-product.</p><p>Filling happens inside an enclosed, positive pressure filling room. Returnable 19 litre bottles are inspected, pre-rinsed, washed internally with food grade caustic solution, rinsed with treated water and sanitised before they reach the filling head. PET bottles are blown, air rinsed and filled in a single enclosed sequence. Every bottle is capped with a tamper evident closure, coded with a batch number and filling date, and released only after the batch has cleared laboratory checks for pH, TDS, turbidity and microbiological safety.</p>',
'Bacteria, viruses, algae, yeasts and moulds, plus post treatment recontamination risk',1);

-- --------------------------------------------------------------------------
--  Minerals
-- --------------------------------------------------------------------------
INSERT INTO `minerals` (`name`,`symbol`,`typical_value`,`unit`,`who_limit`,`psqca_limit`,`benefits`,`details`,`color`,`sort_order`,`is_active`) VALUES
('Calcium','Ca','40 - 60','mg/L','No health based limit','75 max',
'Builds and maintains bone density, supports healthy teeth, enables muscle contraction and normal blood clotting.',
'<p>Calcium is the most abundant mineral in the human body and roughly ninety nine percent of it is stored in bone and teeth. The remaining one percent circulates and does critical work: it triggers muscle contraction, it is required for nerve signal transmission, and it is essential to the blood clotting cascade.</p><p>Drinking water is a genuinely useful source of calcium because the calcium dissolved in water is highly bioavailable, meaning the body absorbs it readily without the digestive competition that comes with some food sources. For children who are still building bone mass, and for older adults losing it, a steady daily contribution from drinking water is meaningful.</p><p>Pak-Everests targets 40 to 60 mg per litre of calcium. That level contributes usefully to daily intake and gives the water a rounded mouthfeel, without pushing hardness to the point where it leaves scale in a kettle.</p>','#3aa7d9',1,1),

('Magnesium','Mg','12 - 25','mg/L','No health based limit','50 max',
'Supports over three hundred enzyme reactions, relaxes muscles, steadies heart rhythm and aids restful sleep.',
'<p>Magnesium is a cofactor in more than three hundred enzyme systems. It governs energy production at the cellular level, protein synthesis, blood glucose control and blood pressure regulation. Where calcium signals a muscle to contract, magnesium signals it to relax, and the balance between the two is what produces smooth, controlled movement and a steady heartbeat.</p><p>Magnesium deficiency is common and quietly disruptive. Night cramps, eyelid twitching, restlessness and poor sleep quality are all associated with low magnesium status. Because magnesium in water is dissolved in ionic form, absorption is efficient.</p><p>Our target of 12 to 25 mg per litre provides a steady daily contribution alongside dietary sources such as nuts, seeds and leafy greens.</p>','#2fbf9c',2,1),

('Sodium','Na','8 - 20','mg/L','200 max','200 max',
'Maintains fluid balance, supports nerve signal transmission and replaces electrolytes lost through sweat.',
'<p>Sodium has a poor reputation, and that reputation comes almost entirely from processed food, not from drinking water. The body genuinely needs sodium. It is the principal ion in extracellular fluid, it governs how much water the body retains, and it carries the electrical charge that makes nerve transmission possible.</p><p>In a hot climate like the Potohar summer, sodium lost through sweat needs replacing. A person working outdoors through a Gujar Khan July loses a substantial quantity of sodium each day.</p><p>Pak-Everests keeps sodium deliberately low at 8 to 20 mg per litre, far below the 200 mg per litre ceiling set by both the World Health Organization guideline value and PSQCA. That level supports electrolyte balance and gives the water a clean taste without contributing meaningfully to total dietary sodium intake, which makes it appropriate for people managing blood pressure.</p>','#f0a92b',3,1),

('Potassium','K','2 - 8','mg/L','No health based limit','12 max',
'Regulates blood pressure, supports heart rhythm, aids kidney function and works with sodium to balance body fluids.',
'<p>Potassium is the counterweight to sodium. Where sodium pulls water into the extracellular space and raises blood pressure, potassium promotes sodium excretion through the kidneys and helps bring blood pressure down. The ratio between the two matters more than either figure alone.</p><p>Potassium is also central to cardiac electrical activity. The heart depends on a precise potassium gradient across cell membranes to maintain rhythm, which is why serious potassium imbalance is a medical emergency.</p><p>We include 2 to 8 mg per litre in the finished water. It is a modest contribution against a daily requirement met mainly through fruit and vegetables, but it supports the overall electrolyte balance that makes mineral water more useful than plain purified water.</p>','#8f6fd6',4,1),

('Bicarbonate Alkalinity','HCO3','60 - 120','mg/L','No health based limit','Not specified',
'Buffers acidity, keeps pH stable and slightly alkaline, and supports comfortable digestion.',
'<p>Bicarbonate is what makes water taste soft and slightly sweet rather than sharp. Chemically it is a buffer, meaning it resists changes in pH. In the body it helps neutralise excess stomach acidity and supports the blood buffering system that keeps physiological pH within its very narrow safe range.</p><p>Reverse osmosis water without bicarbonate tends to sit slightly on the acidic side of neutral and tastes flat. By setting bicarbonate alkalinity at 60 to 120 mg per litre we bring the finished pH to a comfortable 7.2 to 7.8 and give the water body on the palate.</p>','#5eb8e8',5,1),

('Total Dissolved Solids','TDS','120 - 200','mg/L','Below 600 desirable','1000 max',
'The overall mineral content of the water, kept in the range that tastes best and hydrates well.',
'<p>Total dissolved solids is the sum of everything dissolved in the water, mainly the minerals discussed above. It is the single most useful indicator of water character.</p><p>Water below about 50 mg per litre TDS tastes flat and empty, which is the common complaint about pure reverse osmosis water sold without re-mineralisation. Water above roughly 500 mg per litre begins to taste heavy and mineral laden, and above 1000 mg per litre it becomes unpleasant.</p><p>Pak-Everests targets 120 to 200 mg per litre. Water tasting panels consistently rate this band highest, and it is where the mineral contribution is genuinely useful without the water becoming heavy. Every batch is checked against this range before release.</p>','#0b6fa4',6,1),

('pH Value','pH','7.2 - 7.8','pH','6.5 - 8.5','6.5 - 8.5',
'Slightly alkaline and gentle on the stomach, in the range that tastes cleanest.',
'<p>pH measures how acidic or alkaline water is on a scale from 0 to 14, with 7 being neutral. Both the World Health Organization and PSQCA accept a range of 6.5 to 8.5 for drinking water.</p><p>Pak-Everests targets a narrower band of 7.2 to 7.8, which sits just on the alkaline side of neutral. This is where water tastes cleanest, where it is gentlest on the stomach, and where it is least aggressive towards plumbing and storage vessels. The bicarbonate alkalinity added at stage seven is what holds the pH steady in this range rather than letting it drift.</p>','#2fbf9c',7,1);

-- --------------------------------------------------------------------------
--  FAQs
-- --------------------------------------------------------------------------
INSERT INTO `faqs` (`question`,`answer`,`category`,`show_on_home`,`sort_order`,`is_active`,`created_at`) VALUES
('What is the price of a 19 litre water bottle from Pak-Everests?',
'<p>A 19 litre refill costs <strong>Rs 250</strong> and delivery is completely free across our entire coverage area. On your very first order you also pay a <strong>one time refundable security deposit of Rs 1,500 per bottle</strong>, which covers the bottle itself. From your second delivery onwards you only pay Rs 250 for each refill and simply hand over the empty bottle. When you stop the service, return the bottles in a usable condition and the full Rs 1,500 per bottle is refunded to you.</p>',
'Pricing',1,1,1,NOW()),

('Which areas does Pak-Everests deliver to, and is delivery really free?',
'<p>Yes, delivery is genuinely free with no minimum order. We deliver across <strong>Gujar Khan, Mandra, Daultala, Bewal, Habib Chowk, Kallar Syedan, Rawat, Adiala Road, Bahria Town Rawalpindi, DHA Islamabad and the wider Rawalpindi region</strong>. Orders placed before 4:00 PM on a daily route area are normally delivered the same day, and other areas are covered on scheduled alternate day routes. If your address is just outside the listed areas, send it to us on WhatsApp and we will confirm whether our route can reach you.</p>',
'Delivery',1,2,1,NOW()),

('Is Pak-Everests water approved and safe to drink?',
'<p>Pak-Everests operates as a <strong>Punjab Food Authority approved</strong> bottled drinking water plant and follows PSQCA drinking water requirements. Every batch passes through our <strong>eight stage purification process</strong> and is tested in our in-house laboratory for pH, total dissolved solids, turbidity and microbiological safety before it is released for delivery. Bottles carry a batch number and filling date so any pack can be traced back to its production record. Our licence and certification documents are published on the documents page of this website.</p>',
'Quality',1,3,1,NOW()),

('What is the difference between the Pure and Mix packs?',
'<p>The <strong>water inside both packs is identical</strong>. Both come off the same eight stage purification line, both are re-mineralised to the same calcium, magnesium, potassium and sodium profile, and both are tested to the same standard. The difference is the packaging grade. <strong>Pure</strong> packs use a heavier premium PET preform with higher clarity and a stronger cap ring, which suits boardrooms, hotels, events and custom branded labels. <strong>Mix</strong> packs use our standard commercial preform, which brings the price down for households, canteens, shops and everyday use.</p>',
'Products',1,4,1,NOW()),

('Can I get water bottles printed with my own company or event label?',
'<p>Yes. Custom label printing is one of our most requested services. We print branded labels on <strong>500 ml and 1.5 litre bottles</strong>, and the 350 ml bottle will join the range at launch. Companies use it for offices, reception areas and corporate gifting, and families use it for weddings, aqiqah, mehndi and other events. Send us your logo and brand colours through the <a href="/custom-label-request">label customisation form</a>, or ask our design team to create the artwork for you. We share a digital proof for your approval before anything goes to print.</p>',
'Custom Labels',1,5,1,NOW()),

('How do I place an order?',
'<p>There are three ways. Fill in the <a href="/order">online order form</a> and it reaches our admin panel and our WhatsApp instantly. Send a WhatsApp message directly to <strong>0333 5592206</strong> or <strong>0332 2901309</strong>. Or email <strong>info@pakeverests.site</strong>. Tell us your name, complete address, the products you want and any delivery instruction, and our team will confirm the delivery slot by phone or WhatsApp.</p>',
'Orders',0,6,1,NOW()),

('What payment methods do you accept?',
'<p>Cash on delivery is accepted everywhere we deliver and is what most customers use. For advance or online payment we accept <strong>EasyPaisa, JazzCash and direct bank transfer</strong>. Full account titles and numbers are listed on our contact page. If you pay in advance, please send the transaction screenshot to our WhatsApp so we can match it to your order immediately.</p>',
'Payment',0,7,1,NOW()),

('Do you supply water dispensers?',
'<p>Yes, on demand. You can <strong>buy a hot and cold dispenser outright for Rs 42,000</strong>, or <strong>rent one for Rs 2,000 per month</strong> with routine servicing and sanitisation included. Delivery, installation and a full demonstration are free in both cases. Rented units remain the property of Pak-Everests and are covered by a dispenser agreement issued at installation.</p>',
'Products',0,8,1,NOW()),

('How much does bulk water filling cost at your plant?',
'<p>Facility filling is charged at a flat <strong>Rs 6 per litre</strong> with no minimum and no maximum volume. Bring a clean, food grade container to our Gujar Khan plant, our staff will inspect and rinse it, and filling is metered so you pay for exactly what you take. Use the <a href="/bulk-water-calculator">bulk water calculator</a> to work out your cost for any daily or monthly requirement. Please note this is a collection service at the plant and free delivery does not apply to it.</p>',
'Pricing',0,9,1,NOW()),

('How can I become a Pak-Everests distributor?',
'<p>We appoint area distributors across the Potohar region and the wider Rawalpindi and Islamabad market. Submit the <a href="/distributor-application">distributor application form</a> with details of your area, storage capacity, delivery vehicle and target volume. Our team reviews every application, meets shortlisted applicants, and issues a formal distributor agreement setting out territory, pricing, security and targets. Full terms are on the <a href="/distribution">distribution page</a>.</p>',
'Distribution',0,10,1,NOW()),

('How long does bottled water stay fresh?',
'<p>Sealed 19 litre and 12 litre bottles carry a <strong>six month shelf life</strong> from the printed filling date, and sealed PET packs carry <strong>nine months</strong>. Keep bottles out of direct sunlight, away from heat sources, and away from strong smelling items such as detergents, paint and fuel, because PET and polycarbonate can pick up odours through the wall over time. Once a bottle is opened, use it within three to four days for the best taste.</p>',
'Quality',0,11,1,NOW()),

('What happens if a bottle or dispenser is damaged?',
'<p>Normal wear from ordinary use is expected and is never charged to you. Deductions apply only where a bottle or dispenser is lost, cracked, burnt, chemically contaminated or damaged through clear misuse. Every scenario, the applicable deduction and the dispute process are set out in full in our <a href="/damage-policy">bottle and equipment damage policy</a>. If you disagree with any assessment, our <a href="/dispute-resolution">dispute resolution process</a> gives you a formal route to have it reviewed.</p>',
'Policies',0,12,1,NOW());

-- --------------------------------------------------------------------------
--  Reviews and testimonials
-- --------------------------------------------------------------------------
INSERT INTO `reviews` (`product_id`,`reviewer_name`,`reviewer_role`,`location`,`rating`,`title`,`body`,`is_featured`,`is_verified`,`status`,`created_at`) VALUES
(1,'Muhammad Asif Raja','Home Customer','Gujar Khan',5,'Best water plant in Gujar Khan',
'We have been taking the 19 litre bottle for almost two years now. The delivery man never misses a day and the water taste is much better than the other plants we tried before. The deposit system is fair and the receipt is always given properly.',1,1,'approved',DATE_SUB(NOW(), INTERVAL 41 DAY)),
(1,'Sadia Kanwal','School Administrator','Kallar Syedan',5,'Reliable supply for our school',
'We run a school with more than four hundred students and we needed a supplier who does not disappear in summer. Pak-Everests has kept our dispensers filled every single week without one complaint. Their team is polite and the invoicing is clean.',1,1,'approved',DATE_SUB(NOW(), INTERVAL 35 DAY)),
(6,'Hassan Mehmood','Event Manager','Bahria Town Rawalpindi',5,'Custom labels looked excellent',
'We ordered five hundred bottles with the couple name printed for a wedding at a marquee in Bahria Town. The design team sent us three options and the printing quality was sharp. Guests actually took bottles home as a keepsake.',1,1,'approved',DATE_SUB(NOW(), INTERVAL 28 DAY)),
(11,'Dr. Faizan Ahmed','Clinic Owner','Rawat',5,'Dispenser rental made sense for us',
'Renting the dispenser at two thousand a month was the right decision for a new clinic. They installed it, showed the staff how to use it and they come for cleaning on schedule. Water quality is consistent.',1,1,'approved',DATE_SUB(NOW(), INTERVAL 22 DAY)),
(4,'Nadia Iqbal','Home Customer','DHA Islamabad',5,'Clean taste, no plastic smell',
'I am very particular about water taste and most bottled water in this range has a plastic smell. This does not. The one and a half litre pure pack has become our regular order for the dining table.',1,1,'approved',DATE_SUB(NOW(), INTERVAL 19 DAY)),
(1,'Ch. Zulfiqar Ali','Shop Owner','Mandra',5,'Good margins and fast supply',
'I keep their stock at my general store. Supply never stops and the rates leave a reasonable margin for a shopkeeper. Customers ask for it by name now which was not happening with the previous brand.',0,1,'approved',DATE_SUB(NOW(), INTERVAL 17 DAY)),
(12,'Imran Shahzad','Caterer','Gujar Khan',5,'Six rupees per litre is unbeatable',
'For catering work the bulk filling counter saves me a serious amount every month. Staff check the containers properly which I respect, and the metering is honest.',0,1,'approved',DATE_SUB(NOW(), INTERVAL 15 DAY)),
(2,'Rukhsana Bibi','Home Customer','Daultala',5,'Twelve litre is easy for me to lift',
'The nineteen litre was too heavy for me at my age. The twelve litre bottle is perfect and the price is very reasonable. Delivery boy always carries it inside the house for me.',0,1,'approved',DATE_SUB(NOW(), INTERVAL 13 DAY)),
(7,'Bilal Tariq','Site Supervisor','Adiala Road',4,'Good for site workers',
'We order the five hundred ml cartons for the labour on site. Sealed bottles mean no sharing of glasses which is exactly what we wanted. Delivery on time every week.',0,1,'approved',DATE_SUB(NOW(), INTERVAL 11 DAY)),
(NULL,'Ayesha Malik','Office Manager','Rawalpindi Cantt',5,'Switched our whole office over',
'We moved our office of thirty two people to Pak-Everests after a bad experience with another supplier. Six months in and there has not been a single missed delivery. The WhatsApp ordering is genuinely convenient.',1,1,'approved',DATE_SUB(NOW(), INTERVAL 9 DAY)),
(NULL,'Hafiz Abdul Rehman','Mosque Committee','Bewal',5,'They support our Friday gatherings',
'Every Friday we need a large quantity of water and they have never let us down. They understand the timing pressure and always deliver early rather than late.',0,1,'approved',DATE_SUB(NOW(), INTERVAL 7 DAY)),
(5,'Usman Ghani','Canteen Contractor','Habib Chowk',4,'Value pack works for canteen',
'The mix pack at three hundred fifty is what makes the canteen numbers work. Same water as the expensive pack, just simpler bottle. Honest product.',0,1,'approved',DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3,'Saima Noreen','Home Customer','Gujar Khan',5,'Six litre fits my fridge perfectly',
'I buy two six litre bottles every week. They fit in the fridge door and I can lift them without help. Very convenient size and the price is fair.',0,1,'approved',DATE_SUB(NOW(), INTERVAL 3 DAY)),
(NULL,'Tanveer Abbas','Distributor','Rawat',5,'Professional to work with',
'I have been an area distributor for over a year. The agreement was clear from day one, territory is respected and payments are handled properly. That is rare in this business.',1,1,'approved',DATE_SUB(NOW(), INTERVAL 2 DAY));

-- --------------------------------------------------------------------------
--  News ticker
-- --------------------------------------------------------------------------
INSERT INTO `news_ticker` (`text`,`link`,`icon`,`highlight`,`is_active`,`sort_order`,`created_at`) VALUES
('Free home delivery on every 19 litre refill across Gujar Khan, Rawalpindi and Islamabad','/coverage-areas','truck',1,1,1,NOW()),
('19 litre refill only Rs 250 with a refundable Rs 1,500 bottle deposit','/product/19-litre-refill-bottle','drop',0,1,2,NOW()),
('Punjab Food Authority approved plant with a full eight stage purification process','/purification-process','shield',0,1,3,NOW()),
('Custom printed label bottles now available for weddings, offices and events','/custom-label-bottles','label',1,1,4,NOW()),
('Water dispensers on rent at just Rs 2,000 per month with free installation','/product/water-dispenser','cool',0,1,5,NOW()),
('350 ml pack of 24, glass bottles and water pouches launching soon','/products','star',0,1,6,NOW()),
('Area distributors wanted across the Potohar region. Apply online today','/distribution','handshake',1,1,7,NOW());

-- --------------------------------------------------------------------------
--  Ad slots (all disabled until the owner pastes real codes)
-- --------------------------------------------------------------------------
INSERT INTO `ad_slots` (`name`,`network`,`placement`,`code`,`is_active`,`sort_order`,`created_at`) VALUES
('Header Banner','adsense','header','',0,1,NOW()),
('In Content Top','adsense','content_top','',0,2,NOW()),
('In Content Middle','adsense','content_middle','',0,3,NOW()),
('Sidebar','adsense','sidebar','',0,4,NOW()),
('Footer Banner','adsense','footer','',0,5,NOW()),
('Adsterra Social Bar','adsterra','social_bar','',0,6,NOW()),
('Adsterra Popunder','adsterra','popunder','',0,7,NOW()),
('Monetag Push','monetag','push','',0,8,NOW());

-- --------------------------------------------------------------------------
--  Job openings
-- --------------------------------------------------------------------------
INSERT INTO `job_openings` (`slug`,`title`,`department`,`location`,`job_type`,`salary_range`,`experience`,`description`,`requirements`,`positions`,`is_active`,`sort_order`,`created_at`) VALUES
('delivery-rider','Delivery Rider','Distribution','Gujar Khan / Rawalpindi','Full Time','Rs 35,000 - Rs 50,000 plus fuel','1 year preferred',
'<p>Deliver bottled water on an assigned daily route, collect payment and empty bottles, and represent Pak-Everests at every customer door. Route lists are issued each morning and settled each evening.</p>',
'Valid driving licence
Knowledge of Gujar Khan and Rawalpindi routes
Physically able to carry 19 litre bottles
Polite, presentable and punctual
CNIC and two references required',3,1,1,NOW()),
('plant-operator','Water Plant Operator','Production','Gujar Khan','Full Time','Rs 40,000 - Rs 60,000','2 years in a filtration or bottling plant',
'<p>Operate and monitor the eight stage purification line, record batch parameters, manage filter backwash and membrane cleaning schedules, and maintain the plant hygiene log.</p>',
'Diploma or experience in water treatment
Understanding of RO, UV and ozone systems
Able to maintain accurate production records
Willing to work rotating shifts',2,1,2,NOW()),
('sales-officer','Sales and Distribution Officer','Sales','Rawalpindi / Islamabad','Full Time','Rs 45,000 plus commission','2 years FMCG field sales',
'<p>Develop new retail, corporate and institutional accounts across the Rawalpindi and Islamabad market, manage distributor relationships and hit monthly volume targets.</p>',
'FMCG field sales experience
Own motorcycle or car
Strong local market knowledge
Comfortable with daily reporting',2,1,3,NOW()),
('customer-support-executive','Customer Support Executive','Customer Service','Gujar Khan','Full Time','Rs 30,000 - Rs 40,000','Fresh candidates may apply',
'<p>Handle WhatsApp and phone orders, coordinate delivery routes with riders, resolve customer complaints and maintain the order records in the admin panel.</p>',
'Good spoken Urdu and basic English
Comfortable with WhatsApp and computer use
Patient and organised
Intermediate or above',2,1,4,NOW());

-- --------------------------------------------------------------------------
--  Gallery placeholders (replace the image paths after uploading WebP files)
-- --------------------------------------------------------------------------
INSERT INTO `gallery` (`title`,`caption`,`image_path`,`alt_text`,`category`,`sort_order`,`is_active`,`created_at`) VALUES
('Reverse Osmosis Membrane Bank','The heart of our eight stage line, where dissolved salts and heavy metals are rejected.','','Reverse osmosis membrane bank at the Pak-Everests water plant in Gujar Khan','Plant',1,1,NOW()),
('Automated Bottle Filling Line','Bottles are filled and capped inside an enclosed positive pressure filling room.','','Automated bottle filling line at Pak-Everests','Plant',2,1,NOW()),
('19 Litre Bottle Washing Station','Every returned bottle is inspected, scrubbed, rinsed and sanitised before refilling.','','19 litre bottle washing and sanitising station','Plant',3,1,NOW()),
('In-House Quality Laboratory','Every batch is tested for pH, TDS, turbidity and microbiological safety before release.','','In-house water quality testing laboratory','Quality',4,1,NOW()),
('Delivery Fleet','Our delivery vehicles cover Gujar Khan, Rawalpindi and Islamabad every day.','','Pak-Everests water delivery fleet','Delivery',5,1,NOW()),
('Custom Label Bottles','Branded 500 ml bottles printed for a corporate client.','','Custom printed label water bottles for corporate branding','Custom Labels',6,1,NOW()),
('Corporate Office Supply','A client office running Pak-Everests dispensers on a weekly standing order.','','Corporate office water dispenser supply','Customers',7,1,NOW()),
('Warehouse and Dispatch','Finished stock staged for the morning delivery routes.','','Warehouse and dispatch area at the Pak-Everests plant','Plant',8,1,NOW());

-- --------------------------------------------------------------------------
--  Documents placeholders (upload the real files from the admin panel)
-- --------------------------------------------------------------------------
INSERT INTO `documents` (`title`,`description`,`category`,`doc_type`,`file_path`,`issued_by`,`reference_no`,`is_public`,`sort_order`,`created_at`) VALUES
('Punjab Food Authority Licence','Our operating licence as an approved bottled drinking water establishment for the Potohar region.','Licences','image','','Punjab Food Authority','PFA/RWP/GK/PE-2024',1,1,NOW()),
('Water Quality Test Report','The most recent independent laboratory analysis covering physical, chemical and microbiological parameters.','Test Reports','pdf','','Accredited Laboratory','',1,2,NOW()),
('Sample Water Delivery Agreement','The standard agreement issued to households and offices taking a regular supply, including bottle deposit terms.','Sample Agreements','pdf','','Pak-Everests','',1,3,NOW()),
('Sample Distributor Agreement','The standard territory, pricing, security and target terms issued to appointed area distributors.','Sample Agreements','pdf','','Pak-Everests','',1,4,NOW()),
('Sample Quotation Format','The quotation format issued to corporate, institutional and bulk buyers.','Quotations','pdf','','Pak-Everests','',1,5,NOW());

-- --------------------------------------------------------------------------
--  Sync product star ratings with the real approved reviews above.
--  Ratings shown on the website and in Google product schema must reflect
--  reviews that genuinely exist, never a marketing figure. As you approve new
--  reviews in the admin panel these numbers update automatically.
-- --------------------------------------------------------------------------
UPDATE `products` p
LEFT JOIN (
    SELECT product_id, ROUND(AVG(rating), 2) AS avg_rating, COUNT(*) AS total
    FROM `reviews` WHERE status = 'approved' AND product_id IS NOT NULL
    GROUP BY product_id
) r ON r.product_id = p.id
SET p.rating       = COALESCE(r.avg_rating, 5.00),
    p.rating_count = COALESCE(r.total, 0);
