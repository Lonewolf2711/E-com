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
