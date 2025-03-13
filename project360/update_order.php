<?php
// Include database connection
include 'db_connection.php';

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_order.php");
    exit;
}

$order_id = $_GET['id'];

// Fetch order details
$query = "SELECT order_id, delivery_date, delivery_address, payment_method, status 
          FROM orders WHERE order_id = $order_id";
$result = mysqli_query($conn, $query);

// Check if order exists
if (mysqli_num_rows($result) == 0) {
    header("Location: admin_order.php");
    exit;
}

$order = mysqli_fetch_assoc($result);

// Process form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $delivery_date = !empty($_POST['delivery_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['delivery_date']) . "'" : "NULL";
    $delivery_address = "'" . mysqli_real_escape_string($conn, $_POST['delivery_address']) . "'";
    $payment_method = "'" . mysqli_real_escape_string($conn, $_POST['payment_method']) . "'";
    $status = "'" . mysqli_real_escape_string($conn, $_POST['status']) . "'";
    
    // Update order
    $update_query = "UPDATE orders SET 
                    delivery_date = $delivery_date,
                    delivery_address = $delivery_address,
                    payment_method = $payment_method,
                    status = $status
                    WHERE order_id = $order_id";
    
    if (mysqli_query($conn, $update_query)) {
        $message = '<div class="alert alert-success">Order updated successfully!</div>';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="admin_order.css">
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand fw-bold" href="admin_php">
            <i class="bi bi-speedometer2 me-2"></i> Admin Panel
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left Side Navigation Links -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a href="admin.php" class="nav-link">
                        <i class="bi bi-house-door me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin.product.php" class="nav-link">
                        <i class="bi bi-box me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_order.php" class="nav-link active">
                        <i class="bi bi-cart me-2"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_user.php" class="nav-link">
                        <i class="bi bi-people me-2"></i> Users
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Update Order #<?php echo $order_id; ?></h2>
            <a href="admin_order.php" class="btn btn-danger">
                <i class="bi bi-arrow-left me-2"></i>Back to Orders
            </a>
        </div>
        
        <?php echo $message; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="delivery_date" class="form-label">Delivery Date</label>
                        <input type="date" class="form-control" id="delivery_date" name="delivery_date" value="<?php echo $order['delivery_date'] ? date('Y-m-d', strtotime($order['delivery_date'])) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="delivery_address" class="form-label">Delivery Address</label>
                        <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" required><?php echo $order['delivery_address']; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="credit" <?php echo ($order['payment_method'] == 'credit') ? 'selected' : ''; ?>>Credit Card</option>
                            <option value="debit" <?php echo ($order['payment_method'] == 'debit') ? 'selected' : ''; ?>>Debit Card</option>
                            <option value="paypal" <?php echo ($order['payment_method'] == 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                            <option value="bank_transfer" <?php echo ($order['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo ($order['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo ($order['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo ($order['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="completed" <?php echo ($order['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo ($order['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger">Update Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Close connection
mysqli_close($conn);
?>