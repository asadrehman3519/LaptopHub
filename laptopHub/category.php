<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (!isset($_GET['slug'])) {
    header("Location: products.php");
    exit();
}

$slug = $conn->real_escape_string($_GET['slug']);
$sql = "SELECT * FROM categories WHERE slug = '$slug'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: products.php");
    exit();
}

$category = $result->fetch_assoc();
$category_id = $category['id'];

// Get filters
$price_min = isset($_GET['price_min']) ? (float)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) ? (float)$_GET['price_max'] : 999999;
$ram = isset($_GET['ram']) ? $conn->real_escape_string($_GET['ram']) : '';
$storage = isset($_GET['storage']) ? $conn->real_escape_string($_GET['storage']) : '';
$processor = isset($_GET['processor']) ? $conn->real_escape_string($_GET['processor']) : '';
$brand = isset($_GET['brand']) ? $conn->real_escape_string($_GET['brand']) : '';

// Build query
$sql = "SELECT * FROM products WHERE category_id = $category_id";
$sql .= " AND price BETWEEN $price_min AND $price_max";

if ($ram) {
    $sql .= " AND ram LIKE '%$ram%'";
}
if ($storage) {
    $sql .= " AND storage LIKE '%$storage%'";
}
if ($processor) {
    $sql .= " AND processor LIKE '%$processor%'";
}
if ($brand) {
    $sql .= " AND brand LIKE '%$brand%'";
}

$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Get unique values for filters
$rams = ['4GB', '8GB', '16GB', '32GB', '64GB'];
$storages = ['256GB SSD', '512GB SSD', '1TB SSD', '2TB SSD', '500GB HDD', '1TB HDD'];
$processors = ['Intel i3', 'Intel i5', 'Intel i7', 'Intel i9', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9', 'M3', 'M3 Pro', 'M3 Max'];
$brands = ['Apple', 'Dell', 'HP', 'Lenovo', 'ASUS', 'Acer', 'MSI'];
?>

<?php require_once 'includes/header.php'; ?>

<section class="products-section" style="padding-top: 2rem;">
    <div class="container">
        <div class="section-title">
            <h1><i class="fas fa-<?php echo $category['icon']; ?>"></i> <?php echo htmlspecialchars($category['name']); ?></h1>
            <p><?php echo htmlspecialchars($category['description']); ?></p>
        </div>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Filters Sidebar -->
            <div class="filters-sidebar" style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); height: fit-content;">
                <h3 style="margin-bottom: 1.5rem;">Filters</h3>

                <form method="GET" action="">
                    <input type="hidden" name="slug" value="<?php echo $slug; ?>">

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Price Range</label>
                        <select name="price_min" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="0" <?php echo $price_min == 0 ? 'selected' : ''; ?>>Min Price</option>
                            <option value="0" <?php echo $price_min == 0 ? 'selected' : ''; ?>>PKR 0</option>
                            <option value="50000" <?php echo $price_min == 50000 ? 'selected' : ''; ?>>PKR 50,000</option>
                            <option value="100000" <?php echo $price_min == 100000 ? 'selected' : ''; ?>>PKR 100,000</option>
                            <option value="200000" <?php echo $price_min == 200000 ? 'selected' : ''; ?>>PKR 200,000</option>
                        </select>
                        <select name="price_max" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="999999" <?php echo $price_max == 999999 ? 'selected' : ''; ?>>Max Price</option>
                            <option value="100000" <?php echo $price_max == 100000 ? 'selected' : ''; ?>>PKR 100,000</option>
                            <option value="200000" <?php echo $price_max == 200000 ? 'selected' : ''; ?>>PKR 200,000</option>
                            <option value="300000" <?php echo $price_max == 300000 ? 'selected' : ''; ?>>PKR 300,000</option>
                            <option value="500000" <?php echo $price_max == 500000 ? 'selected' : ''; ?>>PKR 500,000+</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">RAM</label>
                        <select name="ram" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">All RAM</option>
                            <?php foreach ($rams as $r): ?>
                                <option value="<?php echo $r; ?>" <?php echo $ram == $r ? 'selected' : ''; ?>><?php echo $r; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Storage</label>
                        <select name="storage" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">All Storage</option>
                            <?php foreach ($storages as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $storage == $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Processor</label>
                        <select name="processor" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">All Processors</option>
                            <?php foreach ($processors as $p): ?>
                                <option value="<?php echo $p; ?>" <?php echo $processor == $p ? 'selected' : ''; ?>><?php echo $p; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Brand</label>
                        <select name="brand" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?php echo $b; ?>" <?php echo $brand == $b ? 'selected' : ''; ?>><?php echo $b; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
                    <a href="category.php?slug=<?php echo $slug; ?>" class="btn btn-warning" style="width: 100%; margin-top: 0.5rem; text-align: center; display: block;">Clear Filters</a>
                </form>
            </div>

            <!-- Products Grid -->
            <div>
                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your filters</p>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <i class="fas fa-laptop"></i>
                                    <?php if ($product['is_deal']): ?>
                                        <span class="deal-badge">DEAL</span>
                                    <?php endif; ?>
                                    <?php if ($product['is_featured']): ?>
                                        <span class="featured-badge">FEATURED</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                                    <?php if ($product['is_deal'] && $product['deal_price']): ?>
                                        <p class="product-price">
                                            <span style="text-decoration: line-through; color: #999; font-size: 0.9rem;">PKR <?php echo number_format($product['price'], 2); ?></span>
                                            PKR <?php echo number_format($product['deal_price'], 2); ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="product-price">PKR <?php echo number_format($product['price'], 2); ?></p>
                                    <?php endif; ?>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $product['rating'] ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                        <span>(<?php echo $product['reviews_count']; ?>)</span>
                                    </div>
                                    <p class="product-specs"><?php echo htmlspecialchars($product['processor']); ?> | <?php echo htmlspecialchars($product['ram']); ?> | <?php echo htmlspecialchars($product['storage']); ?></p>
                                    <div class="product-actions">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center;">View Details</a>
                                        <?php if(isLoggedIn()): ?>
                                            <a href="wishlist.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-warning"><i class="fas fa-heart"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.deal-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ff6b6b;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 0.8rem;
}

.featured-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ffd700;
    color: #333;
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 0.8rem;
}

.rating {
    margin: 0.5rem 0;
    color: #ffc107;
}

.rating span {
    color: #666;
    margin-left: 5px;
}

.rating .active {
    color: #ffc107;
}
</style>

<?php require_once 'includes/footer.php'; ?>
