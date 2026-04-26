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
