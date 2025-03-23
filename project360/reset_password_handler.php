<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connection.php';

header('Content-Type: text/html');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

$token = $_POST['token'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (!$token || !$new_password) {
    die('Invalid request: Token or password missing');
}

if (!$conn) {
    http_response_code(500);
    die('Database connection failed: ' . mysqli_connect_error());
}

// Verify token
$stmt = $conn->prepare('SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()');
if (!$stmt) {
    die('Database error: ' . $conn->error);
}
$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die('Invalid or expired token');
}

$user = $result->fetch_assoc();
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password
$stmt = $conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
if (!$stmt) {
    die('Database error: ' . $conn->error);
}
$stmt->bind_param('si', $hashed_password, $user['user_id']);
if (!$stmt->execute()) {
    die('Failed to update password: ' . $stmt->error);
}

// Clean up token
$stmt = $conn->prepare('DELETE FROM password_resets WHERE token = ?');
if (!$stmt) {
    die('Database error: ' . $conn->error);
}
$stmt->bind_param('s', $token);
$stmt->execute();

echo 'Password successfully reset. <a href="login.php">Login</a>';

$stmt->close();
$conn->close();
?>