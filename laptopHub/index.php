<?php
require_once 'includes/config.php';

// Get featured products
$featured_sql = "SELECT * FROM products WHERE is_featured = TRUE ORDER BY created_at DESC LIMIT 8";
$featured_result = $conn->query($featured_sql);
$products = [];
while ($row = $featured_result->fetch_assoc()) {
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

// Get deal products
$deals_sql = "SELECT * FROM products WHERE is_deal = TRUE ORDER BY created_at DESC LIMIT 4";
$deals_result = $conn->query($deals_sql);
$deals = [];
while ($row = $deals_result->fetch_assoc()) {
    // Get product images
    $product_id = $row['id'];
    $img_result = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id ORDER BY image_order");
    $images = [];
    while ($img_row = $img_result->fetch_assoc()) {
        $images[] = $img_row;
    }
    $row['images'] = $images;
    $deals[] = $row;
}

// Get accessories
$accessories_sql = "SELECT * FROM accessories ORDER BY created_at DESC LIMIT 12";
$accessories_result = $conn->query($accessories_sql);
$accessories = [];
while ($row = $accessories_result->fetch_assoc()) {
    // Get accessory images
    $accessory_id = $row['id'];
    $img_result = $conn->query("SELECT * FROM accessory_images WHERE accessory_id = $accessory_id ORDER BY image_order");
    $images = [];
    while ($img_row = $img_result->fetch_assoc()) {
        $images[] = $img_row;
    }
    $row['images'] = $images;
    $accessories[] = $row;
}

// Get categories from admin panel
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_sql);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1><i class="fas fa-laptop"></i> Welcome to LaptopHub</h1>
                <p>Discover the best laptops at unbeatable prices</p>
                <a href="products.php" class="btn btn-primary">Shop Now</a>
            </div>
            <div class="hero-image">
                <img src="assets/images/welcome-banner.jpg" alt="Welcome to LaptopHub" style="width: 100%; height: auto; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            </div>
        </div>
    </div>
</section>

<section class="products-section">
    <div class="container">
        <div class="section-title">
            <h2>Featured Laptops</h2>
            <p>Check out our latest collection</p>
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
                                <span style="color: #ff6b6b;">PKR <?php echo number_format($product['deal_price'], 2); ?></span>
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
                        <p class="product-specs">
                            <?php 
                            $specs = [];
                            if ($product['processor']) $specs[] = htmlspecialchars($product['processor']);
                            if ($product['ram']) $specs[] = htmlspecialchars($product['ram']);
                            if ($product['storage']) $specs[] = htmlspecialchars($product['storage']);
                            echo implode(' | ', array_slice($specs, 0, 3));
                            ?>
                        </p>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center;">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="products.php" class="btn btn-primary">View All Laptops</a>
        </div>
    </div>
</section>

<!-- Hot Deals Section -->
<?php if (!empty($deals)): ?>
<section class="products-section" style="background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-fire" style="color: #ff6b6b;"></i> Hot Deals 🔥</h2>
            <p>Limited time offers on premium laptops</p>
        </div>
        <div class="products-grid">
            <?php foreach ($deals as $product): ?>
                <div class="product-card" style="border: 2px solid #ff6b6b;">
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
                        <span class="deal-badge" style="position: absolute; top: 10px; left: 10px; background: #ff6b6b; color: white; padding: 5px 10px; border-radius: 5px; font-size: 0.8rem;">
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
                            <span style="text-decoration: line-through; color: #999; font-size: 0.9rem;">PKR <?php echo number_format($product['price'], 2); ?></span><br>
                            <span style="color: #ff6b6b; font-size: 1.5rem;">PKR <?php echo number_format($product['deal_price'], 2); ?></span>
                        </p>
                        <p class="product-specs">
                            <?php 
                            $specs = [];
                            if ($product['processor']) $specs[] = htmlspecialchars($product['processor']);
                            if ($product['ram']) $specs[] = htmlspecialchars($product['ram']);
                            if ($product['storage']) $specs[] = htmlspecialchars($product['storage']);
                            echo implode(' | ', array_slice($specs, 0, 3));
                            ?>
                        </p>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center; background: #ff6b6b;">View Deal</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="deals.php" class="btn btn-danger">View All Deals</a>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- Accessories Section -->
<?php if (!empty($accessories)): ?>
<section class="products-section" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-tools"></i> Laptop Accessories</h2>
            <p>Complete your setup with premium accessories</p>
        </div>
        <div class="products-grid">
            <?php foreach ($accessories as $accessory): ?>
                <div class="product-card">
                    <div class="product-image" style="position: relative;" data-accessory-id="<?php echo $accessory['id']; ?>">
                        <?php if (!empty($accessory['images'])): ?>
                            <img class="accessory-main-image" src="assets/images/<?php echo htmlspecialchars($accessory['images'][0]['image_name']); ?>" 
                                 alt="<?php echo htmlspecialchars($accessory['name']); ?>" 
                                 style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;"
                                 data-images='<?php echo json_encode(array_map(function($img) { return 'assets/images/' . $img['image_name']; }, $accessory['images'])); ?>'>
                        <?php elseif ($accessory['image'] && file_exists('assets/images/' . $accessory['image'])): ?>
                            <img src="assets/images/<?php echo htmlspecialchars($accessory['image']); ?>" alt="<?php echo htmlspecialchars($accessory['name']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                            <i class="fas fa-tools" style="font-size: 3rem; color: #ddd;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($accessory['name']); ?></h3>
                        <p class="product-brand"><?php echo htmlspecialchars($accessory['category']); ?></p>
                        <p class="product-price">PKR <?php echo number_format($accessory['price'], 2); ?></p>
                        <p class="product-specs"><?php echo htmlspecialchars($accessory['description']); ?></p>
                        <div class="product-actions">
                            <a href="accessories.php" class="btn btn-primary" style="flex: 1; text-align: center;">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="accessories.php" class="btn btn-primary" style="background: #667eea; padding: 1rem 2rem; font-size: 1.1rem;">
                <i class="fas fa-tools"></i> View All Accessories
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

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
    font-size: 0.9rem;
}

.rating span {
    color: #666;
    margin-left: 5px;
}

.rating .active {
    color: #ffc107;
}
</style>

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
    
    // Accessory image slideshow functionality
    const accessoryImages = document.querySelectorAll('.accessory-main-image');
    
    accessoryImages.forEach(function(img) {
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
        
        // Add hover events to the accessory image container
        const container = img.closest('.product-image');
        if (container) {
            container.addEventListener('mouseenter', startSlideshow);
            container.addEventListener('mouseleave', stopSlideshow);
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
