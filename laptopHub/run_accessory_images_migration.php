<?php
require_once 'includes/config.php';

echo "Running accessory images migration...\n";

// Create accessory_images table
$sql = "CREATE TABLE IF NOT EXISTS accessory_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accessory_id INT NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    image_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accessory_id) REFERENCES accessories(id) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo "✓ accessory_images table created successfully\n";
} else {
    echo "✗ Error creating accessory_images table: " . $conn->error . "\n";
    exit();
}

// Migrate existing images
$sql = "INSERT INTO accessory_images (accessory_id, image_name, image_order)
SELECT id, image, 0 FROM accessories WHERE image IS NOT NULL AND image != ''";

if ($conn->query($sql)) {
    echo "✓ Existing accessory images migrated successfully\n";
} else {
    echo "✗ Error migrating existing images: " . $conn->error . "\n";
}

echo "Accessory images migration completed successfully!\n";
?>
