ALTER TABLE products ADD COLUMN product_code VARCHAR(50) UNIQUE AFTER sku;
ALTER TABLE products ADD COLUMN machine_name VARCHAR(255) AFTER product_code;
ALTER TABLE products ADD COLUMN machine_model VARCHAR(255) AFTER machine_name;
ALTER TABLE products ADD COLUMN compatible_machines TEXT AFTER machine_model;

CREATE INDEX idx_product_code ON products(product_code);
CREATE FULLTEXT INDEX idx_machine_search ON products(machine_name, machine_model, compatible_machines, name);
