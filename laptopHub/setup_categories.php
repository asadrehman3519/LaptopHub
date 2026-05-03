<?php
require_once 'includes/config.php';

echo "<h2>Setting Up Categories for LaptopHub</h2>";
echo "<pre>";

// Check if categories table exists and has data
$check_sql = "SELECT COUNT(*) as count FROM categories";
$result = $conn->query($check_sql);
$count = $result->fetch_assoc()['count'];

if ($count > 0) {
    echo "Categories table already has $count records.\n";
    echo "Clearing existing categories...\n";
    $conn->query("TRUNCATE TABLE categories");
}

// Insert categories
$categories = [
    ['name' => 'Business Laptops', 'slug' => 'business', 'description' => 'Professional laptops for business use', 'icon' => 'briefcase'],
    ['name' => 'Student Laptops', 'slug' => 'student', 'description' => 'Budget-friendly laptops for students', 'icon' => 'graduation-cap'],
    ['name' => 'Gaming Laptops', 'slug' => 'gaming', 'description' => 'High-performance gaming laptops', 'icon' => 'gamepad'],
    ['name' => 'Premium Laptops', 'slug' => 'premium', 'description' => 'Premium and luxury laptops', 'icon' => 'gem'],
    ['name' => 'Budget Laptops', 'slug' => 'budget', 'description' => 'Affordable laptops for basic use', 'icon' => 'tag']
];

$success = 0;
foreach ($categories as $cat) {
    $sql = "INSERT INTO categories (name, slug, description, icon) VALUES ('" . 
           $conn->real_escape_string($cat['name']) . "', '" . 
           $conn->real_escape_string($cat['slug']) . "', '" . 
           $conn->real_escape_string($cat['description']) . "', '" . 
           $conn->real_escape_string($cat['icon']) . "')";
    
    if ($conn->query($sql)) {
        $success++;
        echo "✓ Added: " . $cat['name'] . "\n";
    } else {
        echo "✗ Error: " . $conn->error . "\n";
    }
}

echo "\n✓ Successfully added $success categories!\n";
echo "\n<a href='import_products.php'>Now Import Products</a>";
echo "</pre>";
?>
