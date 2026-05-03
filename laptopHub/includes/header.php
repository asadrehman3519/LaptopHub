<?php require_once __DIR__ . '/config.php'; require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopHub - Online Laptop Store</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="nav-left">
                    <div class="nav-brand">
                        <a href="<?php echo BASE_URL; ?>/index.php">
                            <i class="fas fa-laptop"></i> LaptopHub
                        </a>
                    </div>
                </div>
                <div class="nav-center">
                    <div class="search-bar">
                        <form action="<?php echo BASE_URL; ?>/search.php" method="GET">
                            <input type="text" name="q" placeholder="Search laptops, accessories..." id="searchInput">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="nav-right">
                    <ul class="nav-menu">
                    <li>
                        <a href="<?php echo BASE_URL; ?>/index.php" class="nav-icon" data-tooltip="Home">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/products.php" class="nav-icon" data-tooltip="Laptops">
                            <i class="fas fa-laptop"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/accessories.php" class="nav-icon" data-tooltip="Accessories">
                            <i class="fas fa-tools"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/deals.php" class="nav-icon deals-link" data-tooltip="Deals">
                            <i class="fas fa-fire"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/contact.php" class="nav-icon" data-tooltip="Contact">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </li>
                    <?php if(isLoggedIn()): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/cart.php" class="nav-icon" data-tooltip="Cart">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count"><?php echo getCartCount(); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/profile.php" class="nav-icon" data-tooltip="Profile">
                                <i class="fas fa-user"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/logout.php" class="nav-icon" data-tooltip="Logout">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/login.php" class="nav-icon" data-tooltip="Login">
                                <i class="fas fa-sign-in-alt"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/register.php" class="nav-icon btn-register" data-tooltip="Register">
                                <i class="fas fa-user-plus"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    </ul>
                </div>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>
    <main>
