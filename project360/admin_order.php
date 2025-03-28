<?php
// Include database connection
include 'db_connection.php';

// Fetch orders with exact field names from your database
$query = "SELECT order_id, user_id, total_price, order_date, delivery_date, 
          delivery_address, payment_method, status FROM orders ORDER BY order_date DESC";
$result = mysqli_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die("Database error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Panel</title>
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
                    <a href="admin.product.html" class="nav-link">
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
    <div class="container-fluid my-4">
        <h2>Customer Orders</h2>

        <!-- Orders Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Total Price</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Delivery Address</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(mysqli_num_rows($result) > 0) {
                        while($order = mysqli_fetch_assoc($result)) {
                            // Get order items for this order
                            $order_id = $order['order_id'];
                            $items_query = "SELECT product_id, quantity FROM orderitems WHERE order_id = $order_id";
                            $items_result = mysqli_query($conn, $items_query);
                            
                            // Format date
                            $order_date = date('M d, Y', strtotime($order['order_date']));
                            $delivery_date = $order['delivery_date'] ? date('M d, Y', strtotime($order['delivery_date'])) : 'Not set';
                            
                            // Set status class for color coding
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
                            
                            echo "<tr>
                                    <td>{$order['order_id']}</td>
                                    <td>{$order['user_id']}</td>
                                    <td>\${$order['total_price']}</td>
                                    <td>{$order_date}</td>
                                    <td>{$delivery_date}</td>
                                    <td>{$order['delivery_address']}</td>
                                    <td>{$order['payment_method']}</td>
                                    <td class='{$status_class}'>" . ucfirst($order['status']) . "</td>
                                    <td>
                                        <a href='view_order.php?id={$order['order_id']}' class='btn btn-sm btn-danger'>
                                            <i class='bi bi-eye'></i>
                                        </a>
                                        <a href='update_order.php?id={$order['order_id']}' class='btn btn-sm btn-danger'>
                                            <i class='bi bi-pencil'></i>
                                        </a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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