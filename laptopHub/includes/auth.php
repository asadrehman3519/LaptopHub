<?php
// Authentication Functions

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserName() {
    return $_SESSION['user_name'] ?? '';
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? 'user';
}

function getCartCount() {
    global $conn;
    if (!isLoggedIn()) return 0;
    $user_id = getCurrentUserId();
    $result = $conn->query("SELECT SUM(quantity) as count FROM cart WHERE user_id = $user_id");
    $row = $result->fetch_assoc();
    return $row['count'] ?? 0;
}
?>
