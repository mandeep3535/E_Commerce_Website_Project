<?php
// Start session
session_start();

// Check if the user is logged in, if not redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// Include database connection
require_once 'db_connection.php';

// Get user details from database using the email stored in session
// First, we need to make sure we have the email stored in session
if(isset($_SESSION["email"])) {
    $email = $_SESSION["email"];
} else {
    // If email is not in session for some reason, redirect to login
    session_destroy();
    header("location: login.html");
    exit;
}

// Query to get user details using email
$sql = "SELECT * FROM users WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    
    // Debug to see what fields are actually available in your database
    // echo "<pre>"; print_r($user); echo "</pre>";
    
    // Check if username field exists, if not use name or first_name or whatever is available
    if(isset($user["username"])) {
        $user_name = $user["username"];
    } elseif(isset($user["name"])) {
        $user_name = $user["name"];
    } elseif(isset($user["first_name"])) {
        $user_name = $user["first_name"];
    } elseif(isset($user["user_name"])) {
        $user_name = $user["user_name"];
    } else {
        // If no name field is available, use email as a fallback
        $user_name = $email;
    }
} else {
    // If user not found in database (should not happen unless session is corrupted)
    session_destroy();
    header("location: login.html");
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
    <!--Header-->
    <div id="header"></div>
    
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
                        
                        <!-- Additional dashboard content can be added here -->
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
    
    <!--Footer-->
    <div id="footer"></div>
    
    <script src="hfload.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>