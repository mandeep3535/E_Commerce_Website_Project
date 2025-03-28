<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = isset($_POST['userId']) ? trim($_POST['userId']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    $response = ['success' => false, 'message' => ''];

    if (empty($userId) || empty($password)) {
        $response['message'] = 'Please enter both User ID and password';
        echo json_encode($response);
        exit;
    }

    try {
        $sql = "SELECT * FROM admins WHERE user_id = ? OR email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();

            // Secure password verification
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['user_id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_logged_in'] = true;

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

    echo json_encode($response);
    exit;
}
?>
