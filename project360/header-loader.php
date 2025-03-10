<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (isset($_SESSION["user_id"])) {
    include "loginheader.php"; // Header for logged-in users
} else {
    include "header.php"; // Header for guests
}
?>
