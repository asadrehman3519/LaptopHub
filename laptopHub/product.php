<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Get product details
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit();
}

// Get product images
$product_images = [];
$img_result = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id ORDER BY image_order");
while ($row = $img_result->fetch_assoc()) {
    $product_images[] = $row;
}

// Handle Add to Cart
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }

    $user_id = getCurrentUserId();
    $quantity = (int)$_POST['quantity'];

    $check_sql = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $update_sql = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id";
        $conn->query($update_sql);
    } else {
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
        $conn->query($insert_sql);
    }

    $message = 'Product added to cart successfully!';
}

// Handle Review Submission
$review_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }

    $user_id = getCurrentUserId();
    $rating = (int)$_POST['rating'];
    $review_text = $conn->real_escape_string($_POST['review_text']);

    $insert_review = "INSERT INTO reviews (user_id, product_id, rating, review_text) VALUES ($user_id, $product_id, $rating, '$review_text')";
    if ($conn->query($insert_review)) {
        $update_rating = "UPDATE products p SET 
            rating = (SELECT AVG(rating) FROM reviews WHERE product_id = $product_id),
            reviews_count = (SELECT COUNT(*) FROM reviews WHERE product_id = $product_id)
            WHERE id = $product_id";
        $conn->query($update_rating);
        $review_message = 'Review submitted successfully!';
    }
}

// Get product reviews
$reviews_sql = "SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = $product_id ORDER BY r.created_at DESC";
$reviews_result = $conn->query($reviews_sql);
$reviews = [];
while ($row = $reviews_result->fetch_assoc()) {
    $reviews[] = $row;
}


// Check if in wishlist
$in_wishlist = false;
if (isLoggedIn()) {
    $user_id = getCurrentUserId();
    $wishlist_check = $conn->query("SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    $in_wishlist = $wishlist_check->num_rows > 0;
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="product-detail">
    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="product-detail-container">
            <div class="product-detail-image" style="position: relative;">
                <?php if (!empty($product_images)): ?>
                    <div class="main-image-container">
                        <img id="main-image" src="assets/images/<?php echo htmlspecialchars($product_images[0]['image_name']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px;">
                    </div>
                    <?php if (count($product_images) > 1): ?>
                        <div class="image-thumbnails" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 0.5rem; margin-top: 1rem;">
                            <?php foreach ($product_images as $index => $img): ?>
                                <img src="assets/images/<?php echo htmlspecialchars($img['image_name']); ?>" 
                                     alt="Thumbnail <?php echo $index + 1; ?>"
                                     class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                     onclick="changeMainImage('assets/images/<?php echo htmlspecialchars($img['image_name']); ?>', this)"
                                     style="width: 100%; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 2px solid <?php echo $index === 0 ? '#667eea' : '#ddd'; ?>;">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <i class="fas fa-laptop" style="font-size: 4rem; color: #ddd;"></i>
                <?php endif; ?>
                <?php if ($product['is_deal']): ?>
                    <span class="deal-badge" style="position: absolute; top: 10px; left: 10px; background: #ff6b6b; color: white; padding: 5px 10px; border-radius: 5px;">
                        <?php 
                        $discount = round((($product['price'] - $product['deal_price']) / $product['price']) * 100);
                        echo $discount . '% OFF';
                        ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="product-detail-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="brand">Brand: <?php echo htmlspecialchars($product['brand']); ?></p>
                
                <div class="rating" style="margin: 1rem 0;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= $product['rating'] ? 'active' : ''; ?>"></i>
                    <?php endfor; ?>
                    <span>(<?php echo $product['reviews_count']; ?> reviews)</span>
                </div>

                <?php if ($product['is_deal'] && $product['deal_price']): ?>
                    <p class="price">
                        <span style="text-decoration: line-through; color: #999; font-size: 1.2rem;">PKR <?php echo number_format($product['price'], 2); ?></span><br>
                        <span style="color: #ff6b6b; font-size: 2rem;">PKR <?php echo number_format($product['deal_price'], 2); ?></span>
                    </p>
                <?php else: ?>
                    <p class="price">PKR <?php echo number_format($product['price'], 2); ?></p>
                <?php endif; ?>

                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>

                <div class="specs">
                    <h3>Specifications:</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <?php if ($product['processor']): ?>
                        <tr><td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Processor:</strong></td><td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($product['processor']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['ram']): ?>
                        <tr><td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>RAM:</strong></td><td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($product['ram']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['storage']): ?>
                        <tr><td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Storage:</strong></td><td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($product['storage']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['graphics']): ?>
                        <tr><td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Graphics:</strong></td><td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($product['graphics']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['display']): ?>
                        <tr><td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Display:</strong></td><td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($product['display']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['battery']): ?>
                        <tr><td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Battery:</strong></td><td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($product['battery']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['weight']): ?>
                        <tr><td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Weight:</strong></td><td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($product['weight']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['warranty']): ?>
                        <tr><td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Warranty:</strong></td><td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($product['warranty']); ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>

                <p><strong>Stock:</strong> <?php echo $product['stock']; ?> units available</p>

                <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
                    <?php if(isLoggedIn()): ?>
                        <a href="wishlist.php?action=<?php echo $in_wishlist ? 'remove' : 'add'; ?>&id=<?php echo $product['id']; ?>" 
                           class="btn <?php echo $in_wishlist ? 'btn-danger' : 'btn-warning'; ?>">
                            <i class="fas fa-heart"></i> <?php echo $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                        </a>
                        <a href="compare.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-balance-scale"></i> Compare
                        </a>
                    <?php endif; ?>
                </div>

                <form method="POST" action="" style="margin-top: 2rem;">
                    <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                        <label>Quantity:</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" style="width: 100px; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-primary add-to-cart">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                    <a href="products.php" class="btn btn-warning">Back to Products</a>
                </form>
            </div>
        </div>

        <?php if(isLoggedIn()): ?>
                <div style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <h3>Write a Review</h3>
                    <?php if ($review_message): ?>
                        <div class="alert alert-success"><?php echo $review_message; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Rating</label>
                            <select name="rating" style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                                <option value="4">⭐⭐⭐⭐ - Very Good</option>
                                <option value="3">⭐⭐⭐ - Good</option>
                                <option value="2">⭐⭐ - Fair</option>
                                <option value="1">⭐ - Poor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Your Review</label>
                            <textarea name="review_text" rows="4" style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;" required></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (empty($reviews)): ?>
                <p>No reviews yet. Be the first to review!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 1rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                            <div class="rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                        <small style="color: #666;"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
function changeMainImage(imageSrc, thumbnail) {
    document.getElementById('main-image').src = imageSrc;
    
    // Update active thumbnail styling
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.style.border = '2px solid #ddd';
    });
    if (thumbnail) {
        thumbnail.style.border = '2px solid #667eea';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
