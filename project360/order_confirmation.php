<?php
// Ensure session is available
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="checkout.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h3 class="mb-0">Order Confirmed!</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-check-circle-fill text-dark" style="font-size: 5rem;"></i>
                            <h4 class="mt-3">Thank you for your order!</h4>
                            <p>Your order #<?php echo $order_id; ?> has been placed successfully.</p>
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
                                                    <img src="<?php echo $item['images']; ?>" alt="<?php echo $item['name']; ?>" width="40" class="me-2">
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
    <script src="hfload.js"></script>
    <script src="loginheader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>