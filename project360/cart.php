<?php
// 1) Ensure session is available
require_once "session_handler.php";

// 2) If user not logged in, redirect or show error
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION["flash_message"] = "You must be logged in to access this page.";
    header("Location: home.php");
    exit;
}

/********************************************************
 * (A) Handle "Remove from Cart" if ?action=remove&product_id=...
 ********************************************************/
if (
    isset($_GET['action']) && $_GET['action'] === 'remove'
    && isset($_GET['product_id']) && !empty($_GET['product_id'])
) {
    require_once "db_connection.php";
    $user_id    = (int) $_SESSION["user_id"];
    $product_id = (int) $_GET["product_id"];

    $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Item removed from cart.";
        } else {
            echo "Item not found or already removed.";
        }
    } else {
        echo "Error removing item: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
    exit;
}

/********************************************************
 * (B) Handle "Update Quantity" if ?action=update&product_id=...&quantity=...
 ********************************************************/
if (
    isset($_GET['action']) && $_GET['action'] === 'update'
    && isset($_GET['product_id']) && !empty($_GET['product_id'])
    && isset($_GET['quantity'])
) {
    require_once "db_connection.php";
    $user_id    = (int) $_SESSION["user_id"];
    $product_id = (int) $_GET["product_id"];
    $newQty     = (int) $_GET["quantity"];

    if ($newQty < 1) {
        $newQty = 1;
    }

    $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $newQty, $user_id, $product_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Quantity updated to $newQty for product $product_id";
        } else {
            echo "Item not found or no change in quantity.";
        }
    } else {
        echo "Error updating quantity: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
    exit;
}

/********************************************************
 * (C) Handle "Add to Cart" if ?product_id=...
 *  (No "action" param => just product_id)
 ********************************************************/
if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
  require_once "db_connection.php";
  $user_id    = (int) $_SESSION["user_id"];
  $product_id = (int) $_GET["product_id"];
  
  $quantityToAdd = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
  if ($quantityToAdd < 1) {
      $quantityToAdd = 1;
  }

  $check_sql = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
  $stmt = $conn->prepare($check_sql);
  $stmt->bind_param("ii", $user_id, $product_id);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows > 0) {
      $row = $res->fetch_assoc();
      $newQuantity = $row['quantity'] + $quantityToAdd;
  
      if ($newQuantity > 10) {
          echo "Item already reached maximum quantity.";
          exit;
      }

      $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
      $update_stmt = $conn->prepare($update_sql);
      $update_stmt->bind_param("iii", $newQuantity, $user_id, $product_id);
  
      if ($update_stmt->execute()) {
          echo "Added to cart";
      } else {
          echo "Error updating quantity: " . $conn->error;
      }
      $update_stmt->close();
  } else {
      $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
      $insert_stmt = $conn->prepare($insert_sql);
      $insert_stmt->bind_param("iii", $user_id, $product_id, $quantityToAdd);
  
      if ($insert_stmt->execute()) {
          echo "Item added successfully";
      } else {
          echo "Error inserting item: " . $conn->error;
      }
      $insert_stmt->close();
  }

  $stmt->close();
  $conn->close();
  exit;
}

require_once "header-loader.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shopping Cart</title>
  <link rel="stylesheet" href="cart.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
</head>
<body>
  <div class="container mt-4">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-4">
      <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
      <span class="breadcrumb-item active text-secondary fw-bold">Cart</span>
    </nav>

    <?php
    require_once 'db_connection.php';
    $cartItems = [];
    $subtotalTotal = 0;
    $user_id = (int) $_SESSION["user_id"];

    $sql = "SELECT c.product_id, c.quantity, p.name, p.price, p.images
            FROM cart c 
            INNER JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $subtotalTotal += $row['price'] * $row['quantity'];
    }
    $stmt->close();
    $conn->close();
    ?>

    <div class="row gy-4 mb-5">
      <!-- Left Section: Cart Table and Actions -->
      <div class="col-lg-8">
        <!-- Cart Table -->
        <div class="table-responsive shadow-sm rounded bg-white p-3">
          <table class="table table-borderless align-middle cart-table mb-0">
            <thead class="border-bottom">
              <tr class="text-uppercase text-muted small">
                <th class="fw-semibold">Remove</th>
                <th class="fw-semibold">Product</th>
                <th class="fw-semibold">Price</th>
                <th class="fw-semibold">Quantity</th>
                <th class="fw-semibold">Subtotal</th>
              </tr>
            </thead>
            <tbody id="cartBody">
              <?php if(count($cartItems) > 0): ?>
                <?php foreach($cartItems as $item): ?>
                  <tr data-product-id="<?php echo $item['product_id']; ?>">
                    <td>
                      <button class="btn btn-sm btn-outline-danger remove-item" data-product-id="<?php echo $item['product_id']; ?>">&times;</button>
                    </td>
                    <td>
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
                      <img 
                        src="<?php echo htmlspecialchars($firstImage); ?>" 
                        alt="<?php echo htmlspecialchars($item['name']); ?>" 
                        style="width: 60px; height: auto; margin-right:8px;"
                      />
                      <?php echo htmlspecialchars($item['name']); ?>
                    </td>
                    <td 
                      class="product-price" 
                      data-price="<?php echo $item['price']; ?>"
                    >
                      $<?php echo number_format($item['price'], 2); ?>
                    </td>
                    <td>
                      <select 
                        class="quantity-select" 
                        data-product-id="<?php echo $item['product_id']; ?>"
                      >
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                          <option value="<?php echo $i; ?>" 
                            <?php echo ($i == $item['quantity']) ? 'selected' : ''; ?>
                          >
                            <?php echo $i; ?>
                          </option>
                        <?php endfor; ?>
                      </select>
                    </td>
                    <td class="subtotal">
                      $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center">Your cart is empty.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Cart Buttons -->
        <div class="mt-3 d-flex flex-wrap justify-content-center justify-content-md-between">
          <a href="home.php"><button class="btn btn-danger mb-2">Return To Shop</button></a>
        </div>
      </div>

      <!-- Right Section: Cart Total -->
      <div class="col-lg-4">
        <div class="bg-white p-4 rounded shadow-sm">
          <h5 class="fw-bold mb-3">Cart Total</h5>
          <div class="d-flex justify-content-between mb-2">
            <span>Subtotal:</span>
            <span class="fw-semibold" id="cartSubtotal">$<?php echo number_format($subtotalTotal, 2); ?></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Shipping:</span>
            <span class="fw-semibold">Free</span>
          </div>
          <hr />
          <div class="d-flex justify-content-between mb-3">
            <span>Total:</span>
            <span class="fw-semibold" id="cartTotal">$<?php echo number_format($subtotalTotal, 2); ?></span>
          </div>
          <a href="checkout.php" class="btn btn-danger w-100">Proceed to Checkout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php require_once "footer.php"; ?>

  <!-- Feedback Modal -->
  <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="feedbackModalLabel">Notification</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Modal message will be inserted here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- JS to load header/footer -->
  <script src="hfload.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="cart.js"></script>
</body>
</html>
