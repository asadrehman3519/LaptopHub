<?php
require_once 'includes/config.php';

echo "=== Removing Duplicate Products ===\n";

// Find and remove duplicates, keeping the first entry (lowest ID)
$duplicate_sql = "DELETE p1 FROM products p1
INNER JOIN products p2 
WHERE p1.id > p2.id 
AND p1.name = p2.name";

if ($conn->query($duplicate_sql)) {
    $affected_rows = $conn->affected_rows;
    echo "Removed $affected_rows duplicate products\n";
} else {
    echo "Error removing duplicates: " . $conn->error . "\n";
}

// Check final count
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$count = $result->fetch_assoc();
echo "Final product count: " . $count['total'] . "\n";

// Verify no more duplicates
$result = $conn->query("SELECT name, COUNT(*) as count FROM products GROUP BY name HAVING count > 1");
$duplicates = $result->num_rows;
echo "Remaining products with duplicate names: " . $duplicates . "\n";

echo "\n=== Cleanup Complete ===\n";
?>
