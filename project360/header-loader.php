<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Output the header stylesheet
echo '<link rel="stylesheet" href="header.css" type="text/css">';
echo '<link rel="stylesheet" href="login.css" type="text/css">';
echo '<link rel="stylesheet" href="footer.css" type="text/css">';

// Default to 0
$wishlistCount = 0;
$cartCount = 0;

// If user is logged in => fetch wishlistCount from DB
if (isset($_SESSION["user_id"])) {
    require_once "db_connection.php";
    $user_id = (int) $_SESSION["user_id"];

    // Query the DB for how many wishlist items this user has
    $countSql = "SELECT COUNT(*) AS total FROM wishlist WHERE user_id = ?";
    $stmt = $conn->prepare($countSql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $wishlistCount = (int)$row["total"];
    }
    $stmt->close();

    // Store it in a session or pass it to loginheader 
    $_SESSION['wishlist_count'] = $wishlistCount;

    $cartSql = "SELECT IFNULL(SUM(quantity), 0) AS total FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($cartSql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $cartCount = (int)$row["total"];
    }
    $stmt->close();
    $_SESSION['cart_count'] = $cartCount;

    
    // Now include loginheader
    include "loginheader.php";

    // Output the login header script
    echo '<script src="loginheader.js"></script>';
} else {
    // If not logged in, show the standard header
    include "header.php";
}
?>
