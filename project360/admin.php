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

// Database connection
require_once 'db_connection.php';

// Try to fetch profile image from database
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

// Fetch dashboard data
// 1. Active Listings (Total number of products)
$activeListings = 0;
try {
    $query = "SELECT COUNT(*) as count FROM products";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $activeListings = $row['count'];
    }
} catch (Exception $e) {
    // Error handling
}

// 2. Out of Stock Products
$outOfStock = 0;
try {
    $query = "SELECT COUNT(*) as count FROM products WHERE stock = 0";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $outOfStock = $row['count'];
    }
} catch (Exception $e) {
    // Error handling
}

// 3. Total Orders
$totalOrders = 0;
try {
    $query = "SELECT COUNT(*) as count FROM orders";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $totalOrders = $row['count'];
    }
} catch (Exception $e) {
    // Error handling
}

// 4. Total Revenue
$totalRevenue = 0;
try {
    $query = "SELECT SUM(total_price) as revenue FROM orders";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $totalRevenue = $row['revenue'] ?? 0;
    }
} catch (Exception $e) {
    // Error handling
}

// 5. Fetch data for revenue by customer chart
$revenueByCustomer = [];
try {
    $query = "SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as customer_name, 
              SUM(o.total_price) as total_spent 
              FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              GROUP BY o.user_id 
              ORDER BY total_spent DESC 
              LIMIT 10";
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $revenueByCustomer[] = $row;
        }
    }
} catch (Exception $e) {
    // Error handling
}

// 6. Fetch data for category distribution chart
$categoryData = [];
try {
    $query = "SELECT category, COUNT(*) as product_count 
              FROM products 
              GROUP BY category";
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categoryData[] = $row;
        }
    }
} catch (Exception $e) {
    // Error handling
}

// 7. Fetch monthly sales data
$monthlySales = [];
try {
    $query = "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, 
              SUM(total_price) as sales 
              FROM orders 
              GROUP BY DATE_FORMAT(order_date, '%Y-%m') 
              ORDER BY month ASC 
              LIMIT 12";
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $monthlySales[] = $row;
        }
    }
} catch (Exception $e) {
    // Error handling
}

// Close the database connection
$conn->close();
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
    <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">
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
            <li class="nav-item"><a href="admin_order.php" class="nav-link"><i class="bi bi-cart me-2"></i> Orders</a></li>
            <li class="nav-item"><a href="admin_user.php" class="nav-link"><i class="bi bi-people me-2"></i> Users</a></li>
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
            <div class="row g-4 mb-5">
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-primary p-3">
                        <h5>Active Listings</h5>
                        <h3 id="activeListings"><?php echo $activeListings; ?></h3>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-danger p-3">
                        <h5>Out of Stock</h5>
                        <h3 id="outOfStock"><?php echo $outOfStock; ?></h3>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-success p-3">
                        <h5>Total Orders</h5>
                        <h3 id="totalOrders"><?php echo $totalOrders; ?></h3>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-warning p-3">
                        <h5>Total Revenue</h5>
                        <h3 id="totalRevenue">$<?php echo number_format($totalRevenue, 2); ?></h3>
                    </div>
                </div>
            </div>
            
            <!-- Analytics Charts -->
            <div class="row g-4">
                <!-- Monthly Sales Chart -->
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Monthly Sales</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Revenue Chart -->
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Top Customers by Revenue</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="customerRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Category Distribution Chart -->
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Product Categories</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pass PHP data to JavaScript -->
    <script>
        // Passing PHP data to JavaScript variables
        const revenueByCustomer = <?php echo json_encode($revenueByCustomer); ?>;
        const categoryData = <?php echo json_encode($categoryData); ?>;
        const monthlySales = <?php echo json_encode($monthlySales); ?>;
    </script>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="admin.js"></script>
    
</body>
</html>