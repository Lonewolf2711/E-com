-- ═══════════════════════════════════════════════════════════════
-- ECOMMERCE CMS — Seed Data
-- Admin user, sample categories, sample products, default settings
-- ═══════════════════════════════════════════════════════════════

-- ─── Admin User ───
-- Email: admin@ecommerce.com
-- Password: Admin@123 (bcrypt hashed)
INSERT INTO users (name, email, password, phone, role, status) VALUES
('Admin', 'admin@ecommerce.com', '$2y$12$A81F4ZamtSF.OsdXlYNOGuysSprwRC9fOkkWTe4ZUBTv517bDgXG.', '+91 9876543210', 'admin', 'active');

-- ─── Sample Customer ───
-- Email: customer@example.com
-- Password: Customer@123
INSERT INTO users (name, email, password, phone, role, status) VALUES
('John Doe', 'customer@example.com', '$2y$12$A81F4ZamtSF.OsdXlYNOGuysSprwRC9fOkkWTe4ZUBTv517bDgXG.', '+91 9876543211', 'customer', 'active');

-- ─── Categories ───
INSERT INTO categories (name, slug, parent_id, status) VALUES
('Electronics & Computers', 'electronics-computers', NULL, 'active'),
('Mobiles & Tablets', 'mobiles-tablets', NULL, 'active'),
('Accessories', 'accessories', NULL, 'active');

-- Sub-categories
INSERT INTO categories (name, slug, parent_id, status) VALUES
('Laptops', 'laptops', 1, 'active'),
('Desktops', 'desktops', 1, 'active'),
('Smartphones', 'smartphones', 2, 'active');

-- ─── Sample Products ───
INSERT INTO products (name, slug, sku, description, price, sale_price, stock, low_stock_threshold, image, category_id, status, is_featured, meta_title, meta_description) VALUES
(
    'Apple MacBook Air M2',
    'apple-macbook-air-m2',
    'ELEC-MBA-M2-001',
    'The MacBook Air M2 delivers incredible performance in an impossibly thin design. With up to 18 hours of battery life, a stunning 13.6-inch Liquid Retina display, and the power of the M2 chip, this laptop handles everything from everyday tasks to intensive creative work.',
    119990.00,
    109990.00,
    25,
    5,
    'products/macbook-air-m2.jpg',
    4,
    'active',
    1,
    'Apple MacBook Air M2 - Buy Online',
    'Shop the Apple MacBook Air M2 with M2 chip, 13.6-inch display, and all-day battery life.'
),
(
    'Samsung Galaxy S24 Ultra',
    'samsung-galaxy-s24-ultra',
    'MOB-SGS24U-001',
    'Experience the ultimate smartphone with Galaxy S24 Ultra. Featuring a 6.8-inch Dynamic AMOLED 2X display, 200MP camera system, S Pen integration, and the latest Snapdragon processor for unmatched performance.',
    134999.00,
    129999.00,
    40,
    10,
    'products/galaxy-s24-ultra.jpg',
    6,
    'active',
    1,
    'Samsung Galaxy S24 Ultra - Best Price',
    'Buy Samsung Galaxy S24 Ultra with 200MP camera, S Pen, and 6.8-inch display.'
),
(
    'Sony WH-1000XM5 Headphones',
    'sony-wh-1000xm5-headphones',
    'ACC-SNYWH-XM5-001',
    'Industry-leading noise cancellation with the Sony WH-1000XM5. Enjoy premium sound quality, 30-hour battery life, and ultra-comfortable design. Perfect for music lovers and frequent travelers.',
    29990.00,
    24990.00,
    60,
    10,
    'products/sony-xm5.jpg',
    3,
    'active',
    1,
    'Sony WH-1000XM5 Wireless Headphones',
    'Best noise-cancelling headphones with 30-hour battery and premium sound.'
),
(
    'Apple iPad Pro 12.9-inch',
    'apple-ipad-pro-12-9',
    'MOB-IPDP-129-001',
    'The ultimate iPad experience with the M2 chip, stunning 12.9-inch Liquid Retina XDR display, blazing fast performance, and all-day battery life. Perfect for creative professionals.',
    112900.00,
    NULL,
    30,
    5,
    'products/ipad-pro.jpg',
    2,
    'active',
    1,
    'Apple iPad Pro 12.9-inch M2 - Shop Now',
    'Apple iPad Pro with M2 chip, 12.9-inch XDR display, and pro-level performance.'
),
(
    'Dell XPS 15 Laptop',
    'dell-xps-15-laptop',
    'ELEC-DXPS15-001',
    'The Dell XPS 15 combines stunning design with powerful performance. Features a 15.6-inch 4K OLED display, Intel Core i9 processor, 32GB RAM, and 1TB SSD for demanding workloads.',
    189990.00,
    174990.00,
    15,
    3,
    'products/dell-xps-15.jpg',
    4,
    'active',
    0,
    'Dell XPS 15 4K OLED Laptop',
    'Premium Dell XPS 15 with 4K OLED display, Intel Core i9, and 32GB RAM.'
),
(
    'Logitech MX Master 3S Mouse',
    'logitech-mx-master-3s',
    'ACC-LGMX3S-001',
    'The most advanced ergonomic mouse for productivity. Features MagSpeed scroll wheel, quiet clicks, 8K DPI sensor, and seamless multi-device connectivity via Bluetooth and USB receiver.',
    9995.00,
    8495.00,
    100,
    15,
    'products/mx-master-3s.jpg',
    3,
    'active',
    1,
    'Logitech MX Master 3S Wireless Mouse',
    'Premium ergonomic wireless mouse with MagSpeed scroll and multi-device support.'
);

