<?php
require_once 'includes/config.php';

echo "Importing accessories data...\n";

// Accessories data from CSV
$accessories = [
    ['Wireless Mouse', 'Input Devices', 2500, 'High quality Wireless Mouse suitable for laptops and daily use.'],
    ['Gaming Keyboard', 'Input Devices', 5500, 'High quality Gaming Keyboard suitable for laptops and daily use.'],
    ['Ergonomic Mouse', 'Input Devices', 3200, 'High quality Ergonomic Mouse suitable for laptops and daily use.'],
    ['USB Keyboard', 'Input Devices', 1800, 'High quality USB Keyboard suitable for laptops and daily use.'],
    ['Bluetooth Mouse', 'Input Devices', 2700, 'High quality Bluetooth Mouse suitable for laptops and daily use.'],
    ['Headphones', 'Audio Devices', 4000, 'High quality Headphones suitable for laptops and daily use.'],
    ['Gaming Headset', 'Audio Devices', 7500, 'High quality Gaming Headset suitable for laptops and daily use.'],
    ['Bluetooth Speaker', 'Audio Devices', 6000, 'High quality Bluetooth Speaker suitable for laptops and daily use.'],
    ['Earbuds', 'Audio Devices', 2200, 'High quality Earbuds suitable for laptops and daily use.'],
    ['Noise Cancelling Headphones', 'Audio Devices', 9500, 'High quality Noise Cancelling Headphones suitable for laptops and daily use.'],
    ['Laptop Bag', 'Laptop Protection', 3500, 'High quality Laptop Bag suitable for laptops and daily use.'],
    ['Sleeve Case', 'Laptop Protection', 2800, 'High quality Sleeve Case suitable for laptops and daily use.'],
    ['Hard Shell Cover', 'Laptop Protection', 4500, 'High quality Hard Shell Cover suitable for laptops and daily use.'],
    ['Screen Protector Kit', 'Laptop Protection', 1500, 'High quality Screen Protector Kit suitable for laptops and daily use.'],
    ['Waterproof Backpack', 'Laptop Protection', 5000, 'High quality Waterproof Backpack suitable for laptops and daily use.'],
    ['Laptop Cooling Pad', 'Cooling & Maintenance', 3000, 'High quality Laptop Cooling Pad suitable for laptops and daily use.'],
    ['Thermal Paste Kit', 'Cooling & Maintenance', 2200, 'High quality Thermal Paste Kit suitable for laptops and daily use.'],
    ['Cleaning Kit', 'Cooling & Maintenance', 1800, 'High quality Cleaning Kit suitable for laptops and daily use.'],
    ['Air Duster', 'Cooling & Maintenance', 1200, 'High quality Air Duster suitable for laptops and daily use.'],
    ['Laptop Stand Fan', 'Cooling & Maintenance', 3500, 'High quality Laptop Stand Fan suitable for laptops and daily use.'],
    ['Laptop Charger', 'Power Accessories', 5000, 'High quality Laptop Charger suitable for laptops and daily use.'],
    ['Power Bank 20000mAh', 'Power Accessories', 8000, 'High quality Power Bank 20000mAh suitable for laptops and daily use.'],
    ['Universal Adapter', 'Power Accessories', 4000, 'High quality Universal Adapter suitable for laptops and daily use.'],
    ['Car Charger', 'Power Accessories', 3500, 'High quality Car Charger suitable for laptops and daily use.'],
    ['Extension Board', 'Power Accessories', 2500, 'High quality Extension Board suitable for laptops and daily use.'],
    ['External HDD 1TB', 'Storage Devices', 12000, 'High quality External HDD 1TB suitable for laptops and daily use.'],
    ['USB Flash Drive 64GB', 'Storage Devices', 2000, 'High quality USB Flash Drive 64GB suitable for laptops and daily use.'],
    ['SSD External 512GB', 'Storage Devices', 15000, 'High quality SSD External 512GB suitable for laptops and daily use.'],
    ['Memory Card 128GB', 'Storage Devices', 1800, 'High quality Memory Card 128GB suitable for laptops and daily use.'],
    ['External SSD 1TB', 'Storage Devices', 18000, 'High quality External SSD 1TB suitable for laptops and daily use.']
];

$success_count = 0;
$error_count = 0;

foreach ($accessories as $index => $accessory) {
    $name = $conn->real_escape_string($accessory[0]);
    $category = $conn->real_escape_string($accessory[1]);
    $price = (float)$accessory[2];
    $description = $conn->real_escape_string($accessory[3]);
    
    // Generate image filename based on name
    $image_name = strtolower(str_replace([' ', '-'], '_', $accessory[0])) . '.jpg';
    
    $sql = "INSERT INTO accessories (name, category, price, description, image, stock) 
            VALUES ('$name', '$category', $price, '$description', '$image_name', 50)";
    
    if ($conn->query($sql)) {
        $success_count++;
        echo "✓ Added: " . $accessory[0] . "\n";
    } else {
        $error_count++;
        echo "✗ Error adding " . $accessory[0] . ": " . $conn->error . "\n";
    }
}

echo "\nImport Summary:\n";
echo "Successfully imported: $success_count accessories\n";
echo "Failed to import: $error_count accessories\n";
echo "Total processed: " . ($success_count + $error_count) . " accessories\n";

if ($success_count > 0) {
    echo "\nAccessories import completed successfully!\n";
}
?>
