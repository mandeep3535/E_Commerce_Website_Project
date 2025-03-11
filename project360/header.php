<?php
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - MV Electronics' : 'MV Electronics'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="header.css">
    <?php if(isset($additionalCss)): ?>
        <?php foreach($additionalCss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
<header>
    <!-- Top Banner -->
    <div class="top-banner text-center py-2">
        Back to School deals up to <strong>50% OFF</strong> and Free Express Delivery! <a href="home.php" class="shop-now">Shop Now</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="home.php">
                <img src="images/logo1.png" alt="MV Electronics" width="135" height="auto">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse text-center d-lg-none" id="navbarNav">
                <form class="d-flex me-auto" role="search" method="GET" action="search.php" id="searchBar">
                    <input class="form-control me-2" type="search" name="query" placeholder="Search products..." aria-label="Search">
                    <button class="btn btn-outline-dark" type="submit">Search</button>
                </form>
                <ul class="navbar-nav ms-auto me-5">
                    <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'home.php') ? 'active' : ''; ?>" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<?php if(isset($headerContent) && $headerContent): ?>
    <?php echo $headerContent; ?>
<?php endif; ?>

<?php if(isset($includeBootstrapJS) && $includeBootstrapJS): ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php endif; ?>