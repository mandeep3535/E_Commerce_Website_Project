<?php
// Start session
session_start();

// Include database connection
require_once 'db_connection.php';
// The echo "Connected successfully \n"; in dbconnection.php might cause issues in production
// You might want to remove that line from dbconnection.php

// Function to sanitize user inputs
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Process the form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize user inputs
    $user_input = sanitize_input($_POST["email"]); // This could be email or phone
    $password = $_POST["password"]; // Don't sanitize password before verification
    
    // Check if input is email or phone number
    $is_email = filter_var($user_input, FILTER_VALIDATE_EMAIL);
    
    // Prepare SQL statement based on input type
    if ($is_email) {
        // If input is email
        $sql = "SELECT * FROM users WHERE email = ?";
        $email = $user_input;
    } 
    
    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_input);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        // User found
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user["password"])) {
            // Password is correct - set session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["email"] = $email;
            $_SESSION["username"] = $user["username"];
            
            // Redirect to home page or dashboard
            header("location: dashboard.php");
            exit;
        } else {
            // Password is incorrect
            $login_err = "Invalid password";
        }
    } else {
        // User not found
        $login_err = "No account found with that email/phone";
    }
    
    // Close statement
    $stmt->close();
}

// If there was an error, you can redirect back to login with error message
if (isset($login_err)) {
    $_SESSION["login_error"] = $login_err;
    header("location: login.html");
    exit;
}
?>