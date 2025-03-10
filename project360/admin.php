<?php
// Include session check
require_once 'session_check.php';
checkAdminLogin();

// Get admin info from session
$adminId = $_SESSION['admin_id'] ?? '';
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? '';

// Get profile image path 
$profileImage = ""; // Default image path

// Try to fetch profile image from database
require_once 'db_connection.php';
try {
    $stmt = $conn->prepare("SELECT profile_image FROM admins WHERE user_id = ?");
    $stmt->bind_param("s", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!empty($row['profile_image'])) {
            $profileImage = $row['profile_image'];
        } else {
            // Default image if not set in database
            $profileImage = "images/default_admin.jpg";
        }
    }
} catch (Exception $e) {
    // If there's an error, use default image
    $profileImage = "images/default_admin.jpg";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <!-- Top Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand fw-bold" href="admin.php">
        <i class="bi bi-speedometer2 me-2"></i> Admin Panel
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a href="admin.php" class="nav-link active"><i class="bi bi-house-door me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a href="admin.product.html" class="nav-link"><i class="bi bi-box me-2"></i> Products</a></li>
            <li class="nav-item"><a href="admin.order.html" class="nav-link"><i class="bi bi-cart me-2"></i> Orders</a></li>
            <li class="nav-item"><a href="admin.user.html" class="nav-link"><i class="bi bi-people me-2"></i> Users</a></li>
        </ul>

        <!-- Profile Dropdown --> 
        <div class="ms-auto">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdownLg" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile" class="rounded-circle me-2" width="30" height="30">
                        <span><?php echo htmlspecialchars($adminName); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="admin_profile.php"><i class="bi bi-person-circle me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item text-danger" href="admin_logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- Main Content -->
    <div class="main-content p-4 mt-5 mx-4">
        <div class="container-fluid">
            <h2 class="mb-4">Dashboard</h2>

            <!-- Stats Overview -->
            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-primary p-3">
                        <h5>Active Listings</h5>
                        <h3 id="activeListings">0</h3>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-danger p-3">
                        <h5>Out of Stock</h5>
                        <h3 id="outOfStock">0</h3>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-success p-3">
                        <h5>Total Orders</h5>
                        <h3 id="totalOrders">0</h3>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-warning p-3">
                        <h5>Total Revenue</h5>
                        <h3 id="totalRevenue">$0</h3>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="mt-4">
                <h4>Recent Orders</h4>
                <div class="table-responsive">
                    <table class="table table-hover mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer ID</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="orderTableBody">
                            <!--Our Future Step : Orders will be dynamically can be added in further steps here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>

</body>
</html>