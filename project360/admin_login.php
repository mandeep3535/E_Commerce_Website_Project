<?php
// Start session
session_start();

// Include database connection
require_once 'db_connection.php';

// Handle the login request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user inputs
    $userId = isset($_POST['userId']) ? trim($_POST['userId']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // Prepare response array
    $response = array('success' => false, 'message' => '');
    
    // Basic validation
    if (empty($userId) || empty($password)) {
        $response['message'] = 'Please enter both User ID and password';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Check if user exists in database
        $sql = "SELECT * FROM admins WHERE user_id = ? OR email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $userId, $userId); // Allow login with user_id or email
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            
            // Verify password
            if ($password === $admin['password']) { 
                // Set session variables
                $_SESSION['admin_id'] = $admin['user_id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_logged_in'] = true;
                
                // Success response
                $response['success'] = true;
                $response['message'] = 'Login successful! Redirecting...';
            } else {
                $response['message'] = 'Invalid password. Please try again.';
            }
        } else {
            $response['message'] = 'Admin not found. Please check your User ID.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
    
    // Return JSON response
    echo json_encode($response);
    exit;
}
?>