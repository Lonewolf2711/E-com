CREATE TABLE enquiries (
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
