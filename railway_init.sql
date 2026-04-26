-- ═══════════════════════════════════════════════════════════════
-- ECOMMERCE CMS — Complete Database Schema
-- 18 Tables · InnoDB · utf8mb4 · Foreign Keys
-- Source of truth: ecommerce_architecture.txt Section 3
-- ═══════════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables (if re-importing)
DROP TABLE IF EXISTS wishlists;
DROP TABLE IF EXISTS seo_meta;
DROP TABLE IF EXISTS inventory_logs;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_tracking;
DROP TABLE IF EXISTS order_addresses;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS product_attributes;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;

-- ═══════════════════════════════════════
-- 1. USERS
-- ═══════════════════════════════════════
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'bcrypt hashed',
    phone VARCHAR(20) DEFAULT NULL,
    role ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    status ENUM('active', 'banned') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 2. CATEGORIES
-- ═══════════════════════════════════════
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE COMMENT 'URL slug',
    parent_id INT DEFAULT NULL COMMENT 'Self-referencing FK for sub-categories',
    image VARCHAR(255) DEFAULT NULL COMMENT 'Category banner image path',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 3. PRODUCTS
-- ═══════════════════════════════════════
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE COMMENT 'SEO URL slug',
    sku VARCHAR(100) UNIQUE COMMENT 'Stock keeping unit',
    description TEXT,
    short_description VARCHAR(500) DEFAULT NULL COMMENT 'Brief description for listings',
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) DEFAULT NULL COMMENT 'If set, displayed as discounted price',
    stock INT NOT NULL DEFAULT 0,
    low_stock_threshold INT NOT NULL DEFAULT 5 COMMENT 'Alert when stock falls below this',
    image VARCHAR(255) DEFAULT NULL COMMENT 'Primary image',
    category_id INT DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    is_featured TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Homepage featured flag',
    meta_title VARCHAR(255) DEFAULT NULL COMMENT 'SEO meta title',
    meta_description VARCHAR(500) DEFAULT NULL COMMENT 'SEO meta description',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 4. PRODUCT ATTRIBUTES
-- ═══════════════════════════════════════
CREATE TABLE product_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    attribute_name VARCHAR(100) NOT NULL COMMENT 'e.g. Color, Size, RAM',
    attribute_value VARCHAR(100) NOT NULL COMMENT 'e.g. Red, XL, 8GB',
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 5. PRODUCT IMAGES
-- ═══════════════════════════════════════
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL COMMENT 'Additional gallery image',
    sort_order INT NOT NULL DEFAULT 0 COMMENT 'Display order',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 6. COUPONS
-- ═══════════════════════════════════════
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('percent', 'flat') NOT NULL COMMENT 'percent = % off, flat = fixed amount off',
    discount_value DECIMAL(10,2) NOT NULL,
    min_order_amount DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Min cart value to apply',
    max_discount DECIMAL(10,2) DEFAULT NULL COMMENT 'Max discount cap for percent type',
    max_uses INT NOT NULL DEFAULT 0 COMMENT '0 = unlimited',
    per_user_limit INT NOT NULL DEFAULT 1 COMMENT 'Max uses per user',
    used_count INT NOT NULL DEFAULT 0,
    expiry_date DATE NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 7. CARTS
-- ═══════════════════════════════════════
CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL COMMENT 'NULL for guest carts (session-based)',
    session_id VARCHAR(100) DEFAULT NULL COMMENT 'For guest cart identification',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 8. CART ITEMS
