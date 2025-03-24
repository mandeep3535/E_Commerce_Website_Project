<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connection.php';
require 'mailer/PHPMailer.php';
require 'mailer/SMTP.php';
require 'mailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

// Check if email exists
$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'Email not found']);
    exit;
}

$user = $result->fetch_assoc();
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Store reset token
$stmt = $conn->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param('iss', $user['user_id'], $token, $expires);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to store reset token: ' . $stmt->error]);
    exit;
}

// Send email
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mvelectronics31@gmail.com';
    $mail->Password = 'dzvbtsppjvyoukhk'; // Replace with valid app password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('mvelectronics31@gmail.com', 'MV Electronics');
    $mail->addAddress($email);
    $mail->Subject = 'Password Reset Request';
 //   $resetLink = "https://cosc360.ok.ubc.ca/vgarg28/project360/project360/reset_password.php?token=$token";
    $resetLink = "https://cosc360.ok.ubc.ca/msingh78/project360/project360/reset_password.php?token=$token";
    $mail->Body = "Hello,\n\nClick this link to reset your password:\n$resetLink\n\nThis link expires in 1 hour.";

    $mail->send();
    echo json_encode(['success' => true,  'message' => 'Reset link sent to your email. If you didn\'t receive it, please check your spam or junk folder.']);
} catch (Exception $e) {
    // Clean up token if email fails
    $conn->query("DELETE FROM password_resets WHERE token = '$token'");
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}

$stmt->close();
$conn->close();
?>