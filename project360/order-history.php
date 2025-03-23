<?php
require_once "session_handler.php";
require_once 'db_connection.php'; 
require_once "header-loader.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Order History</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  
  <style>
    /* Custom styles */

    .order-history-card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
   
  </style>
      <link rel = "stylesheet" href = "footer.css">
      <link rel = "stylesheet" href = "header.css">
</head>
<body>
 
  <div class="container my-4">
    <!-- Breadcrumb -->
<nav class="breadcrumb mb-4">
  <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
  <a class="breadcrumb-item text-decoration-none text-muted" href="account.php">Account</a>
  <span class="breadcrumb-item active text-secondary fw-bold" aria-current="page">Orders</span>
</nav>
    <h1 class="text-center mb-4">My Order History</h1>
    
    <!-- Wrap in a Card for a sleek look -->
    <div class="card order-history-card mb-5">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col">Order #</th>
                <th scope="col">Product Name</th>
                <th scope="col">Units</th>
                <th scope="col">Order Date</th>
                <th scope="col">Delivery Date</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
            <tbody>
                <?php
                if ($is_logged_in && isset($user_id)) {
                    $sql = "SELECT o.order_id, o.order_date, o.delivery_date, o.status, 
                                  oi.quantity, p.name AS product_name
                            FROM orders o
                            JOIN orderitems oi ON o.order_id = oi.order_id
                            JOIN products p ON oi.product_id = p.product_id
                            WHERE o.user_id = ?
                            ORDER BY o.order_date DESC";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Map status to Bootstrap badge class
                            $statusClass = match(strtolower($row['status'])) {
                                'pending'   => 'bg-warning text-dark',
                                'shipped'   => 'bg-secondary',
                                'delivered' => 'bg-success',
                                'canceled'  => 'bg-danger',
                                default     => 'bg-light text-dark'
                            };
                            echo "<tr>
                            <td><a href=\"order_details.php?order_id={$row['order_id']}\" style=\"color: black; text-decoration: underline;\">#{$row['order_id']}</a></td>
                            <td>" . htmlspecialchars($row['product_name']) . "</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['order_date']}</td>
                            <td>" . ($row['delivery_date'] ?? '-') . "</td>
                            <td><span class='badge {$statusClass}'>" . ucfirst($row['status']) . "</span></td>
                          </tr>";
                    
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted'>No orders found.</td></tr>";
                    }

                    $stmt->close();
                } else {
                    echo "<tr><td colspan='6' class='text-center text-muted'>Please log in to view your orders.</td></tr>";
                }
                ?>
                </tbody>

            </tbody>
          </table>
        </div> 
      </div>
    </div> 
  </div> 
  <?php
require_once "footer.php";
?>
<script src="loginheader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
