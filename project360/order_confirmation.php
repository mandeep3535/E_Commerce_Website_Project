<?php
ob_start(); 

// Validate session and required files
require_once "session_handler.php";
require_once "header-loader.php";
require_once "db_connection.php";

// Check if order ID is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Location: home.php');
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
  <style>
    #initial-loader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
  </style>
</head>
<body>
  <div id="initial-loader">
    <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;"></div>
  </div>
<?php

ob_flush();
flush();


// Fetch order details
$orderSql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$orderStmt = $conn->prepare($orderSql);
$orderStmt->bind_param("ii", $order_id, $user_id);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

if ($orderResult->num_rows == 0) {
    header('Location: home.php');
    exit();
}

$orderData = $orderResult->fetch_assoc();

// Fetch order items
$itemsSql = "SELECT oi.*, p.name, p.images 
             FROM orderitems oi 
             JOIN products p ON oi.product_id = p.product_id 
             WHERE oi.order_id = ?";
$itemsStmt = $conn->prepare($itemsSql);
$itemsStmt->bind_param("i", $order_id);
$itemsStmt->execute();
$itemsResult = $itemsStmt->get_result();

// Load PHPMailer
require 'mailer/PHPMailer.php';
require 'mailer/SMTP.php';
require 'mailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch customer email and name
$userSql = "SELECT email, user_name FROM users WHERE user_id = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();

$customerEmail = $userData['email'];
$customerName  = $userData['user_name'];

// Prepare order summary (plain text)
$orderSummary = "Order ID: $order_id\n";
$orderSummary .= "Order Date: " . date('F j, Y, g:i a', strtotime($orderData['order_date'])) . "\n";
$orderSummary .= "Status: " . ucfirst($orderData['status']) . "\n";
$orderSummary .= "Payment: " . $orderData['payment_method'] . "\n\n";
$orderSummary .= "Items:\n";

$itemsResult->data_seek(0); // Reset result pointer
while ($item = $itemsResult->fetch_assoc()) {
    $orderSummary .= "- {$item['name']} x {$item['quantity']} = $" . number_format($item['price'] * $item['quantity'], 2) . "\n";
}
$orderSummary .= "\nTotal: $" . number_format($orderData['total_price'], 2);

// Email both customer and admin
$recipients = [
    [$customerEmail, $customerName],
    ['mvelectronics31@gmail.com', 'Admin']
];

foreach ($recipients as $recipient) {
    $toEmail = $recipient[0];
    $toName = $recipient[1];
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mvelectronics31@gmail.com';
        $mail->Password   = 'dzvbtsppjvyoukhk';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('mvelectronics31@gmail.com', 'MV Electronics');
        $mail->addAddress($toEmail, $toName);
        $mail->addReplyTo('mvelectronics31@gmail.com', 'MV Electronics');

        if ($toEmail === 'mvelectronics31@gmail.com') {
            // Email to admin
            $mail->Subject = "New Order Placed - Order #$order_id";
            $mail->Body    = "Hello Admin,\n\nA new order has been placed by $customerName ($customerEmail).\n\n$orderSummary\n\nLogin to the dashboard to view more details.";
        } else {
            // Email to customer
            $mail->Subject = "Your Order Confirmation - Order #$order_id";
            $mail->Body    = "Hi $toName,\n\nThank you for shopping with MV Electronics! Your order has been received and is being processed.\n\n$orderSummary\n\nWe'll notify you once your order is shipped.\n\nBest regards,\nMV Electronics Team";
        }

        $mail->send();
    } catch (Exception $e) {
        error_log("Mail error to $toEmail: " . $mail->ErrorInfo);
    }
}
//output buffering
ob_end_flush();
?>


<!-- Main Content -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">Order Confirmed!</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                        <h4 class="mt-3">Thank you for your order!</h4>
                        <p>Your order #<?php echo $order_id; ?> has been placed successfully.</p>
                        <p>You will receive an email shortly. Please check your spam or junk folder if you don't see it soon.</p>
                    </div>
                    
                    <div class="order-details mb-4">
                        <h5 class="border-bottom pb-2">Order Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($orderData['order_date'])); ?></p>
                                <p><strong>Order Status:</strong> <?php echo ucfirst($orderData['status']); ?></p>
                                <p><strong>Payment Method:</strong> <?php echo $orderData['payment_method']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Delivery Address:</strong><br>
                                <?php echo nl2br(str_replace(', ', ",\n", $orderData['delivery_address'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php $itemsResult->data_seek(0); ?>
                    <div class="order-items">
                        <h5 class="border-bottom pb-2">Order Items</h5>
                        
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = $itemsResult->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php 
                                                    $decoded = json_decode($item['images'], true);
                                                    if (is_array($decoded)) {
                                                        $firstImage = trim($decoded[0]);
                                                    } else if (strpos($item['images'], ',') !== false) {
                                                        $parts = explode(',', $item['images']);
                                                        $firstImage = trim($parts[0]);
                                                    } else {
                                                        $firstImage = $item['images'];
                                                    }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($firstImage); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="40" class="me-2">
                                                <?php echo $item['name']; ?>
                                            </div>
                                        </td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                                    <td><strong>$<?php echo number_format($orderData['total_price'], 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="home.php" class="btn btn-danger">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "footer.php"; ?>

<script>
window.onload = function () {
  const loader = document.getElementById("initial-loader");
  if (loader) {
    setTimeout(() => {
      loader.remove(); 
      console.log("Loader removed after page load.");
    }, 500);
  }
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="loginheader.js"></script>
</body>
</html>
