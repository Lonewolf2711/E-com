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