-- ═══════════════════════════════════════
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL COMMENT 'Snapshot of price at time of add',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 9. ORDERS
-- ═══════════════════════════════════════
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL COMMENT 'NULL for guest checkout',
    order_number VARCHAR(30) NOT NULL UNIQUE COMMENT 'Human-readable e.g. ORD-20240001',
    subtotal DECIMAL(10,2) NOT NULL COMMENT 'Before discounts',
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Coupon discount applied',
    shipping_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL COMMENT 'Final total including tax/shipping',
    coupon_id INT DEFAULT NULL,
    status ENUM('pending', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL COMMENT 'razorpay, stripe, paypal, cod',
    tracking_id VARCHAR(100) DEFAULT NULL COMMENT 'Courier tracking number',
    courier VARCHAR(100) DEFAULT NULL COMMENT 'Courier company name',
    notes TEXT DEFAULT NULL COMMENT 'Customer order notes',
    shipping_address JSON DEFAULT NULL COMMENT 'Stored address snapshot',
    billing_address JSON DEFAULT NULL COMMENT 'Stored address snapshot',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 10. ORDER ITEMS
-- ═══════════════════════════════════════
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    product_name VARCHAR(200) NOT NULL COMMENT 'Snapshot of name at order time',
    product_image VARCHAR(255) DEFAULT NULL COMMENT 'Snapshot of image',
    price DECIMAL(10,2) NOT NULL COMMENT 'Snapshot of price at order time',
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 11. ORDER ADDRESSES
-- ═══════════════════════════════════════
CREATE TABLE order_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    type ENUM('shipping', 'billing') NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'India',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 12. ORDER TRACKING
-- ═══════════════════════════════════════
CREATE TABLE order_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status VARCHAR(100) NOT NULL COMMENT 'e.g. Pending, Confirmed, Packed, Shipped, Delivered',
    message TEXT DEFAULT NULL COMMENT 'Custom message from admin',
    location VARCHAR(200) DEFAULT NULL COMMENT 'Location during transit',
    created_by INT DEFAULT NULL COMMENT 'Admin who added update',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 13. PAYMENTS
-- ═══════════════════════════════════════
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    method VARCHAR(50) NOT NULL COMMENT 'razorpay, stripe, paypal, upi, cod',
    payment_gateway VARCHAR(50) DEFAULT NULL COMMENT 'Gateway name (matches method)',
    transaction_id VARCHAR(150) DEFAULT NULL COMMENT 'Gateway transaction/payment ID',
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'INR',
    payment_status ENUM('pending', 'success', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    gateway_response JSON DEFAULT NULL COMMENT 'Raw gateway response payload',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 14. REVIEWS
-- ═══════════════════════════════════════
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL COMMENT '1 to 5',
    comment TEXT DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' COMMENT 'Moderated',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 15. INVENTORY LOGS
-- ═══════════════════════════════════════
CREATE TABLE inventory_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    change_amount INT NOT NULL COMMENT 'Positive = stock in, Negative = stock out',
    stock_before INT NOT NULL DEFAULT 0,
    stock_after INT NOT NULL DEFAULT 0,
    type ENUM('sale', 'restock', 'manual', 'return') NOT NULL,
    note TEXT DEFAULT NULL,
    created_by INT DEFAULT NULL COMMENT 'User who made the change',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 16. SETTINGS
-- ═══════════════════════════════════════
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    setting_group VARCHAR(50) NOT NULL DEFAULT 'general' COMMENT 'general, payment, seo, email, shipping',
    label VARCHAR(100) DEFAULT NULL COMMENT 'Human-readable label',
    type VARCHAR(20) NOT NULL DEFAULT 'text' COMMENT 'text, boolean, textarea, select',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 17. SEO META
-- ═══════════════════════════════════════
CREATE TABLE seo_meta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_type VARCHAR(50) NOT NULL COMMENT 'product, category, page, custom',
    page_id INT DEFAULT NULL COMMENT 'FK to respective table',
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description VARCHAR(500) DEFAULT NULL,
    meta_keywords VARCHAR(300) DEFAULT NULL,
    og_title VARCHAR(255) DEFAULT NULL,
    og_description VARCHAR(500) DEFAULT NULL,
    og_image VARCHAR(255) DEFAULT NULL COMMENT 'Open Graph image',
    canonical_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_page (page_type, page_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- 18. WISHLISTS
-- ═══════════════════════════════════════
CREATE TABLE wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════
-- INDEXES
-- ═══════════════════════════════════════
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_products_sku ON products(sku);
CREATE INDEX idx_categories_slug ON categories(slug);
CREATE INDEX idx_categories_parent ON categories(parent_id);
CREATE INDEX idx_categories_status ON categories(status);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created ON orders(created_at);
CREATE INDEX idx_orders_number ON orders(order_number);
CREATE INDEX idx_orders_payment_status ON orders(payment_status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_tracking_order ON order_tracking(order_id);
CREATE INDEX idx_cart_items_cart ON cart_items(cart_id);
CREATE INDEX idx_cart_items_product ON cart_items(product_id);
CREATE INDEX idx_carts_user ON carts(user_id);
CREATE INDEX idx_carts_session ON carts(session_id);
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);
CREATE INDEX idx_reviews_status ON reviews(status);
CREATE INDEX idx_payments_order ON payments(order_id);
CREATE INDEX idx_payments_status ON payments(payment_status);
CREATE INDEX idx_inventory_product ON inventory_logs(product_id);
CREATE INDEX idx_settings_key ON settings(setting_key);
CREATE INDEX idx_settings_group ON settings(setting_group);
CREATE INDEX idx_seo_page ON seo_meta(page_type, page_id);
CREATE INDEX idx_wishlists_user ON wishlists(user_id);

SET FOREIGN_KEY_CHECKS = 1;
-- ═══════════════════════════════════════
-- 19. BANNERS (Homepage Banner Manager)
-- ═══════════════════════════════════════
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position ENUM('hero', 'side_top', 'side_bottom') NOT NULL DEFAULT 'hero' COMMENT 'hero = main slider, side_top = Best Deals block, side_bottom = New Arrivals block',
    title VARCHAR(200) DEFAULT NULL COMMENT 'Main heading text',
    subtitle VARCHAR(200) DEFAULT NULL COMMENT 'Sub-heading or tagline',
    description TEXT DEFAULT NULL COMMENT 'Description paragraph',
    button_text VARCHAR(100) DEFAULT NULL COMMENT 'CTA button label',
    button_link VARCHAR(500) DEFAULT NULL COMMENT 'CTA button URL path (e.g. /shop)',
    image VARCHAR(255) DEFAULT NULL COMMENT 'Banner image path (relative to uploads/)',
    bg_color VARCHAR(50) DEFAULT NULL COMMENT 'Background color or CSS class',
    sort_order INT NOT NULL DEFAULT 0 COMMENT 'Display order (lower = first)',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index for quick lookups
CREATE INDEX idx_banners_position ON banners(position);
CREATE INDEX idx_banners_status ON banners(status);
CREATE INDEX idx_banners_sort ON banners(sort_order);

-- ═══════════════════════════════════════
-- Default banners (matching current hardcoded content)
-- ═══════════════════════════════════════
INSERT INTO banners (position, title, subtitle, description, button_text, button_link, bg_color, sort_order, status) VALUES
('hero', 'Welcome to Our Store', 'Fresh & Quality', 'Discover fresh groceries, daily essentials, and premium products at the best prices.', 'Shop Now', '/shop', 'bg-info', 1, 'active'),
('side_top', 'Trending Products', 'Best Deals', NULL, 'Shop Collection', '/shop', 'bg-success-subtle', 1, 'active'),
('side_bottom', 'Fresh Collection', 'New Arrivals', NULL, 'Shop Collection', '/shop?sort=newest', 'bg-danger', 1, 'active');
CREATE TABLE IF NOT EXISTS enquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  enquiry_number VARCHAR(20) UNIQUE NOT NULL,
  customer_name VARCHAR(150) NOT NULL,
  customer_email VARCHAR(150) NOT NULL,
  customer_phone VARCHAR(30) NOT NULL,
  customer_company VARCHAR(150),
  message TEXT,
  cart_snapshot JSON NOT NULL,
  status ENUM('new','acknowledged','quoted','closed') DEFAULT 'new',
  whatsapp_sent TINYINT(1) DEFAULT 0,
  admin_email_sent TINYINT(1) DEFAULT 0,
  customer_email_sent TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE products
  ADD COLUMN IF NOT EXISTS product_code VARCHAR(50) UNIQUE AFTER sku,
  ADD COLUMN IF NOT EXISTS machine_name VARCHAR(255) AFTER product_code,
  ADD COLUMN IF NOT EXISTS machine_model VARCHAR(255) AFTER machine_name,
  ADD COLUMN IF NOT EXISTS compatible_machines TEXT AFTER machine_model;

CREATE INDEX IF NOT EXISTS idx_product_code ON products(product_code);

ALTER TABLE banners
  ADD COLUMN IF NOT EXISTS media_type ENUM('image','video') DEFAULT 'image' AFTER image,
  ADD COLUMN IF NOT EXISTS video_url VARCHAR(500) AFTER media_type;

INSERT IGNORE INTO settings (setting_key, setting_value, setting_group, label) VALUES
('admin_email',               '',              'notifications', 'Admin email for enquiry notifications'),
('admin_whatsapp',            '',              'notifications', 'Admin WhatsApp number (with country code, no +)'),
('contact_phone',             '',              'notifications', 'Contact phone shown to customers'),
('contact_whatsapp',          '',              'notifications', 'WhatsApp number shown to customers'),
('contact_email',             '',              'notifications', 'Contact email shown to customers'),
('groq_api_key',              '',              'integrations',  'Groq AI API key for quote generation'),
('groq_model',                'llama3-8b-8192','integrations',  'Groq model to use'),
('google_analytics_id',       '',              'seo',           'Google Analytics 4 measurement ID'),
('google_search_console_tag', '',              'seo',           'Google Search Console verification tag');
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
CREATE FULLTEXT INDEX idx_machine_search ON products(machine_name, machine_model, compatible_machines, name);
