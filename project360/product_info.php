<?php
// Include database connection
require_once 'db_connection.php';

// Check if product id is passed via URL
if (!isset($_GET['id'])) {
    die("No product ID specified.");
}
$productId = (int) $_GET['id'];

// Fetch product from database
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}
$row = $result->fetch_assoc();

// Decode images 
$images = json_decode($row['images'], true);

// If not valid JSON or empty, treat as a single image 
if (!is_array($images)) {
    $images = [$row['images']];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($row['name']); ?></title>
  
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
  <link rel="stylesheet" href="product.css" />
</head>
<body>
<div id="loginheader"></div>
<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item">
              <a href="<?php echo strtolower($row['category']); ?>.php">
                <?php echo htmlspecialchars($row['category']); ?>
              </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              <?php echo htmlspecialchars($row['name']); ?>
            </li>
        </ol>
    </nav>

    <!-- Main Product Section -->
    <div class="row mb-5">  
        
        <!-- Left Thumbnails -->
        <div class="col-md-2 mb-3">
            <div class="d-flex flex-md-column flex-row gap-2 justify-content-md-start justify-content-center">
                <?php
                // default image for missing thumbnails
                $defaultImage = "images/default-placeholder.jpg";
                //  exactly 3 thumbnails
                $thumbCount = 3;
                for ($i = 0; $i < $thumbCount; $i++) {
                    // If an image exists in the array, use it; otherwise, use default image.
                    $img = isset($images[$i]) ? $images[$i] : $defaultImage;
                    $activeClass = ($i === 0) ? ' active' : '';
                    ?>
                    <div 
                      class="thumb-card text-center border rounded<?php echo $activeClass; ?>"
                      onclick="changeMainImage('<?php echo htmlspecialchars($img); ?>', this)"
                    >
                        <img src="<?php echo htmlspecialchars($img); ?>" class="img-fluid thumb-img" />
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        
        <!-- Center Main Image -->
        <div class="col-md-5">
            <div class="border rounded p-3 text-center">
                <img 
                  id="mainProductImage" 
                  src="<?php echo htmlspecialchars(isset($images[0]) ? $images[0] : $defaultImage); ?>" 
                  class="img-fluid main-product-img"
                />
            </div>
        </div>

        <!-- Right Product Info -->
        <div class="col-md-5">
            <h3 class="fw-bold" id="productName">
              <?php echo htmlspecialchars($row['name']); ?>
            </h3>
            <div class="d-flex align-items-center mb-2">
                <div class="text-warning me-2">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-half"></i>
                </div>
                <small class="text-muted">(150 Reviews)</small>
                <span class="ms-3 text-success small">
                  <?php echo ($row['stock'] > 0) ? "In Stock" : "Out of Stock"; ?>
                </span>
            </div>

            <h4 class="text-danger text-md-start text-center" id="productPrice">
              $<?php echo number_format($row['price'], 2); ?>
            </h4>

            <p class="text-muted">
              <?php echo htmlspecialchars($row['description']); ?>
            </p>
            <hr/>

            <!-- Quantity & Actions -->
            <div class="d-flex align-items-center mb-3 pt-3">
                <div class="input-group me-3" style="width:120px;">
                    <button id="decreaseQty" class="btn btn-outline-secondary" type="button">-</button>
                    <input id="quantityInput" type="text" class="form-control text-center" value="1"/>
                    <button id="increaseQty" class="btn btn-outline-secondary" type="button">+</button>
                </div>
                <button class="btn btn-danger me-2" id="buyNowBtn">Buy Now</button>
                <button class="btn btn-outline-secondary" id="wishlistBtn">
                  <i class="bi bi-heart"></i>
                </button>
            </div>

            <!-- Delivery & Return Info -->
            <div class="border p-3 mb-2">
                <div class="d-flex align-items-start">
                    <i class="bi bi-truck fs-5 me-3 text-dark"></i>
                    <div>
                        <p class="fw-semibold mb-0">Free Delivery</p>
                        <small class="text-muted">
                          Enter your postal code for Delivery Availability
                        </small>
                    </div>
                </div>
            </div>
            <div class="border p-3">
                <div class="d-flex align-items-start">
                    <i class="bi bi-arrow-repeat fs-5 me-3 text-dark"></i>
                    <div>
                        <p class="fw-semibold mb-0">Return Delivery</p>
                        <small class="text-muted">
                          Free 30 Days Delivery Returns.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div> 

<!-- Added to cart toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="bi bi-cart-check me-2"></i>
            <strong class="me-auto">Success!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Product added to cart successfully!
        </div>
    </div>
</div>

<!-- Footer -->
<div id="footer"></div>

<!-- Add to Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="cartModalLabel">Cart Update</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="cartModalBody">
          <!-- Insert product details via JS -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
          <a href="cart.html" class="btn btn-danger">Go to Cart</a>
        </div>
      </div>
    </div>
</div>

<!-- Wishlist Modal -->
<div class="modal fade" id="wishlistModal" tabindex="-1" aria-labelledby="wishlistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="wishlistModalLabel">Wishlist Update</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- Modal Body -->
        <div class="modal-body" id="wishlistModalBody"></div>
        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
</div>

<script src="hfload.js"></script>
<script src="product.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
