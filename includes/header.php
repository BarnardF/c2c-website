<?php
require_once 'functions.php';
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

    <!-- used bootstrap
    https://www.w3schools.com/bootstrap/tryit.asp?filename=trybs_navbar_collapse&stacked=h
    -->
<header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

            <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><?php echo SITE_NAME; ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="browse_products.php">Products</a></li>

                        <?php if (logged_in()): ?>
                            <li class="nav-item"><a class="nav-link" href="add_product.php">Sell Item</a></li>
                            <li class="nav-item"><a class="nav-link" href="user_listings.php">My Listings</a></li>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <li class="nav-item"><a class="nav-link" href="admin_page.php">Admin Page</a></li>
                            <?php endif; ?>
                        <?php endif; ?>

                    </ul>

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                        <?php if (logged_in()): ?>
                            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="register.php"><i class="bi bi-person-plus"></i> Register</a></li>
                            <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
                        <?php endif; ?>

                    </ul>
                </div>

            </div>
            
        </nav>
    </header>
    <main>


