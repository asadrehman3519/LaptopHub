<?php
require_once 'includes/config.php';

echo "=== Product Database Check ===\n";

// Count total products
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$count = $result->fetch_assoc();
echo "Total products in database: " . $count['total'] . "\n";

// Check for duplicates
$result = $conn->query("SELECT name, COUNT(*) as count FROM products GROUP BY name HAVING count > 1");
$duplicates = $result->num_rows;
echo "Products with duplicate names: " . $duplicates . "\n";

if ($duplicates > 0) {
    echo "\nDuplicate products:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['name'] . " (appears " . $row['count'] . " times)\n";
    }
}

// Check product IDs
$result = $conn->query("SELECT id, name FROM products ORDER BY id");
echo "\nFirst 10 products by ID:\n";
$count = 0;
while ($row = $result->fetch_assoc() && $count < 10) {
    echo "ID: " . $row['id'] . " - " . $row['name'] . "\n";
    $count++;
}

// Check homepage query
echo "\n=== Homepage Query Check ===\n";
$featured_sql = "SELECT * FROM products WHERE is_featured = TRUE ORDER BY created_at DESC LIMIT 8";
$result = $conn->query($featured_sql);
$featured_count = $result->num_rows;
echo "Featured products found: " . $featured_count . "\n";

// Check deals query
$deals_sql = "SELECT * FROM products WHERE is_deal = TRUE ORDER BY created_at DESC LIMIT 4";
$result = $conn->query($deals_sql);
$deals_count = $result->num_rows;
echo "Deal products found: " . $deals_count . "\n";

// Check products page query
$products_sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($products_sql);
$products_count = $result->num_rows;
echo "All products for products page: " . $products_count . "\n";

echo "\n=== Investigation Complete ===\n";
?>
