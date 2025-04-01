<?php
// wishlist.php

// Ensure session is started
require_once "session_handler.php";

// 1) Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Optionally set a flash message
    $_SESSION["flash_message"] = "You must be logged in to access this page.";
    // Redirect to home (or login) page
    header("Location: home.php");
    exit;
}

// 2) Check for ?action=remove&product_id=... => REMOVE from DB
if (
    isset($_GET['action']) && $_GET['action'] === 'remove'
    && isset($_GET['product_id']) && !empty($_GET['product_id'])
) {
    require_once "db_connection.php";
    $user_id    = (int) $_SESSION["user_id"];
    $product_id = (int) $_GET["product_id"];

    $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Item removed from wishlist.";
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

// 3) Check for ?product_id=... => ADD to DB
if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
    require_once "db_connection.php";
    $user_id    = (int) $_SESSION["user_id"];
    $product_id = (int) $_GET["product_id"];

    // Check if item is already in wishlist
    $check_sql = "SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo "Item is already in wishlist";
    } else {
        // Insert the item
        $insert_sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $product_id);
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

// 4) If no remove/add action => show the wishlist page
require_once "header-loader.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Wishlist & Just For You</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <!-- Bootstrap CSS -->
  <link 
    rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
  />
  <!-- Bootstrap Icons -->
  <link 
    rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
  />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="wishlist.css"/>
</head>
<body>

<?php
// Fetch wishlist items for the current user
$wishlistItems = [];
if (isset($_SESSION["user_id"])) {
    require_once 'db_connection.php';
    $user_id = (int) $_SESSION["user_id"];
    
    $sql = "
        SELECT w.product_id, p.name, p.price, p.images
        FROM wishlist w
        INNER JOIN products p ON w.product_id = p.product_id
        WHERE w.user_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $wishlistItems[] = $row;
    }
    $stmt->close();
    $conn->close();
}
?>

<div class="container my-4">
  <!-- Breadcrumb -->
  <nav class="breadcrumb mb-4">
    <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
    <span class="breadcrumb-item active text-secondary fw-bold" aria-current="page">Wishlist</span>
  </nav>

  <!-- Wishlist Section -->
  <div class="wishlist-section p-3 border rounded mb-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="fw-bold mb-0" id="wishlistHeader">
        Wishlist (<?php echo count($wishlistItems); ?>)
      </h5>
      <button class="btn btn-outline-dark btn-sm" id="moveAllButton">Move All To Bag</button>
    </div>

    <!-- Wishlist items -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 mt-3" id="wishlistContainer">
      <?php if (count($wishlistItems) > 0): ?>
        <?php foreach ($wishlistItems as $item): ?>
          <div class="col">
            <div class="product-card h-100 p-3 position-relative d-flex flex-column">
              
              <!-- Trash Icon => triggers remove from DB -->
              <button 
                class="icon-btn remove-wishlist-item"
                data-product-id="<?php echo $item['product_id']; ?>"
                data-product-name="<?php echo htmlspecialchars($item['name']); ?>"
                data-product-price="<?php echo $item['price']; ?>"
                data-product-image="<?php echo htmlspecialchars($item['images']); ?>"
              >
                <i class="bi bi-trash"></i>
              </button>

              <!-- Product Image -->
              <div class="text-center mb-2">
  <?php 
    //decode images
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
    class="img-fluid product-img"
  />
</div>
              <!-- Add to Cart  -->
              <button 
  class="btn btn-danger w-100 btn-sm mb-1 add-to-cart-btn"
  data-product-id="<?php echo $item['product_id']; ?>"
  data-product-name="<?php echo htmlspecialchars($item['name']); ?>"
  data-product-price="<?php echo $item['price']; ?>"
  data-product-image="<?php echo htmlspecialchars($item['images']); ?>"
>
  Add To Cart
</button>

              <p class="mb-0 fw-semibold"><?php echo htmlspecialchars($item['name']); ?></p>
              <p class="text-danger mb-1">
                $<?php echo number_format($item['price'], 2); ?>
              </p>
              <!-- Star Rating (static example) -->
              <div class="star-rating">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <span class="text-muted">(65)</span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted">You have no items in your wishlist.</p>
      <?php endif; ?>
    </div>
  </div>
  <div class="just-for-you-section p-3 border rounded mb-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="fw-bold mb-0">Coming Soon !</h5>
    </div>

    <!-- Products Grid (static demo) -->
    <div class="row row-cols-2 row-cols-md-4 g-3 mt-3" id="justForYouContainer"> 
      <!-- Product 1 -->
      <div class="col">
        <div class="product-card h-100 p-3 position-relative d-flex flex-column"> 
        <span class="new-badge">NEW</span>
          <div class="text-center mb-2">
            <img 
              src="images/asusgaming.webp" 
              alt="ASUS FHD Gaming Laptop" 
              class="img-fluid product-img"
            />
          </div>
              <p class="mb-0 fw-semibold">ASUS FHD Gaming Laptop</p>
          <p class="text-danger mb-1">$960 
            <span class="text-muted text-decoration-line-through">$1160</span>
          </p>
          <!-- Star Rating -->
      
        </div>
      </div>
      <!-- Product 2 -->
      <div class="col">
        <div class="product-card h-100 p-3 position-relative d-flex flex-column"> 
        <span class="new-badge">NEW</span>
        <div class="text-center mb-2">
            <img 
              src="images/monitor_gaming.jpeg" 
              alt="IPS LCD Gaming Monitor" 
              class="img-fluid product-img"
            />
          </div>
             <p class="mb-0 fw-semibold">IPS LCD Gaming Monitor</p>
          <p class="text-danger mb-1">$1160</p>
    
        </div>
      </div>
      <!-- Product 3 -->
      <div class="col">
        <div class="product-card h-100 p-3 position-relative d-flex flex-column"> 
          <span class="new-badge">NEW</span>
          <div class="text-center mb-2">
            <img 
              src="images/havit.jpg" 
              alt="HAVIT HV-G92 Gamepad" 
              class="img-fluid product-img"
            />
          </div>
           <p class="mb-0 fw-semibold">HAVIT HV-G92 Gamepad</p>
          <p class="text-danger mb-1">$560</p>
    
        </div>
      </div>
      <!-- Product 4 -->
      <div class="col">
        <div class="product-card h-100 p-3 position-relative d-flex flex-column"> 
        <span class="new-badge">NEW</span>
        <div class="text-center mb-2">
            <img 
              src="images/keyboard.jpg" 
              alt="AK-900 Wired Keyboard" 
              class="img-fluid product-img"
            />
          </div>
           <p class="mb-0 fw-semibold">AK-900 Wired Keyboard</p>
          <p class="text-danger mb-1">$200</p>
    
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<?php require_once "footer.php"; ?>

<!-- Modal for "Add to Cart" -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="cartModalLabel">Cart Update</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="cartModalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
        <a href="cart.php" class="btn btn-danger">Go to Cart</a>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="loginheader.js"></script>

<script src = "wishlist.js"></script>
</script>
</body>
</html>
