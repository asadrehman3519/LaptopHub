<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = getCurrentUserId();

// Get user orders
$sql = "SELECT o.*, COUNT(oi.id) as item_count 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.user_id = $user_id 
        GROUP BY o.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="cart-page">
    <div class="container">
        <div class="section-title">
            <h2>My Orders</h2>
        </div>

        <?php if (isset($_GET['order_success'])): ?>
            <div class="alert alert-success">
                Order placed successfully! Order ID: #<?php echo $_GET['order_success']; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <i class="fas fa-box"></i>
                <h3>No orders yet</h3>
                <p><a href="products.php" class="btn btn-primary">Start Shopping</a></p>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td><?php echo $order['item_count']; ?> items</td>
                                <td>PKR <?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <span style="padding: 5px 10px; border-radius: 5px; 
                                        <?php 
                                        $status_colors = [
                                            'pending' => 'background: #ffc107; color: #333;',
                                            'processing' => 'background: #17a2b8; color: white;',
                                            'completed' => 'background: #28a745; color: white;',
                                            'cancelled' => 'background: #dc3545; color: white;'
                                        ];
                                        echo $status_colors[$order['status']] ?? 'background: #6c757d; color: white;';
                                        ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
