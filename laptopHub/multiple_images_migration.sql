-- Multiple Product Images Migration
-- Run this to add support for multiple images per product

USE laptophub;

-- Create product_images table
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    image_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert existing single images into the new table (if any products have images)
INSERT INTO product_images (product_id, image_name, image_order)
SELECT id, image, 0 FROM products WHERE image IS NOT NULL AND image != '';

-- Note: After running this migration, you can optionally drop the image column from products table
-- ALTER TABLE products DROP COLUMN image;
