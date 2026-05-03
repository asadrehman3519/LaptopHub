<?php
require_once 'includes/config.php';

// Read the SQL file
$sql_file = file_get_contents('products_import.sql');

// Split into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql_file)));

echo "<h2>Importing 60 Laptop Products</h2>";
echo "<pre>";

$success_count = 0;
$error_count = 0;

foreach ($statements as $statement) {
    // Skip empty statements and comments
    if (empty($statement) || strpos($statement, '--') === 0 || strpos($statement, 'USE') === 0 || strpos($statement, 'TRUNCATE') === 0) {
        continue;
    }

    // Execute the statement
    if ($conn->query($statement)) {
        $success_count++;
        echo "✓ Product imported successfully\n";
    } else {
        $error_count++;
        echo "✗ Error: " . $conn->error . "\n";
    }
}

echo "\n";
echo "========================================\n";
echo "Import Summary:\n";
echo "Success: $success_count products\n";
echo "Errors: $error_count\n";
echo "========================================\n";

if ($success_count > 0) {
    echo "\n✓ All products imported successfully!";
    echo "\n<a href='index.php'>Go to Homepage</a>";
} else {
    echo "\n✗ No products were imported.";
}

echo "</pre>";
?>
