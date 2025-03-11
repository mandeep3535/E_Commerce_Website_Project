<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load the correct header dynamically
include isset($_SESSION["user_id"]) ? "loginheader.php" : "header.php";
?>
