<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user is not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once 'db_connection.php';

// Ensure email is set in session before using it
if (!isset($_SESSION["email"])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$email = $_SESSION["email"];

// Query to get user details using email
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();

    // Assign username or fallback to email
    $user_name = $user["username"] ?? $user["name"] ?? $user["first_name"] ?? $user["user_name"] ?? $email;
} else {
    // If user not found (session might be corrupted), logout and redirect
    session_destroy();
    header("Location: login.php");
    exit;
}

// Close statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MV Electronics - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- Header Section -->
<div id="header">
    <?php include isset($_SESSION["user_id"]) ? "loginheader.php" : "header.php"; ?>
</div>

<nav class="breadcrumb mt-4 ms-5">
    <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
    <span class="breadcrumb-item active text-secondary fw-bold">Dashboard</span>
</nav>

<!-- Dashboard Content -->
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2>Welcome to Your Dashboard</h2>
                </div>
                <div class="card-body">
                    <h3>Hello, <?php echo htmlspecialchars($user_name); ?>!</h3>
                    <p>You are logged in with email: <?php echo htmlspecialchars($email); ?></p>
                    
                    <!-- Dashboard Options -->
                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Your Orders</h5>
                                    <p class="card-text">View and track your orders</p>
                                    <a href="#" class="btn btn-primary">View Orders</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Your Wishlist</h5>
                                    <p class="card-text">Manage your wishlist items</p>
                                    <a href="#" class="btn btn-primary">View Wishlist</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Account Settings</h5>
                                    <p class="card-text">Update your profile information</p>
                                    <a href="#" class="btn btn-primary">Edit Profile</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer Section -->
<div id="footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
