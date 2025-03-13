<?php
// Include database connection
include 'db_connection.php';

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_order.php");
    exit;
}

$order_id = intval($_GET['id']); // Convert to integer for security

// Fetch order details using prepared statement
$query = "SELECT * FROM orders WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if order exists
if (mysqli_num_rows($result) == 0) {
    header("Location: admin_order.php");
    exit;
}

$order = mysqli_fetch_assoc($result);

// Fetch order items (modified to match the database schema)
$items_query = "SELECT * FROM orderitems WHERE order_id = ?";
$items_stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($items_stmt, "i", $order_id);
mysqli_stmt_execute($items_stmt);
$items_result = mysqli_stmt_get_result($items_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="admin.order.css">
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand fw-bold" href="admin.php">
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
            <h2>Order #<?php echo $order_id; ?> Details</h2>
            <div>
                <a href="update_order.php?id=<?php echo $order_id; ?>" class="btn btn-danger me-2">
                    <i class="bi bi-pencil me-2"></i>Edit Order
                </a>
                <a href="admin_order.php" class="btn btn-danger">
                    <i class="bi bi-arrow-left me-2"></i>Back to Orders
                </a>
            </div>
        </div>
        
        <div class="row">
            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
                        <p><strong>User ID:</strong> <?php echo $order['user_id']; ?></p>
                        <p><strong>Total Price:</strong> $<?php echo $order['total_price']; ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('M d, Y', strtotime($order['order_date'])); ?></p>
                        
                        <!-- Status with color coding -->
                        <?php
                        $status_class = '';
                        switch($order['status']) {
                            case 'pending':
                                $status_class = 'text-warning';
                                break;
                            case 'completed':
                                $status_class = 'text-success';
                                break;
                            case 'cancelled':
                                $status_class = 'text-danger';
                                break;
                            default:
                                $status_class = 'text-secondary';
                        }
                        ?>
                        <p><strong>Status:</strong> <span class="<?php echo $status_class; ?>"><?php echo ucfirst($order['status']); ?></span></p>
                    </div>
                </div>
                
                <!-- Shipping Details -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Shipping Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Delivery Address:</strong> <?php echo $order['delivery_address']; ?></p>
                        <p><strong>Delivery Date:</strong> 
                            <?php echo $order['delivery_date'] ? date('M d, Y', strtotime($order['delivery_date'])) : 'Not set'; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Payment Details -->
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Payment Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Item ID</th>
                                        <th>Product ID</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    if(mysqli_num_rows($items_result) > 0) {
                                        while($item = mysqli_fetch_assoc($items_result)) {
                                            $subtotal = $item['price'] * $item['quantity'];
                                            $total += $subtotal;
                                            ?>
                                            <tr>
                                                <td><?php echo $item['order_item_id']; ?></td>
                                                <td><?php echo $item['product_id']; ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td>$<?php echo $item['price']; ?></td>
                                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>No items found for this order</td></tr>";
                                    }
                                    ?>
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
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