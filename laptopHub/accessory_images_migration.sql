-- Multiple Accessory Images Migration
-- Run this to add support for multiple images per accessory

USE laptophub;

-- Create accessory_images table
CREATE TABLE IF NOT EXISTS accessory_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accessory_id INT NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    image_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accessory_id) REFERENCES accessories(id) ON DELETE CASCADE
);

-- Insert existing single images into the new table (if any accessories have images)
INSERT INTO accessory_images (accessory_id, image_name, image_order)
SELECT id, image, 0 FROM accessories WHERE image IS NOT NULL AND image != '';

-- Note: After running this migration, you can optionally drop the image column from accessories table
-- ALTER TABLE accessories DROP COLUMN image;
