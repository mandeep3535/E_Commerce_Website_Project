<?php
// Include the database connection
require_once 'db_connection.php';

// Check if user_id is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = mysqli_real_escape_string($conn, $_POST['user_id']);
    
    // Delete the user
    $query = "DELETE FROM users WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        // Redirect back to the users page with success message
        header("Location: admin_user.php?message=User deleted successfully");
        exit;
    } else {
        // Redirect back with error message
        header("Location: admin_user.php?error=" . urlencode("Error deleting user: " . mysqli_error($conn)));
        exit;
    }
} else {
    // If accessed directly without proper parameters
    header("Location: admin_user.php");
    exit;
}
?>