<?php
require_once 'includes/config.php';

echo "Running multiple images migration...\n";

// Create product_images table
$sql = "CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    image_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo "✓ product_images table created successfully\n";
} else {
    echo "✗ Error creating product_images table: " . $conn->error . "\n";
    exit();
}

// Migrate existing images
$sql = "INSERT INTO product_images (product_id, image_name, image_order)
SELECT id, image, 0 FROM products WHERE image IS NOT NULL AND image != ''";

if ($conn->query($sql)) {
    echo "✓ Existing product images migrated successfully\n";
} else {
    echo "✗ Error migrating existing images: " . $conn->error . "\n";
}

echo "Migration completed successfully!\n";
?>
