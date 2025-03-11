<?php
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection
require_once 'db_connection.php';

// Initialize variables
$userName = 'User';
$profileImage = '';  // This can be a path, e.g. "uploads/default.png" if you want a default

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Prepare and execute query to get user data
    $sql = "SELECT user_name, profile_image FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch user data if it exists
    if ($row = $result->fetch_assoc()) {
        $userName = $row['user_name'];
        $profileImage = $row['profile_image']; // e.g. "uploads/profile123.jpg"
    }
    $stmt->close();
}
?>

<header>
    <!-- Top Banner -->
    <div class="top-banner text-center py-2">
        Back to School deals up to <strong>50% OFF</strong> and Free Express Delivery! 
        <a href="home.php" class="shop-now">Shop Now</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="home.php">
                <img src="images/logo1.png" alt="MV Electronics" width="135">
            </a>
            
            <!-- Mobile Cart and Wishlist -->
            <a href="cart.html" class="d-flex d-lg-none position-relative me-3">
                <i class="bi bi-cart fs-4"></i>
                <span 
                  id="cartCountBadgeMobile"
                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  style="display:none;"
                >0</span>
            </a>
            
            <a href="wishlist.html" class="d-flex d-lg-none position-relative me-3">
                <i class="bi bi-heart fs-4"></i>
                <span 
                  id="wishlistCountBadgeMobile"
                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  style="display:none;"
                >0</span>
            </a>
            
            <!-- Mobile Profile Dropdown -->
            <div class="dropdown d-flex d-lg-none">
                <button class="btn border-0 d-flex align-items-center" 
                        id="mobileProfileDropdown" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    
                    <?php if (!empty($profileImage)): ?>
                        <img 
                            src="<?php echo htmlspecialchars($profileImage); ?>" 
                            alt="Profile" 
                            class="rounded-circle me-2" 
                            style="width: 30px; height: 30px; object-fit: cover;"
                        >
                    <?php else: ?>
                        <i class="bi bi-person fs-4 me-2"></i>
                    <?php endif; ?>
                    
                    <span><?php echo htmlspecialchars($userName); ?></span>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileProfileDropdown">
                    <li><a class="dropdown-item" href="Account.html">Manage My Account</a></li>
                    <li><a class="dropdown-item" href="cart.html">Cart</a></li>
                    <li><a class="dropdown-item" href="wishlist.html">Wishlist</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>

            <!-- Search Bar -->
            <form class="d-flex mx-auto" role="search" method="GET" action="search.php">
                <input class="form-control me-2" type="search" name="query" placeholder="Search products..." aria-label="Search">
                <button class="btn btn-outline-dark" type="submit">Search</button>
            </form>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse text-center" id="navbarNav">
                <ul class="navbar-nav ms-auto me-5">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                </ul>
            </div>

            <!-- Desktop Icons Section (Wishlist, Cart, Profile) -->
            <div class="d-none d-lg-flex align-items-center">
                <a href="wishlist.html" class="me-3 position-relative">
                    <i class="bi bi-heart fs-4"></i>
                    <span id="wishlistCountBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">0</span>
                </a>

                <a href="cart.html" class="me-3 position-relative">
                    <i class="bi bi-cart fs-4"></i>
                    <span id="cartCountBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">0</span>
                </a>

                <!-- Desktop Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn border-0 d-flex align-items-center" 
                            id="profileDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        
                        <?php if (!empty($profileImage)): ?>
                            <img 
                                src="<?php echo htmlspecialchars($profileImage); ?>" 
                                alt="Profile" 
                                class="rounded-circle me-2" 
                                style="width: 30px; height: 30px; object-fit: cover;"
                            >
                        <?php else: ?>
                            <i class="bi bi-person fs-4 me-2"></i>
                        <?php endif; ?>
                        
                        <span><?php echo htmlspecialchars($userName); ?></span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="Account.html">Manage My Account</a></li>
                        <li><a class="dropdown-item" href="cart.html">Cart</a></li>
                        <li><a class="dropdown-item" href="wishlist.html">Wishlist</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>
