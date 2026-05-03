<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdmin();

// Handle add/edit/delete categories
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    $description = $conn->real_escape_string($_POST['description']);

    $sql = "INSERT INTO categories (name, slug, description) VALUES ('$name', '$slug', '$description')";

    if ($conn->query($sql)) {
        $success = "Category added successfully!";
    } else {
        $error = "Failed to add category: " . $conn->error;
    }
}

if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id = $delete_id");
    header("Location: categories.php");
    exit();
}

// Get all categories
$sql = "SELECT * FROM categories ORDER BY name";
$result = $conn->query($sql);
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - LaptopHub</title>
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
        .form-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h3 style="padding: 0 2rem; margin-bottom: 1rem;">Admin Panel</h3>
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="accessories.php"><i class="fas fa-tools"></i> Accessories</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Back to Site</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <div class="admin-header">
                <h1><i class="fas fa-tags"></i> Manage Categories</h1>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Add Category Form -->
            <div class="form-container" style="margin-bottom: 2rem;">
                <h3>Add New Category</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Category Name *</label>
                        <input type="text" name="name" required placeholder="e.g., Gaming Laptops">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" placeholder="Category description..."></textarea>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-success"><i class="fas fa-plus"></i> Add Category</button>
                </form>
            </div>

            <!-- Categories List -->
            <div class="form-container">
                <h3>All Categories</h3>
                <?php if (empty($categories)): ?>
                    <p>No categories added yet.</p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?php echo $cat['id']; ?></td>
                                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($cat['description'], 0, 50)); ?>...</td>
                                    <td>
                                        <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="btn btn-danger" onclick="return confirmDelete('Delete this category?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
