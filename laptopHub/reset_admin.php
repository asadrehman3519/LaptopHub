<?php
require_once 'includes/config.php';

// Update admin password to admin123
$new_password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "UPDATE users SET password = '$new_password' WHERE email = 'admin@laptophub.com'";

if ($conn->query($sql)) {
    echo "Admin password has been reset successfully!<br>";
    echo "Email: admin@laptophub.com<br>";
    echo "Password: admin123<br><br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "Error: " . $conn->error;
}
?>
