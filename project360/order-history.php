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
  <a class="breadcrumb-item text-decoration-none text-muted" href="Account.html">Account</a>
  <span class="breadcrumb-item active text-secondary fw-bold" aria-current="page">Orders</span>
</nav>
    <h1 class="text-center mb-4">My Order History</h1>
    
    <!-- Wrap in a Card for a sleek look -->
    <div class="card order-history-card">
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
              <!-- row 1 -->
              <tr>
                <td>00123</td>
                <td>Wireless Headphones</td>
                <td>1</td>
                <td>2025-01-11</td>
                <td>2025-01-18</td>
                <td>
                  <span class="badge bg-success">Delivered</span>
                </td>
              </tr>
              <!-- row 2 -->
              <tr>
                <td>00456</td>
                <td>Gaming Mouse</td>
                <td>2</td>
                <td>2025-02-05</td>
                <td>2025-02-12</td>
                <td>
                  <span class="badge bg-secondary">Shipped</span>
                </td>
              </tr>
              <!-- row 3 -->
              <tr>
                <td>00789</td>
                <td>Bluetooth Speaker</td>
                <td>1</td>
                <td>2025-02-15</td>
                <td>2025-02-22</td>
                <td>
                  <span class="badge bg-warning text-dark">Pending</span>
                </td>
              </tr>
              <!--Rows can be added more rows  -->
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
