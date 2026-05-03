<?php
require_once 'includes/config.php';

// Get filter parameters
$brand = isset($_GET['brand']) ? $conn->real_escape_string($_GET['brand']) : '';
$price_min = isset($_GET['price_min']) ? (float)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) ? (float)$_GET['price_max'] : 999999;
$ram = isset($_GET['ram']) ? $conn->real_escape_string($_GET['ram']) : '';
$storage = isset($_GET['storage']) ? $conn->real_escape_string($_GET['storage']) : '';
$processor = isset($_GET['processor']) ? $conn->real_escape_string($_GET['processor']) : '';

// Build query
$sql = "SELECT * FROM products WHERE 1=1";
if ($brand) {
    $sql .= " AND brand = '$brand'";
}
if ($price_min > 0) {
    $sql .= " AND price >= $price_min";
}
if ($price_max < 999999) {
    $sql .= " AND price <= $price_max";
}
if ($ram) {
    $sql .= " AND ram LIKE '%$ram%'";
}
if ($storage) {
    $sql .= " AND storage LIKE '%$storage%'";
}
if ($processor) {
    $sql .= " AND processor LIKE '%$processor%'";
}
$sql .= " ORDER BY created_at DESC";

// Execute query
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

// Get unique brands for filter
$brands_result = $conn->query("SELECT DISTINCT brand FROM products ORDER BY brand");
$brands = [];
while ($row = $brands_result->fetch_assoc()) {
    $brands[] = $row['brand'];
}

// Get unique RAM options
$ram_result = $conn->query("SELECT DISTINCT ram FROM products WHERE ram IS NOT NULL AND ram != '' ORDER BY ram");
$rams = [];
while ($row = $ram_result->fetch_assoc()) {
    $rams[] = $row['ram'];
}

// Get unique Storage options
$storage_result = $conn->query("SELECT DISTINCT storage FROM products WHERE storage IS NOT NULL AND storage != '' ORDER BY storage");
$storages = [];
while ($row = $storage_result->fetch_assoc()) {
    $storages[] = $row['storage'];
}

// Get unique Processor options
$processor_result = $conn->query("SELECT DISTINCT processor FROM products WHERE processor IS NOT NULL AND processor != '' ORDER BY processor");
$processors = [];
while ($row = $processor_result->fetch_assoc()) {
    $processors[] = $row['processor'];
}

?>

<?php require_once 'includes/header.php'; ?>

<section class="products-section">
    <div class="container">
        <div class="section-title">
            <h2>All Laptops</h2>
            <p>Browse our complete collection</p>
        </div>

        <!-- Filters -->
        <div style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <form method="GET" action="">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                                        
                    <select name="brand" style="width: 100%; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; background: white; cursor: pointer; transition: border-color 0.3s;">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo $brand; ?>" <?php echo $brand == $brand ? 'selected' : ''; ?>><?php echo htmlspecialchars($brand); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="category" style="width: 100%; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; background: white; cursor: pointer; transition: border-color 0.3s;">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="ram" style="width: 100%; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; background: white; cursor: pointer; transition: border-color 0.3s;">
                        <option value="">All RAM</option>
                        <?php foreach ($rams as $r): ?>
                            <option value="<?php echo $r; ?>" <?php echo $ram == $r ? 'selected' : ''; ?>><?php echo htmlspecialchars($r); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="storage" style="width: 100%; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; background: white; cursor: pointer; transition: border-color 0.3s;">
                        <option value="">All Storage</option>
                        <?php foreach ($storages as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $storage == $s ? 'selected' : ''; ?>><?php echo htmlspecialchars($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="processor" style="width: 100%; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; background: white; cursor: pointer; transition: border-color 0.3s;">
                        <option value="">All Processors</option>
                        <?php foreach ($processors as $p): ?>
                            <option value="<?php echo $p; ?>" <?php echo $processor == $p ? 'selected' : ''; ?>><?php echo htmlspecialchars($p); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--accent);">Price Range (PKR):</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <input type="number" name="price_min" value="<?php echo $price_min > 0 ? $price_min : ''; ?>" placeholder="Min Price" style="width: 100%; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s;">
                        </div>
                        <div>
                            <input type="number" name="price_max" value="<?php echo $price_max < 999999 ? $price_max : ''; ?>" placeholder="Max Price" style="width: 100%; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s;">
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="products.php" class="btn btn-warning">Clear Filters</a>
                </div>
            </form>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>No laptops found</h3>
                <p>Try adjusting your search or filters</p>
            </div>
        <?php else: ?>
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

<                           <?php endif; ?>
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

<?php require_once 'includes/footer.php'; ?>
