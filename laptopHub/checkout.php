<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = getCurrentUserId();

// Get cart items
$sql = "SELECT c.*, p.name, p.price, p.stock 
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

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

$error = '';
$success = '';

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = $conn->real_escape_string($_POST['shipping_address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $transaction_id = '';
    $payment_status = 'pending';

    if (empty($shipping_address) || empty($phone) || empty($payment_method)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Generate transaction ID for online payments
        if ($payment_method != 'cod') {
            $transaction_id = 'TXN' . time() . rand(1000, 9999);
            $payment_status = 'paid';
        }

        // Create order
        $order_sql = "INSERT INTO orders (user_id, total_price, status, shipping_address, phone, payment_method, payment_status, transaction_id) 
                      VALUES ($user_id, $total, 'pending', '$shipping_address', '$phone', '$payment_method', '$payment_status', '$transaction_id')";
        
        if ($conn->query($order_sql)) {
            $order_id = $conn->insert_id;

            // Add order items
            foreach ($cart_items as $item) {
                $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                   VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})";
                $conn->query($order_item_sql);

                // Update product stock
                $update_stock = "UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product_id']}";
                $conn->query($update_stock);
            }

            // Clear cart
            $clear_cart = "DELETE FROM cart WHERE user_id = $user_id";
            $conn->query($clear_cart);

            $success = "Order placed successfully! Order ID: #$order_id";
            header("Location: orders.php?order_success=$order_id");
            exit();
        } else {
            $error = "Failed to place order: " . $conn->error;
        }
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="cart-page">
    <div class="container">
        <div class="section-title">
            <h2>Checkout</h2>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Order Summary -->
            <div class="cart-container">
                <h3>Order Summary</h3>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>PKR <?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="cart-total">
                    Total: PKR <?php echo number_format($total, 2); ?>
                </div>
            </div>

            <!-- Checkout Form -->
            <div class="cart-container">
                <h3>Shipping Information</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Shipping Address *</label>
                        <textarea name="shipping_address" rows="4" required placeholder="Enter your full shipping address"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" required placeholder="Enter your phone number">
                    </div>
                    <div class="form-group">
                        <label>Payment Method *</label>
                        <select name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cod">Cash on Delivery</option>
                            <option value="easypaisa">Easypaisa</option>
                            <option value="jazzcash">JazzCash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success" style="width: 100%;">
                        <i class="fas fa-check"></i> Place Order
                    </button>
                    <a href="cart.php" class="btn btn-warning" style="width: 100%; margin-top: 10px; text-align: center;">
                        <i class="fas fa-arrow-left"></i> Back to Cart
                    </a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
