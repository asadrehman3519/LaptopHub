<?php
require_once 'includes/config.php';

// First, ensure categories exist
$categories_sql = "SELECT * FROM categories";
$result = $conn->query($categories_sql);

if ($result->num_rows == 0) {
    // Insert categories if they don't exist
    $insert_categories = "INSERT INTO categories (name, slug, description, icon) VALUES
    ('Business Laptops', 'business', 'Light weight, long battery life, security focused', 'briefcase'),
    ('Student Laptops', 'student', 'Budget friendly, good for study and browsing', 'graduation-cap'),
    ('Gaming Laptops', 'gaming', 'High graphics GPU, RGB keyboard, cooling system', 'gamepad'),
    ('Premium Laptops', 'premium', 'MacBook/ultra-thin, high performance + design', 'gem'),
    ('Budget Laptops', 'budget', 'Under 100,000 PKR, basic use', 'tag')";
    $conn->query($insert_categories);
}

// Get all categories
$categories = [];
$result = $conn->query("SELECT * FROM categories");
while ($row = $result->fetch_assoc()) {
    $categories[$row['slug']] = $row['id'];
}

// Update products with appropriate category_id based on their specifications
$update_sql = "UPDATE products SET category_id = CASE 
    WHEN brand IN ('Apple', 'Dell', 'HP', 'Lenovo') AND price > 150000 THEN {$categories['premium']}
    WHEN brand IN ('ASUS', 'MSI', 'Acer') AND (processor LIKE '%Gaming%' OR graphics LIKE '%GTX%' OR graphics LIKE '%RTX%') THEN {$categories['gaming']}
    WHEN price < 80000 THEN {$categories['budget']}
    WHEN brand IN ('Dell', 'HP', 'Lenovo') AND price BETWEEN 80000 AND 150000 THEN {$categories['business']}
    ELSE {$categories['student']}
END WHERE category_id IS NULL";

$conn->query($update_sql);

echo "Categories fixed successfully!";
?>
