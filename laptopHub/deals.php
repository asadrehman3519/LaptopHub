<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Get all deal products
$sql = "SELECT * FROM products WHERE is_deal = TRUE ORDER BY created_at DESC";
$result = $conn->query($sql);

$deals = [];
while ($row = $result->fetch_assoc()) {
    $deals[] = $row;
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="products-section">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-fire" style="color: #ff6b6b;"></i> Hot Deals 🔥</h2>
            <p>Limited time offers on premium laptops</p>
        </div>

        <?php if (empty($deals)): ?>
            <div class="empty-state">
                <i class="fas fa-fire"></i>
                <h3>No deals available right now</h3>
                <p>Check back later for amazing offers!</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($deals as $product): ?>
                    <div class="product-card" style="border: 2px solid #ff6b6b;">
                        <div class="product-image">
                            <i class="fas fa-laptop"></i>
                            <span class="deal-badge" style="background: #ff6b6b; font-size: 1rem; padding: 8px 15px;">
                                <?php 
                                $discount = round((($product['price'] - $product['deal_price']) / $product['price']) * 100);
                                echo $discount . '% OFF';
                                ?>
                            </span>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                            <p class="product-price">
                                <span style="text-decoration: line-through; color: #999; font-size: 1rem;">PKR <?php echo number_format($product['price'], 2); ?></span><br>
                                <span style="color: #ff6b6b; font-size: 1.8rem;">PKR <?php echo number_format($product['deal_price'], 2); ?></span>
                            </p>
                            <p class="product-specs"><?php echo htmlspecialchars($product['processor']); ?> | <?php echo htmlspecialchars($product['ram']); ?> | <?php echo htmlspecialchars($product['storage']); ?></p>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center; background: #ff6b6b;">View Deal</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
