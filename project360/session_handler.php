<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $is_logged_in = false;
    $user_id = null;
    $user_email = null;
} else {
    $is_logged_in = true;
    $user_id = $_SESSION["user_id"];
    $user_email = $_SESSION["email"];
}
?>
