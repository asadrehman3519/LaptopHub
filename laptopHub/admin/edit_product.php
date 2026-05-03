<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdmin();

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = (int)$_GET['id'];
$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $brand = $conn->real_escape_string($_POST['brand']);
    $price = (float)$_POST['price'];
    $specs = $conn->real_escape_string($_POST['specs']);
    $description = $conn->real_escape_string($_POST['description']);
    $stock = (int)$_POST['stock'];
        $ram = $conn->real_escape_string($_POST['ram']);
    $storage = $conn->real_escape_string($_POST['storage']);
    $processor = $conn->real_escape_string($_POST['processor']);
    $graphics = $conn->real_escape_string($_POST['graphics']);
    $display = $conn->real_escape_string($_POST['display']);
    $battery = $conn->real_escape_string($_POST['battery']);
    $weight = $conn->real_escape_string($_POST['weight']);
    $warranty = $conn->real_escape_string($_POST['warranty']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_deal = isset($_POST['is_deal']) ? 1 : 0;
    $deal_price = $is_deal ? (float)$_POST['deal_price'] : NULL;
    $image = $product['image'];
    $uploaded_images = [];

    // Handle multiple image uploads
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $target_dir = "../assets/images/";
        
        // Get current max order for this product
        $order_result = $conn->query("SELECT MAX(image_order) as max_order FROM product_images WHERE product_id = $id");
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
                    
                    // Insert into product_images table
                    $img_sql = "INSERT INTO product_images (product_id, image_name, image_order) VALUES ($id, '$image_name', " . ($max_order + $key + 1) . ")";
                    $conn->query($img_sql);
                }
            }
        }
    }

    if (empty($error)) {
        $sql = "UPDATE products SET name='$name', brand='$brand', price=$price, specs='$specs', 
                description='$description', stock=$stock, image='$image', 
                ram='$ram', storage='$storage', processor='$processor', graphics='$graphics', 
                display='$display', battery='$battery', weight='$weight', warranty='$warranty', 
                is_featured=$is_featured, is_deal=$is_deal, deal_price=" . ($deal_price ? $deal_price : 'NULL') . " WHERE id=$id";

        if ($conn->query($sql)) {
            $success = "Product updated successfully!";
        } else {
            $error = "Failed to update product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - LaptopHub</title>
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
                <li><a href="add_product.php"><i class="fas fa-plus"></i> Add Product</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Back to Site</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <div class="admin-header">
                <h1>Edit Product</h1>
                <a href="products.php" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Back to Products</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="" enctype="multipart/form-data">
                    <h3 style="margin-bottom: 1rem; color: #667eea;">Basic Information</h3>
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Brand *</label>
                        <input type="text" name="brand" required value="<?php echo htmlspecialchars($product['brand']); ?>">
                    </div>
                                        <div class="form-group">
                        <label>Price (PKR) *</label>
                        <input type="number" name="price" step="0.01" required value="<?php echo number_format($product['price'], 2); ?>">
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock" required value="<?php echo $product['stock']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <h3 style="margin: 2rem 0 1rem; color: #667eea;">Specifications</h3>
                    <div class="form-group">
                        <label>Processor *</label>
                        <input type="text" name="processor" required value="<?php echo htmlspecialchars($product['processor']); ?>">
                    </div>
                    <div class="form-group">
                        <label>RAM *</label>
                        <input type="text" name="ram" required value="<?php echo htmlspecialchars($product['ram']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Storage *</label>
                        <input type="text" name="storage" required value="<?php echo htmlspecialchars($product['storage']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Graphics</label>
                        <input type="text" name="graphics" value="<?php echo htmlspecialchars($product['graphics']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Display</label>
                        <input type="text" name="display" value="<?php echo htmlspecialchars($product['display']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Battery</label>
                        <input type="text" name="battery" value="<?php echo htmlspecialchars($product['battery']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Weight</label>
                        <input type="text" name="weight" value="<?php echo htmlspecialchars($product['weight']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Warranty</label>
                        <input type="text" name="warranty" value="<?php echo htmlspecialchars($product['warranty']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Detailed Specifications *</label>
                        <textarea name="specs" rows="4" required><?php echo htmlspecialchars($product['specs']); ?></textarea>
                    </div>

                    <h3 style="margin: 2rem 0 1rem; color: #667eea;">Special Options</h3>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="is_featured" style="width: auto;" <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                            Mark as Featured Product
                        </label>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="is_deal" style="width: auto;" <?php echo $product['is_deal'] ? 'checked' : ''; ?>>
                            Mark as Deal Product
                        </label>
                    </div>
                    <div class="form-group" id="deal-price-group" style="display: <?php echo $product['is_deal'] ? 'block' : 'none'; ?>;">
                        <label>Deal Price ($) *</label>
                        <input type="number" name="deal_price" step="0.01" value="<?php echo $product['deal_price']; ?>">
                    </div>

                    <?php
// Get existing product images
$existing_images = [];
$img_result = $conn->query("SELECT * FROM product_images WHERE product_id = $id ORDER BY image_order");
while ($row = $img_result->fetch_assoc()) {
    $existing_images[] = $row;
}
?>

                    <h3 style="margin: 2rem 0 1rem; color: #667eea;">Product Images</h3>
                    <div class="form-group">
                        <label>Add More Images</label>
                        <input type="file" name="images[]" accept="image/*" multiple>
                        <small style="color: #666;">You can select multiple images to add to this product.</small>
                    </div>
                    
                    <?php if (!empty($existing_images)): ?>
                    <div class="form-group">
                        <label>Current Images</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem;">
                            <?php foreach ($existing_images as $img): ?>
                                <div style="text-align: center; border: 1px solid #ddd; padding: 0.5rem; border-radius: 5px;">
                                    <img src="../assets/images/<?php echo htmlspecialchars($img['image_name']); ?>" alt="Product Image" style="width: 100%; height: 100px; object-fit: cover; border-radius: 3px;">
                                    <p style="margin: 0.5rem 0; font-size: 0.8rem;"><?php echo htmlspecialchars($img['image_name']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Product</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.querySelector('input[name="is_deal"]').addEventListener('change', function() {
            document.getElementById('deal-price-group').style.display = this.checked ? 'block' : 'none';
        });
    </script>
</body>
</html>
