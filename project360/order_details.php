<?php
require_once "session_handler.php";
require_once "db_connection.php";
require_once "header-loader.php";

if (!isset($_GET['order_id']) || !$is_logged_in) {
    die("Unauthorized or missing order ID.");
}

$order_id = (int) $_GET['order_id'];

// Verify that the order belongs to this user
$order_sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    die("Order not found or access denied.");
}
$order = $order_result->fetch_assoc();

// Fetch items in this order
$items_sql = "SELECT oi.quantity, oi.price, p.name, p.images
              FROM orderitems oi
              JOIN products p ON oi.product_id = p.product_id
              WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order_id; ?> Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="header.css">
</head>
<body>

<div class="container my-4">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-4">
        <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
        <a class="breadcrumb-item text-decoration-none text-muted" href="account.html">Account</a>
        <a class="breadcrumb-item text-decoration-none text-muted" href="order-history.php">Orders</a>
        <span class="breadcrumb-item active text-secondary fw-bold" aria-current="page">Order #<?php echo $order_id; ?></span>
    </nav>

    <h2 class="mb-4 text-center">Order #<?php echo $order_id; ?> Details</h2>
    
    <div class="mb-3">
        <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
        <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
        <p><strong>Delivery Date:</strong> <?php echo $order['delivery_date'] ?? 'Not set'; ?></p>
    </div>

    <div class="table-responsive mb-5">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <a href="order-history.php" class="btn btn-danger">← Back to Order History</a>
</div>

<?php require_once "footer.php"; ?>

<script src="loginheader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>
