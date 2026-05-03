<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = getCurrentUserId();

// Handle add/remove from compare
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $product_id = (int)$_GET['id'];

    if ($action == 'add') {
        // Check if already in compare
        $check_sql = "SELECT COUNT(*) as count FROM compare WHERE user_id = $user_id";
        $count = $conn->query($check_sql)->fetch_assoc()['count'];
        
        if ($count < 3) {
            $check_exists = "SELECT * FROM compare WHERE user_id = $user_id AND product_id = $product_id";
            if ($conn->query($check_exists)->num_rows == 0) {
                $insert_sql = "INSERT INTO compare (user_id, product_id) VALUES ($user_id, $product_id)";
                $conn->query($insert_sql);
            }
        } else {
            $error = "You can compare maximum 3 products at a time.";
        }
    } elseif ($action == 'remove') {
        $delete_sql = "DELETE FROM compare WHERE user_id = $user_id AND product_id = $product_id";
        $conn->query($delete_sql);
    }
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'compare.php'));
    exit();
}

// Get compare items
$sql = "SELECT c.*, p.* 
        FROM compare c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id 
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);

$compare_items = [];
while ($row = $result->fetch_assoc()) {
    $compare_items[] = $row;
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="cart-page">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-balance-scale"></i> Compare Laptops</h2>
            <p>Compare up to 3 laptops side by side</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($compare_items)): ?>
            <div class="empty-state">
                <i class="fas fa-balance-scale"></i>
                <h3>No products to compare</h3>
                <p>Add products from the product pages to compare them</p>
                <p><a href="products.php" class="btn btn-primary">Browse Laptops</a></p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="min-width: 150px;">Feature</th>
                            <?php foreach ($compare_items as $item): ?>
                                <th style="min-width: 200px;">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                    <br>
                                    <a href="compare.php?action=remove&id=<?php echo $item['id']; ?>" style="font-size: 0.8rem; color: #ff6b6b;">Remove</a>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Image</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><i class="fas fa-laptop" style="font-size: 3rem;"></i></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Brand</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><?php echo htmlspecialchars($item['brand']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Price</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td style="font-size: 1.2rem; font-weight: bold; color: #667eea;">
                                    PKR <?php echo number_format($item['price'], 2); ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Processor</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><?php echo htmlspecialchars($item['processor']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>RAM</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><?php echo htmlspecialchars($item['ram']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Storage</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><?php echo htmlspecialchars($item['storage']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Graphics</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><?php echo htmlspecialchars($item['graphics']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Display</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><?php echo htmlspecialchars($item['display']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Battery</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><?php echo htmlspecialchars($item['battery']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Weight</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><?php echo htmlspecialchars($item['weight']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Warranty</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td><?php echo htmlspecialchars($item['warranty']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Rating</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td>
                                    <?php echo $item['rating']; ?>/5
                                    (<?php echo $item['reviews_count']; ?> reviews)
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Action</strong></td>
                            <?php foreach ($compare_items as $item): ?>
                                <td>
                                    <a href="product.php?id=<?php echo $item['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View Details</a>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
