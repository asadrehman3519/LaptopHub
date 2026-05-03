<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdmin();

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

    if (empty($error)) {
        $sql = "INSERT INTO products (name, brand, price, specs, description, stock, image, ram, storage, processor, graphics, display, battery, weight, warranty, is_featured, is_deal, deal_price) 
                VALUES ('$name', '$brand', $price, '$specs', '$description', $stock, '$image', '$ram', '$storage', '$processor', '$graphics', '$display', '$battery', '$weight', '$warranty', $is_featured, $is_deal, " . ($deal_price ? $deal_price : 'NULL') . ")";

        if ($conn->query($sql)) {
            $product_id = $conn->insert_id;
            
            // Insert multiple images into product_images table
            foreach ($uploaded_images as $index => $img_name) {
                $img_sql = "INSERT INTO product_images (product_id, image_name, image_order) VALUES ($product_id, '$img_name', $index)";
                $conn->query($img_sql);
            }
            
            $success = "Product added successfully!";
        } else {
            $error = "Failed to add product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - LaptopHub</title>
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
                                <li><a href="accessories.php"><i class="fas fa-tools"></i> Accessories</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Back to Site</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <div class="admin-header">
                <h1>Add New Product</h1>
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
                    <h3 style="margin-bottom: 1rem; color: #667eea;">Product Details</h3>
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" required placeholder="e.g., MacBook Pro 14 inch">
                    </div>
                    <div class="form-group">
                        <label>Brand *</label>
                        <input type="text" name="brand" required placeholder="e.g., Apple, Dell, HP">
                    </div>
                    <div class="form-group">
                        <label>Price (PKR) *</label>
                        <input type="number" name="price" step="0.01" required placeholder="e.g., 1999.99">
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock" required placeholder="e.g., 10">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" placeholder="Product description..."></textarea>
                    </div>

                    <h3 style="margin: 2rem 0 1rem; color: #667eea;">Product Images</h3>
                    <div class="form-group">
                        <label>Product Images (Multiple images allowed)</label>
                        <input type="file" name="images[]" accept="image/*" multiple style="margin-bottom: 1rem;">
                        <small style="color: #666;">You can select multiple images. First image will be the main product image.</small>
                    </div>

                    
                    <h3 style="margin: 2rem 0 1rem; color: #667eea;">Specifications</h3>
                    <div class="form-group">
                        <label>Processor *</label>
                        <input type="text" name="processor" required placeholder="e.g., Intel Core i7-12700H">
                    </div>
                    <div class="form-group">
                        <label>RAM *</label>
                        <input type="text" name="ram" required placeholder="e.g., 16GB DDR5">
                    </div>
                    <div class="form-group">
                        <label>Storage *</label>
                        <input type="text" name="storage" required placeholder="e.g., 512GB SSD">
                    </div>
                    <div class="form-group">
                        <label>Graphics</label>
                        <input type="text" name="graphics" placeholder="e.g., NVIDIA RTX 4060">
                    </div>
                    <div class="form-group">
                        <label>Display</label>
                        <input type="text" name="display" placeholder="e.g., 14.2 inch Liquid Retina XDR">
                    </div>
                    <div class="form-group">
                        <label>Battery</label>
                        <input type="text" name="battery" placeholder="e.g., 70Wh, 10 hours">
                    </div>
                    <div class="form-group">
                        <label>Weight</label>
                        <input type="text" name="weight" placeholder="e.g., 1.6 kg">
                    </div>
                    <div class="form-group">
                        <label>Warranty</label>
                        <input type="text" name="warranty" placeholder="e.g., 1 Year International">
                    </div>
                    <div class="form-group">
                        <label>Detailed Specifications *</label>
                        <textarea name="specs" rows="4" required placeholder="e.g., M3 Pro Chip, 18GB RAM, 512GB SSD"></textarea>
                    </div>

                    <h3 style="margin: 2rem 0 1rem; color: #667eea;">Special Options</h3>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="is_featured" style="width: auto;">
                            Mark as Featured Product
                        </label>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="is_deal" style="width: auto;">
                            Mark as Deal Product
                        </label>
                    </div>
                    <div class="form-group" id="deal-price-group" style="display: none;">
                        <label>Deal Price ($) *</label>
                        <input type="number" name="deal_price" step="0.01" placeholder="e.g., 1499.99">
                    </div>

                    <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add Product</button>
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
