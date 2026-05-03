<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = getCurrentUserId();

// Get user details
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

// Get user's recent orders
$orders_sql = "SELECT o.*, COUNT(oi.id) as items_count 
               FROM orders o 
               LEFT JOIN order_items oi ON o.id = oi.order_id 
               WHERE o.user_id = $user_id 
               GROUP BY o.id 
               ORDER BY o.created_at DESC LIMIT 5";
$orders_result = $conn->query($orders_sql);
$recent_orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $recent_orders[] = $row;
}

// Get all orders for statistics
$all_orders_sql = "SELECT * FROM orders WHERE user_id = $user_id";
$all_orders_result = $conn->query($all_orders_sql);
$all_orders = [];
while ($row = $all_orders_result->fetch_assoc()) {
    $all_orders[] = $row;
}

// Get wishlist count and items
$wishlist_sql = "SELECT w.*, p.name as product_name, p.price as product_price 
                 FROM wishlist w 
                 JOIN products p ON w.product_id = p.id 
                 WHERE w.user_id = $user_id 
                 ORDER BY w.created_at DESC LIMIT 3";
$wishlist_result = $conn->query($wishlist_sql);
$wishlist_items = [];
while ($row = $wishlist_result->fetch_assoc()) {
    $wishlist_items[] = $row;
}
$wishlist_count = count($wishlist_items);

// Calculate user statistics
$total_orders = count($all_orders);
$completed_orders = 0;
$total_spent = 0;
foreach ($all_orders as $order) {
    if ($order['status'] == 'completed') {
        $completed_orders++;
        $total_spent += $order['total_price'];
    }
}

?>

<?php require_once 'includes/header.php'; ?>

<section class="products-section">
    <div class="container">
        <div class="admin-header">
            <h1>My Profile</h1>
        </div>

        <!-- Profile Header -->
        <div class="profile-header" style="display: flex; align-items: center; gap: 2rem; margin-bottom: 2rem; padding: 2rem; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user" style="color: white; font-size: 2rem;"></i>
            </div>
            <div style="flex: 1;">
                <h2 style="margin: 0 0 0.5rem 0; color: var(--accent); font-size: 1.8rem;"><?php echo htmlspecialchars($user['name']); ?></h2>
                <p style="margin: 0 0 0.5rem 0; color: var(--gray-text);"><?php echo htmlspecialchars($user['email']); ?></p>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span style="background: var(--secondary); padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; text-transform: capitalize;">
                        <i class="fas fa-tag" style="margin-right: 0.3rem;"></i> <?php echo htmlspecialchars($user['role']); ?>
                    </span>
                    <span style="color: var(--gray-text); font-size: 0.9rem;">
                        <i class="fas fa-calendar" style="margin-right: 0.3rem;"></i> Member since <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 12px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <i class="fas fa-shopping-bag" style="color: #667eea; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                <h3 style="margin: 0 0 0.5rem 0; font-size: 2rem; color: var(--accent);"><?php echo $total_orders; ?></h3>
                <p style="margin: 0; color: var(--gray-text);">Total Orders</p>
            </div>
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 12px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <i class="fas fa-check-circle" style="color: #10b981; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                <h3 style="margin: 0 0 0.5rem 0; font-size: 2rem; color: var(--accent);"><?php echo $completed_orders; ?></h3>
                <p style="margin: 0; color: var(--gray-text);">Completed Orders</p>
            </div>
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 12px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <i class="fas fa-heart" style="color: #ef4444; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                <h3 style="margin: 0 0 0.5rem 0; font-size: 2rem; color: var(--accent);"><?php echo $wishlist_count; ?></h3>
                <p style="margin: 0; color: var(--gray-text);">Wishlist Items</p>
            </div>
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 12px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <i class="fas fa-money-bill-wave" style="color: #f59e0b; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                <h3 style="margin: 0 0 0.5rem 0; font-size: 2rem; color: var(--accent);">PKR <?php echo number_format($total_spent, 0); ?></h3>
                <p style="margin: 0; color: var(--gray-text);">Total Spent</p>
            </div>
        </div>

        
        <!-- Recent Orders -->
        <div class="form-container" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--accent); font-weight: 600; margin: 0;">Recent Orders</h3>
                <?php if (!empty($recent_orders)): ?>
                    <a href="orders.php" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View All Orders</a>
                <?php endif; ?>
            </div>
            <?php if (empty($recent_orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders yet. Start shopping!</p>
                    <a href="products.php" class="btn btn-primary" style="margin-top: 1rem;">Start Shopping</a>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo $order['items_count']; ?></td>
                                    <td>PKR <?php echo number_format($order['total_price'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></td>
                                    <td>
                                        <a href="orders.php" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">View Orders</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Account Actions -->
        <div class="form-container" style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--accent); font-weight: 600;">Account Actions</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="wishlist.php" class="btn btn-primary">
                    <i class="fas fa-heart"></i> My Wishlist
                </a>
                <a href="cart.php" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> My Cart
                </a>
                <a href="compare.php" class="btn btn-primary">
                    <i class="fas fa-balance-scale"></i> Compare Products
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
