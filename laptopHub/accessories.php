<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$category_filter = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$sql = "SELECT * FROM accessories";
if ($category_filter) {
    $sql .= " WHERE category = '$category_filter'";
}
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);
$accessories = [];
while ($row = $result->fetch_assoc()) {
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

// Get unique categories
$categories_result = $conn->query("SELECT DISTINCT category FROM accessories ORDER BY category");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row['category'];
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="products-section">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-tools"></i> Laptop Accessories</h2>
            <p>Enhance your laptop experience with our premium accessories</p>
        </div>

        <!-- Category Filter -->
        <div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <span style="font-weight: 600;">Categories:</span>
                <a href="accessories.php" style="padding: 0.5rem 1rem; background: <?php echo $category_filter == '' ? '#667eea' : '#f4f4f4'; ?>; color: <?php echo $category_filter == '' ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 5px;">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="accessories.php?category=<?php echo urlencode($cat); ?>" style="padding: 0.5rem 1rem; background: <?php echo $category_filter == $cat ? '#667eea' : '#f4f4f4'; ?>; color: <?php echo $category_filter == $cat ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 5px;"><?php echo htmlspecialchars($cat); ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($accessories)): ?>
            <div class="empty-state">
                <i class="fas fa-tools"></i>
                <h3>No accessories found</h3>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($accessories as $item): ?>
                    <div class="product-card">
                        <div class="product-image" style="position: relative;" data-accessory-id="<?php echo $item['id']; ?>">
                            <?php if (!empty($item['images'])): ?>
                                <img class="accessory-main-image" src="assets/images/<?php echo htmlspecialchars($item['images'][0]['image_name']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;"
                                     data-images='<?php echo json_encode(array_map(function($img) { return 'assets/images/' . $img['image_name']; }, $item['images'])); ?>'>
                            <?php elseif ($item['image'] && file_exists('assets/images/' . $item['image'])): ?>
                                <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <i class="fas fa-box" style="font-size: 3rem; color: #ddd;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="product-brand"><?php echo htmlspecialchars($item['category']); ?></p>
                            <p class="product-price">PKR <?php echo number_format($item['price'], 2); ?></p>
                            <p class="product-specs"><?php echo htmlspecialchars(substr($item['description'], 0, 80)); ?>...</p>
                            <div class="rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $item['rating'] ? 'active' : ''; ?>"></i>
                                <?php endfor; ?>
                                <span>(<?php echo $item['reviews_count']; ?>)</span>
                            </div>
                            <div class="product-actions">
                                <a href="accessory_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center;">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Accessory image slideshow functionality
document.addEventListener('DOMContentLoaded', function() {
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
