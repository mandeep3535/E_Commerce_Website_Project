<?php
// Ensure session is available
require_once "session_handler.php";
require_once "header-loader.php";
require_once "db_connection.php"; 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php?redirect=checkout.php");
    exit();
}

// Process order submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  error_log("Raw cart_items POST data: " . $_POST['cart_items']);
    $user_id = $_SESSION['user_id'];
    $province = $_POST['province'];
    $street_address = $_POST['street_address'];
    $apartment_info = isset($_POST['apartment_info']) ? $_POST['apartment_info'] : '';
    $city = $_POST['city'];
    $payment_method = $_POST['payment_method'];
    $total_price = $_POST['total_price'];
    
    // Combine address parts into one string
    $delivery_address = "$street_address, " . 
                        ($apartment_info ? "$apartment_info, " : "") . 
                        "$city, $province";
    
    // Insert into orders table
    $orderSql = "INSERT INTO orders (user_id, delivery_address, payment_method, total_price, status) 
                VALUES (?, ?, ?, ?, 'pending')";
    
    $stmt = $conn->prepare($orderSql);
    $stmt->bind_param("issd", $user_id, $delivery_address, $payment_method, $total_price);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id; 
        
        
        
        // Insert each item into the orderitems table
        $itemSql = "INSERT INTO orderitems (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $itemStmt = $conn->prepare($itemSql);
        
        
     $cart_items = json_decode($_POST['cart_items'], true);
if (!is_array($cart_items) || empty($cart_items)) {
    $error = "Your cart appears to be empty or invalid. Please try again.";
    
} else {
  foreach ($cart_items as $item) {
    if (!isset($item['id'])) {
      $findProductSql = "SELECT product_id FROM products WHERE name = ?";
      $findStmt = $conn->prepare($findProductSql);
      $findStmt->bind_param("s", $item['name']);
      $findStmt->execute();
      $findResult = $findStmt->get_result();
      
      if ($findResult->num_rows > 0) {
        $productRow = $findResult->fetch_assoc();
        $product_id = $productRow['product_id'];
      } else {
        error_log("Could not find product ID for: " . $item['name']);
        continue; // Skip this item
      }
    } else {
      $product_id = $item['id'];
    }
    
    $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
    $price = isset($item['price']) ? $item['price'] : 0;
    
    $itemStmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
    $itemStmt->execute();
  } 
}
        // Clear the cart after successful order
        echo "<script>
            localStorage.removeItem('cart');
            localStorage.removeItem('appliedCoupon');
            window.location.href = 'order_confirmation.php?order_id=$order_id';
        </script>";
        exit();
    } else {
        $error = "Error processing your order. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout</title>

  <link rel="stylesheet" href="checkout.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
</head>
<body>

<div class="container mt-4">
  <!-- Breadcrumb -->
  <nav class="breadcrumb mb-4">
    <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
    <a class="breadcrumb-item text-decoration-none text-muted" href="cart.php">Cart</a>
    <span class="breadcrumb-item active" aria-current="page">Checkout</span>
  </nav>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <div class="row">
    <!-- Left Column - Delivery Details -->
    <div class="col-lg-7">
      <h2 class="mb-4 fw-bold">Delivery Details</h2>
      <form id="deliveryForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="mb-3">
          <label for="provinceSelect" class="form-label">Province*</label>
          <select class="form-select" id="provinceSelect" name="province" required>
            <option value="">Select a province</option>
            <option value="AB">Alberta</option>
            <option value="BC">British Columbia</option>
            <option value="MB">Manitoba</option>
            <option value="NB">New Brunswick</option>
            <option value="NL">Newfoundland and Labrador</option>
            <option value="NS">Nova Scotia</option>
            <option value="ON">Ontario</option>
            <option value="PE">Prince Edward Island</option>
            <option value="QC">Quebec</option>
            <option value="SK">Saskatchewan</option>
            <option value="NT">Northwest Territories</option>
            <option value="YT">Yukon</option>
            <option value="NU">Nunavut</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Street Address*</label>
          <input type="text" class="form-control" name="street_address" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Apartment, floor, etc. (optional)</label>
          <input type="text" class="form-control" name="apartment_info" />
        </div>
        <div class="mb-3">
          <label class="form-label">Town/City*</label>
          <input type="text" class="form-control" name="city" required />
        </div>
        
        <!-- Hidden fields to store order data -->
        <input type="hidden" name="total_price" id="hiddenTotalPrice" value="0" />
        <input type="hidden" name="payment_method" id="hiddenPaymentMethod" value="Credit Card" />
        <input type="hidden" name="cart_items" id="hiddenCartItems" value="" />
        
        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="save-info" name="save_info" />
          <label class="form-check-label text-muted" for="save-info">
            Save this information for faster check-out next time
          </label>
        </div>
      </form>
    </div>

    <!-- Right Column - Order Summary -->
    <div class="col-lg-5">
      <div class="order-summary mb-5">
        <h4 class="fw-bold">Your Order</h4>
        <div id="orderItems"></div>
        <!-- Subtotal and Total -->
        <div class="d-flex justify-content-between">
          <span>Subtotal:</span>
          <span class="fw-bold" id="orderSubtotal">$0.00</span>
        </div>
        <div class="d-flex justify-content-between">
          <span>Shipping*:</span>
          <span class="fw-bold" id="shippingCost">Free</span>
        </div>
        <div class="text-danger small mt-1">
          *Shipping free for orders above $500
        </div>
        <hr />
        <div class="d-flex justify-content-between">
          <span>Total:</span>
          <span class="fw-bold" id="orderTotal">$0.00</span>
        </div>

        <!-- Payment Selection -->
        <div class="mt-3">
          <div class="form-check">
            <input class="form-check-input payment-method-radio" type="radio" name="payment_method" id="credit_card" value="Credit Card" checked />
            <label class="form-check-label" for="credit_card">
              Credit Card <img src="images/visa.webp" width="40" />
            </label>
          </div>
          
          <div class="form-check">
            <input class="form-check-input payment-method-radio" type="radio" name="payment_method" id="paypal" value="PayPal" />
            <label class="form-check-label" for="paypal">
              PayPal <img src="images/paypal.webp" width="40" />
            </label>
          </div>
          
          <div class="form-check">
            <input class="form-check-input payment-method-radio" type="radio" name="payment_method" id="cash_on_delivery" value="Cash on Delivery" />
            <label class="form-check-label" for="cash_on_delivery">
              Cash on Delivery
            </label>
          </div>
        </div>

        <!-- Coupon Section -->
        <div class="input-group mt-3">
          <input type="text" class="form-control" placeholder="Coupon Code" id="couponInput" />
          <button type="button" class="btn btn-danger" id="applyCoupon">Apply Coupon</button>
        </div>
        <!-- Coupon Message -->
        <div id="couponMessage" class="mt-2 text-danger"></div>  
        <!-- Place Order Button -->
        <button 
          form="deliveryForm" 
          type="submit" 
          class="btn btn-danger w-100 mt-3"
          id="placeOrderBtn"
        >
          Place Order
        </button>
      </div>
    </div>
  </div>
</div>

<?php
require_once "footer.php";
?>
<script src="hfload.js"></script>
<script src="checkout.js"></script>
<script src="loginheader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>