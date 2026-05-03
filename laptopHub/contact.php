<?php
require_once 'includes/config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // Here you would typically send an email or save to database
    $success = "Thank you for contacting us! We'll get back to you soon.";
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="cart-page">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-envelope"></i> Contact Us</h2>
            <p>Have questions? We're here to help!</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            <!-- Contact Form -->
            <div class="cart-container">
                <h3>Send us a Message</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="name" required placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label>Subject *</label>
                        <input type="text" name="subject" required placeholder="What is this about?">
                    </div>
                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" rows="5" required placeholder="Your message..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="cart-container">
                <h3>Contact Information</h3>
                <div style="margin-top: 2rem;">
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="color: #667eea;"><i class="fas fa-map-marker-alt"></i> Address</h4>
                        <p>123 Tech Street, Digital City<br>Pakistan</p>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="color: #667eea;"><i class="fas fa-phone"></i> Phone</h4>
                        <p>+92 300 1234567<br>+92 21 34567890</p>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="color: #667eea;"><i class="fas fa-envelope"></i> Email</h4>
                        <p>support@laptophub.com<br>sales@laptophub.com</p>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="color: #667eea;"><i class="fas fa-clock"></i> Business Hours</h4>
                        <p>Monday - Friday: 9AM - 6PM<br>Saturday: 10AM - 4PM<br>Sunday: Closed</p>
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <h4 style="margin-bottom: 1rem;">Follow Us</h4>
                    <div style="display: flex; gap: 1rem;">
                        <a href="#" style="font-size: 2rem; color: #667eea;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="font-size: 2rem; color: #667eea;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="font-size: 2rem; color: #667eea;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="font-size: 2rem; color: #667eea;"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
