<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdmin();

// Handle add/update/delete accessories
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_accessory'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = (float)$_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
    $rating = 5;
    $reviews_count = 0;
    $image = 'default.jpg';
    $uploaded_images = [];

    // Handle multiple image uploads
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $target_dir = "../assets/images/";
        
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] == 0) {
                $image_name = time() . '_' . $key . '_' . basename($name);
                $target_file = $target_dir . $image_name;
                
                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                    $uploaded_images[] = $image_name;
                    if (empty($image) || $image == 'default.jpg') {
                        $image = $image_name; // First image as main image
                    }
                }
            }
        }
    }

    $sql = "INSERT INTO accessories (name, category, price, description, rating, reviews_count, image) 
            VALUES ('$name', '$category', $price, '$description', $rating, $reviews_count, '$image')";

    if ($conn->query($sql)) {
        $accessory_id = $conn->insert_id;
        
        // Insert multiple images into accessory_images table
        foreach ($uploaded_images as $index => $img_name) {
            $img_sql = "INSERT INTO accessory_images (accessory_id, image_name, image_order) VALUES ($accessory_id, '$img_name', $index)";
            $conn->query($img_sql);
        }
        
        $success = "Accessory added successfully!";
    } else {
        $error = "Failed to add accessory: " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_accessory'])) {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = (float)$_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
    $image = $_POST['current_image'] ?? 'default.jpg';
    $uploaded_images = [];

    // Handle multiple image uploads
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $target_dir = "../assets/images/";
        
        // Get current max order for this accessory
        $order_result = $conn->query("SELECT MAX(image_order) as max_order FROM accessory_images WHERE accessory_id = $id");
        $max_order = $order_result->fetch_assoc()['max_order'] ?? 0;
        
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] == 0) {
                $image_name = time() . '_' . $key . '_' . basename($name);
                $target_file = $target_dir . $image_name;
                
                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                    $uploaded_images[] = $image_name;
                    
                    // Update main image if this is the first image and no main image exists
                    if (empty($image) || $image == 'default.jpg') {
                        $image = $image_name;
                    }
                    
                    // Insert into accessory_images table
                    $img_sql = "INSERT INTO accessory_images (accessory_id, image_name, image_order) VALUES ($id, '$image_name', " . ($max_order + $key + 1) . ")";
                    $conn->query($img_sql);
                }
            }
        }
    }

    $sql = "UPDATE accessories SET name='$name', category='$category', price=$price, description='$description', image='$image' WHERE id=$id";

    if ($conn->query($sql)) {
        $success = "Accessory updated successfully!";
    } else {
        $error = "Failed to update accessory: " . $conn->error;
    }
}

if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM accessories WHERE id = $delete_id");
    header("Location: accessories.php");
    exit();
}

// Handle edit accessory
$editing_accessory = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM accessories WHERE id = $edit_id");
    $editing_accessory = $result->fetch_assoc();
}

