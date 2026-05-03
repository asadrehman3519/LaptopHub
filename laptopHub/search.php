<?php
require_once 'includes/config.php';

// Get search query
$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if (empty($search)) {
    header("Location: index.php");
    exit();
}

// Search products
$sql = "SELECT * FROM products WHERE 
        name LIKE '%$search%' OR 
        brand LIKE '%$search%' OR 
        processor LIKE '%$search%' OR 
        ram LIKE '%$search%' OR 
        storage LIKE '%$search%' OR 
        description LIKE '%$search%'
        ORDER BY created_at DESC";

$result = $conn->query($sql);
$products = [];
while ($row = $result->fetch_assoc()) {
    // Get product images
    $product_id = $row['id'];
    $img_result = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id ORDER BY image_order");
    $images = [];
    while ($img_row = $img_result->fetch_assoc()) {
        $images[] = $img_row;
    }
    $row['images'] = $images;
    $products[] = $row;
}

// Get search suggestions (optional)
$suggestions_sql = "SELECT DISTINCT brand FROM products WHERE brand LIKE '%$search%' ORDER BY brand LIMIT 5";
$suggestions_result = $conn->query($suggestions_sql);
$suggestions = [];
while ($row = $suggestions_result->fetch_assoc()) {
    $suggestions[] = $row['brand'];
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="search-results">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-search"></i> Search Results</h2>
            <p>Showing results for: "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
        </div>

        <?php if (!empty($suggestions)): ?>
            <div class="search-suggestions" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                <h4>Search Suggestions:</h4>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem;">
                    <?php foreach ($suggestions as $suggestion): ?>
                        <a href="search.php?q=<?php echo urlencode($suggestion); ?>" 
                           style="background: white; padding: 0.5rem 1rem; border-radius: 20px; text-decoration: none; color: #667eea; border: 1px solid #ddd;">
                            <?php echo htmlspecialchars($suggestion); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($products)): ?>
            <div class="empty-state" style="text-align: center; padding: 3rem;">
                <i class="fas fa-search" style="font-size: 4rem; color: #ddd;"></i>
                <h3>No products found</h3>
                <p>Try searching with different keywords or browse our categories</p>
                <div style="margin-top: 2rem;">
                    <a href="products.php" class="btn btn-primary">Browse All Products</a>
                    <a href="index.php" class="btn btn-secondary">Back to Home</a>
                </div>
            </div>
        <?php else: ?>
            <div class="results-info" style="margin-bottom: 2rem;">
                <p>Found <?php echo count($products); ?> product(s) matching your search</p>
            </div>

            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image" style="position: relative;" data-product-id="<?php echo $product['id']; ?>">
                            <?php if (!empty($product['images'])): ?>
                                <img class="product-main-image" src="assets/images/<?php echo htmlspecialchars($product['images'][0]['image_name']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;"
                                     data-images='<?php echo json_encode(array_map(function($img) { return 'assets/images/' . $img['image_name']; }, $product['images'])); ?>'>
                            <?php elseif ($product['image'] && file_exists('assets/images/' . $product['image'])): ?>
                                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <i class="fas fa-laptop" style="font-size: 3rem; color: #ddd;"></i>
                            <?php endif; ?>
                            <?php if ($product['is_deal']): ?>
                                <span class="deal-badge" style="position: absolute; top: 10px; left: 10px; background: #ff6b6b; color: white; padding: 5px 10px; border-radius: 5px; font-size: 0.8rem;">DEAL</span>
                            <?php endif; ?>
                            <?php if ($product['is_featured']): ?>
                                <span class="featured-badge" style="position: absolute; top: 10px; right: 10px; background: #ffd700; color: #333; padding: 5px 10px; border-radius: 5px; font-size: 0.8rem;">FEATURED</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                            <?php if ($product['is_deal'] && $product['deal_price']): ?>
                                <p class="product-price">
                                    <span style="text-decoration: line-through; color: #999; font-size: 0.9rem;">PKR <?php echo number_format($product['price'], 2); ?></span><br>
                                    <span style="color: #ff6b6b; font-size: 1.2rem;">PKR <?php echo number_format($product['deal_price'], 2); ?></span>
                                </p>
                            <?php else: ?>
                                <p class="product-price">PKR <?php echo number_format($product['price'], 2); ?></p>
                            <?php endif; ?>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center;">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Product image slideshow functionality
document.addEventListener('DOMContentLoaded', function() {
    const productImages = document.querySelectorAll('.product-main-image');
    
    productImages.forEach(function(img) {
        const imagesData = img.getAttribute('data-images');
        if (!imagesData) return;
        
        const images = JSON.parse(imagesData);
        if (images.length <= 1) return;
        
        let currentImageIndex = 0;
        let slideshowInterval;
        
        function changeImage() {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            img.src = images[currentImageIndex];
        }
        
        function startSlideshow() {
            slideshowInterval = setInterval(changeImage, 2000);
        }
        
        function stopSlideshow() {
            if (slideshowInterval) {
                clearInterval(slideshowInterval);
            }
        }
        
        // Add hover events to the product image container
        const container = img.closest('.product-image');
        if (container) {
            container.addEventListener('mouseenter', startSlideshow);
            container.addEventListener('mouseleave', stopSlideshow);
        }
    });
});
</script>

<style>
.search-results {
    padding: 2rem 0;
}

.search-suggestions {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.search-suggestions h4 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.empty-state {
    text-align: center;
    padding: 3rem;
}

.empty-state i {
    font-size: 4rem;
    color: #ddd;
}

.results-info {
    margin-bottom: 2rem;
    color: #666;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.product-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.product-image {
    position: relative;
    overflow: hidden;
}

.product-info {
    padding: 1.5rem;
}

.product-info h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    color: #333;
}

.product-brand {
    color: #666;
    font-size: 0.9rem;
    margin: 0 0 1rem 0;
}

.product-price {
    font-weight: bold;
    color: #667eea;
    font-size: 1.2rem;
    margin: 0 0 1rem 0;
}

.product-actions {
    display: flex;
    gap: 0.5rem;
}

.btn {
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5a67d8;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.deal-badge, .featured-badge {
    position: absolute;
    top: 10px;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.8rem;
    font-weight: bold;
}

.deal-badge {
    left: 10px;
    background: #ff6b6b;
    color: white;
}

.featured-badge {
    right: 10px;
    background: #ffd700;
    color: #333;
}
</style>

<?php require_once 'includes/footer.php'; ?>