-- ─── Product Attributes ───
INSERT INTO product_attributes (product_id, attribute_name, attribute_value) VALUES
(1, 'Processor', 'Apple M2'),
(1, 'RAM', '8GB'),
(1, 'Storage', '256GB SSD'),
(1, 'Display', '13.6-inch Liquid Retina'),
(1, 'Color', 'Midnight'),
(2, 'Processor', 'Snapdragon 8 Gen 3'),
(2, 'RAM', '12GB'),
(2, 'Storage', '256GB'),
(2, 'Display', '6.8-inch Dynamic AMOLED 2X'),
(2, 'Color', 'Titanium Black'),
(3, 'Type', 'Over-ear Wireless'),
(3, 'Battery', '30 hours'),
(3, 'Noise Cancellation', 'Yes - Industry Leading'),
(3, 'Color', 'Black'),
(4, 'Processor', 'Apple M2'),
(4, 'Display', '12.9-inch Liquid Retina XDR'),
(4, 'Storage', '128GB'),
(4, 'Color', 'Space Gray'),
(5, 'Processor', 'Intel Core i9-13900H'),
(5, 'RAM', '32GB'),
(5, 'Storage', '1TB SSD'),
(5, 'Display', '15.6-inch 4K OLED'),
(6, 'Connectivity', 'Bluetooth + USB Receiver'),
(6, 'DPI', '8000'),
(6, 'Battery', '70 days');

-- ─── Default Settings ───
INSERT INTO settings (setting_key, setting_value, setting_group, label, type) VALUES
('general_store_name', 'Electro Store', 'general', 'Store Name', 'text'),
('general_tagline', 'Your One-Stop Electronics Shop', 'general', 'Tagline', 'text'),
('general_email', 'info@electrostore.com', 'general', 'Email', 'text'),
('general_phone', '+91 9876543210', 'general', 'Phone', 'text'),
('general_address', '123 Electronics Street, Tech City, India', 'general', 'Address', 'textarea'),
('general_currency', 'INR', 'general', 'Currency', 'text'),
('general_currency_symbol', '₹', 'general', 'Currency Symbol', 'text'),
('general_timezone', 'Asia/Kolkata', 'general', 'Timezone', 'text'),
('general_logo', '', 'general', 'Logo', 'text'),
('general_favicon', '', 'general', 'Favicon', 'text'),
('payment_razorpay_enabled', '1', 'payment', 'Enable Razorpay', 'boolean'),
('payment_stripe_enabled', '0', 'payment', 'Enable Stripe', 'boolean'),
('payment_paypal_enabled', '0', 'payment', 'Enable PayPal', 'boolean'),
('payment_cod_enabled', '1', 'payment', 'Enable COD', 'boolean'),
('payment_mode', 'test', 'payment', 'Payment Mode (test/live)', 'text'),
('shipping_free_threshold', '500', 'shipping', 'Free Shipping Above', 'text'),
('shipping_default_cost', '60', 'shipping', 'Default Shipping Cost', 'text'),
('seo_default_title', '{page} | Electro Store', 'seo', 'Default Title Format', 'text'),
('seo_default_description', 'Shop the best electronics at Electro Store. Laptops, Smartphones, Accessories and more.', 'seo', 'Default Meta Description', 'textarea'),
('seo_google_analytics', '', 'seo', 'Google Analytics ID', 'text'),
('email_smtp_host', 'smtp.hostinger.com', 'email', 'SMTP Host', 'text'),
('email_smtp_port', '465', 'email', 'SMTP Port', 'text'),
('email_from_name', 'Electro Store', 'email', 'From Name', 'text'),
('email_from_email', 'noreply@electrostore.com', 'email', 'From Email', 'text');