// Get all accessories
$sql = "SELECT * FROM accessories ORDER BY created_at DESC";
$result = $conn->query($sql);
$accessories = [];
while ($row = $result->fetch_assoc()) {
    $accessories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accessories - LaptopHub</title>
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
                <li><a href="accessories.php"><i class="fas fa-tools"></i> Accessories</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Back to Site</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <div class="admin-header">
                <h1><i class="fas fa-tools"></i> Manage Accessories</h1>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Add Accessory Form -->
            <div class="form-container" style="margin-bottom: 2rem;">
                <h3>Add New Accessory</h3>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Accessory Name *</label>
                        <input type="text" name="name" required placeholder="e.g., Wireless Mouse">
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select Category</option>
                            <option value="Input Devices">Input Devices</option>
                            <option value="Audio Devices">Audio Devices</option>
                            <option value="Laptop Protection">Laptop Protection</option>
                            <option value="Cooling & Maintenance">Cooling & Maintenance</option>
                            <option value="Power Accessories">Power Accessories</option>
                            <option value="Storage Devices">Storage Devices</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price (PKR) *</label>
                        <input type="number" name="price" step="0.01" required placeholder="e.g., 2500">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" placeholder="Accessory description..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Accessory Images (Multiple images allowed)</label>
                        <input type="file" name="images[]" accept="image/*" multiple style="margin-bottom: 1rem;">
                        <small style="color: #666;">You can select multiple images. First image will be the main accessory image.</small>
                    </div>
                    <button type="submit" name="add_accessory" class="btn btn-success"><i class="fas fa-plus"></i> Add Accessory</button>
                </form>
            </div>

            <!-- Edit Accessory Form -->
            <?php if ($editing_accessory): ?>
                <?php
                // Get existing accessory images
                $existing_images = [];
                $img_result = $conn->query("SELECT * FROM accessory_images WHERE accessory_id = " . $editing_accessory['id'] . " ORDER BY image_order");
                while ($row = $img_result->fetch_assoc()) {
                    $existing_images[] = $row;
                }
                ?>
                <div class="form-container" style="margin-bottom: 2rem;">
                    <h3>Edit Accessory</h3>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $editing_accessory['id']; ?>">
                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($editing_accessory['image']); ?>">
                        
                        <div class="form-group">
                            <label>Accessory Name *</label>
                            <input type="text" name="name" required value="<?php echo htmlspecialchars($editing_accessory['name']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">Select Category</option>
                                <option value="Input Devices" <?php echo $editing_accessory['category'] == 'Input Devices' ? 'selected' : ''; ?>>Input Devices</option>
                                <option value="Audio Devices" <?php echo $editing_accessory['category'] == 'Audio Devices' ? 'selected' : ''; ?>>Audio Devices</option>
                                <option value="Laptop Protection" <?php echo $editing_accessory['category'] == 'Laptop Protection' ? 'selected' : ''; ?>>Laptop Protection</option>
                                <option value="Cooling & Maintenance" <?php echo $editing_accessory['category'] == 'Cooling & Maintenance' ? 'selected' : ''; ?>>Cooling & Maintenance</option>
                                <option value="Power Accessories" <?php echo $editing_accessory['category'] == 'Power Accessories' ? 'selected' : ''; ?>>Power Accessories</option>
                                <option value="Storage Devices" <?php echo $editing_accessory['category'] == 'Storage Devices' ? 'selected' : ''; ?>>Storage Devices</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Price (PKR) *</label>
                            <input type="number" name="price" step="0.01" required value="<?php echo $editing_accessory['price']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="4"><?php echo htmlspecialchars($editing_accessory['description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Add More Images</label>
                            <input type="file" name="images[]" accept="image/*" multiple>
                            <small style="color: #666;">You can select multiple images to add to this accessory.</small>
                        </div>
                        
                        <?php if (!empty($existing_images)): ?>
                        <div class="form-group">
                            <label>Current Images</label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem;">
                                <?php foreach ($existing_images as $img): ?>
                                    <div style="text-align: center; border: 1px solid #ddd; padding: 0.5rem; border-radius: 5px;">
                                        <img src="../assets/images/<?php echo htmlspecialchars($img['image_name']); ?>" alt="Accessory Image" style="width: 100%; height: 100px; object-fit: cover; border-radius: 3px;">
                                        <p style="margin: 0.5rem 0; font-size: 0.8rem;"><?php echo htmlspecialchars($img['image_name']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" name="update_accessory" class="btn btn-success"><i class="fas fa-save"></i> Update Accessory</button>
                            <a href="accessories.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Accessories List -->
            <div class="form-container">
                <h3>All Accessories</h3>
                <?php if (empty($accessories)): ?>
                    <p>No accessories added yet.</p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Rating</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accessories as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                    <td>PKR <?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['rating']; ?>/5 (<?php echo $item['reviews_count']; ?> reviews)</td>
                                    <td>
                                        <a href="accessories.php?edit=<?php echo $item['id']; ?>" class="btn btn-primary" style="margin-right: 0.5rem;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="accessories.php?delete=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirmDelete('Delete this accessory?')">
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
