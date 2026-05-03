<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = getCurrentUserId();

// Handle add/remove from wishlist
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $product_id = (int)$_GET['id'];

    if ($action == 'add') {
        $check_sql = "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
        if ($conn->query($check_sql)->num_rows == 0) {
            $insert_sql = "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)";
            $conn->query($insert_sql);
        }
    } elseif ($action == 'remove') {
        $delete_sql = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
        $conn->query($delete_sql);
    }
    header("Location: wishlist.php");
    exit();
}

// Get wishlist items
$sql = "SELECT w.*, p.* 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.id 
        WHERE w.user_id = $user_id 
        ORDER BY w.created_at DESC";
$result = $conn->query($sql);

$wishlist_items = [];
while ($row = $result->fetch_assoc()) {
    $wishlist_items[] = $row;
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="cart-page">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-heart"></i> My Wishlist</h2>
            <p>Products you've saved for later</p>
        </div>

        <?php if (empty($wishlist_items)): ?>
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <h3>Your wishlist is empty</h3>
                <p><a href="products.php" class="btn btn-primary">Browse Laptops</a></p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="product-brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                            <p class="product-price">PKR <?php echo number_format($item['price'], 2); ?></p>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $item['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center;">View Details</a>
                                <a href="wishlist.php?action=remove&id=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirmDelete('Remove from wishlist?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
