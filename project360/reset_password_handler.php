<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'session_handler.php';
require_once 'header-loader.php';
require_once 'db_connection.php';

$reset_message = '';
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (!$token || !$new_password) {
        $reset_message = 'Invalid request: Token or password missing.';
        $error = true;
    } elseif (!$conn) {
        $reset_message = 'Database connection failed: ' . mysqli_connect_error();
        $error = true;
    } else {
        // Verify token
        $stmt = $conn->prepare('SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()');
        if (!$stmt) {
            $reset_message = 'Database error: ' . $conn->error;
            $error = true;
        } else {
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows !== 1) {
                $reset_message = 'Invalid or expired token.';
                $error = true;
            } else {
                $user = $result->fetch_assoc();
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password
                $update_stmt = $conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
                if (!$update_stmt) {
                    $reset_message = 'Database error: ' . $conn->error;
                    $error = true;
                } else {
                    $update_stmt->bind_param('si', $hashed_password, $user['user_id']);
                    if (!$update_stmt->execute()) {
                        $reset_message = 'Failed to update password: ' . $update_stmt->error;
                        $error = true;
                    } else {
                        // Clean up token
                        $delete_stmt = $conn->prepare('DELETE FROM password_resets WHERE token = ?');
                        if ($delete_stmt) {
                            $delete_stmt->bind_param('s', $token);
                            $delete_stmt->execute();
                        }
                        $reset_message = 'Your password has been successfully reset.';
                    }
                    $update_stmt->close();
                }
            }
            $stmt->close();
        }
    }
    $conn->close();
} else {
    http_response_code(405);
    $reset_message = 'Method not allowed.';
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Password - MV Electronics</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="home.css" />
  <link rel="stylesheet" href="header.css" />
  <link rel="stylesheet" href="footer.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
</head>
<body>

<?php require_once 'header.php'; ?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="alert <?php echo $error ? 'alert-danger' : 'alert-success'; ?> text-center shadow-sm">
        <?php echo $reset_message; ?>
        <?php if (!$error): ?>
          <div class="mt-3">
            <a href="login.php" class="btn btn-success">Go to Login</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>

<script src="loginheader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
