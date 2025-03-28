<?php
// Include dependencies
include 'db_connection.php';
require 'mailer/PHPMailer.php';
require 'mailer/SMTP.php';
require 'mailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_order.php");
    exit;
}

$order_id = $_GET['id'];

// Fetch order details
$query = "SELECT * FROM orders WHERE order_id = $order_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: admin_order.php");
    exit;
}

$order = mysqli_fetch_assoc($result);
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $new_delivery_date = !empty($_POST['delivery_date']) ? $_POST['delivery_date'] : null;
    $delivery_address = mysqli_real_escape_string($conn, $_POST['delivery_address']);
    $payment_method = $order['payment_method']; 
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_query = "UPDATE orders SET 
                        delivery_date = " . ($new_delivery_date ? "'$new_delivery_date'" : "NULL") . ",
                        delivery_address = '$delivery_address',
                        payment_method = '$payment_method',
                        status = '$new_status'
                     WHERE order_id = $order_id";

    if (mysqli_query($conn, $update_query)) {
        $message = '<div class="alert alert-success">Order updated successfully! An email notification has been sent to the customer.</div>';
        
        // Check if delivery date or status changed
        if ($order['delivery_date'] !== $new_delivery_date || $order['status'] !== $new_status) {
            // Get user info
            $user_id = $order['user_id'];
            $user_sql = "SELECT email, user_name FROM users WHERE user_id = $user_id";
            $user_result = mysqli_query($conn, $user_sql);
            $user = mysqli_fetch_assoc($user_result);

            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'mvelectronics31@gmail.com';
                $mail->Password = 'dzvbtsppjvyoukhk';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('mvelectronics31@gmail.com', 'MV Electronics');
                $mail->addAddress($user['email'], $user['user_name']);
                $mail->Subject = "Your Order #$order_id has been updated";
                $mail->Body = "Hi {$user['user_name']},\n\nYour order #$order_id has been updated with the following details:\n\nStatus: " . ucfirst($new_status) . "\nDelivery Date: " . ($new_delivery_date ?: 'Not set') . "\n\nThank you,\nMV Electronics Team";
                $mail->send();
            } catch (Exception $e) {
                error_log("Mail error: " . $mail->ErrorInfo);
            }
        }

        // Refresh order data
        $result = mysqli_query($conn, $query);
        $order = mysqli_fetch_assoc($result);
    } else {
        $message = '<div class="alert alert-danger">Error updating order: ' . mysqli_error($conn) . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Order - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand fw-bold" href="admin.php"><i class="bi bi-speedometer2 me-2"></i> Admin Panel</a>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Update Order #<?php echo $order_id; ?></h2>
        <a href="admin_order.php" class="btn btn-danger"><i class="bi bi-arrow-left me-2"></i>Back to Orders</a>
    </div>

    <?php echo $message; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="delivery_date" class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" name="delivery_date" value="<?php echo $order['delivery_date'] ? date('Y-m-d', strtotime($order['delivery_date'])) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="delivery_address" class="form-label">Delivery Address</label>
                    <textarea class="form-control" name="delivery_address" rows="3" required><?php echo htmlspecialchars($order['delivery_address']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" name="payment_method" disabled>
                        <option value="credit" <?php if ($order['payment_method'] == 'credit') echo 'selected'; ?>>Credit Card</option>
                        <option value="debit" <?php if ($order['payment_method'] == 'debit') echo 'selected'; ?>>Debit Card</option>
                        <option value="paypal" <?php if ($order['payment_method'] == 'paypal') echo 'selected'; ?>>PayPal</option>
                        <option value="bank_transfer" <?php if ($order['payment_method'] == 'bank_transfer') echo 'selected'; ?>>Bank Transfer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Order Status</label>
                    <select class="form-select" name="status" required>
                        <option value="pending" <?php if ($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="shipped" <?php if ($order['status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                        <option value="delivered" <?php if ($order['status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                        <option value="cancelled" <?php if ($order['status'] == 'canceled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-danger">Update Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>
