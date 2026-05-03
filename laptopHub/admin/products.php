<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdmin();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: products.php");
    exit();
}

// Get all products
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - LaptopHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-layout { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 250px; background: #333; color: white; padding: 2rem 0; }
        .admin-sidebar ul { list-style: none; }
        .admin-sidebar li { padding: 1rem 2rem; }
        .admin-sidebar a { color: white; text-decoration: none; display: block; transition: background 0.3s; }
        .admin-sidebar a:hover { background: #555; }
        .admin-content { flex: 1; padding: 2rem; background: #f4f4f4; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h3 style="padding: 0 2rem; margin-bottom: 1rem;">Admin Panel</h3>
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                                                <li><a href="accessories.php"><i class="fas fa-tools"></i> Accessories</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Back to Site</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <div class="admin-header">
                <h1>Manage Products</h1>
                <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['brand']); ?></td>
                            <td>PKR <?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="return confirmDelete()">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box"></i>
                    <h3>No products found</h3>
                    <p><a href="add_product.php" class="btn btn-primary">Add Your First Product</a></p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this product?');
        }
    </script>
</body>
</html>
