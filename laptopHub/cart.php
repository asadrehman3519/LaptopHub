<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = getCurrentUserId();

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $cart_id => $quantity) {
        $quantity = (int)$quantity;
        if ($quantity > 0) {
            $sql = "UPDATE cart SET quantity = $quantity WHERE id = $cart_id AND user_id = $user_id";
            $conn->query($sql);
        }
    }
    header("Location: cart.php");
    exit();
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $sql = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
    $conn->query($sql);
    header("Location: cart.php");
    exit();
}

// Get cart items
$sql = "SELECT c.*, p.name, p.price, p.image, p.stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);

$cart_items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="cart-page">
    <div class="container">
        <div class="section-title">
            <h2>Shopping Cart</h2>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p><a href="products.php" class="btn btn-primary">Start Shopping</a></p>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <form method="POST" action="">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    </td>
                                    <td>PKR <?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <input type="number" name="quantities[<?php echo $item['id']; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['stock']; ?>"
                                               class="quantity-input" style="width: 70px; padding: 0.5rem;">
                                    </td>
                                    <td>PKR <?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td>
                                        <a href="cart.php?remove=<?php echo $item['id']; ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirmDelete('Remove this item from cart?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="cart-total">
                        Total: PKR <?php echo number_format($total, 2); ?>
                    </div>

                    <div class="cart-actions">
                        <button type="submit" name="update_cart" class="btn btn-warning">Update Cart</button>
                        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
