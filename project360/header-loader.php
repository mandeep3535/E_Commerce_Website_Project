<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Output the header stylesheet
echo '<link rel="stylesheet" href="header.css" type="text/css">';
echo '<link rel="stylesheet" href="login.css" type="text/css">';
echo '<link rel="stylesheet" href="footer.css" type="text/css">';


// Check if the user is logged in
if (isset($_SESSION["user_id"])) {
    include "loginheader.php";
    // Output the login header script
    echo '<script src="loginheader.js"></script>';
} else {
    include "header.php";
}
?>
