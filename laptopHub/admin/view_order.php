<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdmin();

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Get order info
$sql = "SELECT o.*, u.name as user_name, u.email as user_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = $order_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: orders.php");
    exit();
}

$order = $result->fetch_assoc();

// Get order items
$items_sql = "SELECT oi.*, p.name, p.image
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = $order_id";
$items_result = $conn->query($items_sql);
$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - LaptopHub</title>
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
        .order-details { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .order-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
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
                <h1>Order Details #<?php echo $order['id']; ?></h1>
                <a href="orders.php" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Back to Orders</a>
            </div>

            <div class="order-details">
                <div class="order-info-grid">
                    <div>
                        <h3>Customer Information</h3>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    </div>
                    <div>
                        <h3>Order Information</h3>
                        <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                        <p><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                        <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                        <p><strong>Total:</strong> PKR <?php echo number_format($order['total_price'], 2); ?></p>
                    </div>
                </div>

                <div>
                    <h3>Shipping Address</h3>
                    <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                </div>
            </div>

            <div class="order-details">
                <h3>Order Items</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>PKR <?php echo number_format($item['price'], 2); ?></td>
                                <td>PKR <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align: right;">Total:</th>
                            <th>PKR <?php echo number_format($order['total_price'], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
