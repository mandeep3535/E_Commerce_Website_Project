<?php
// Include session handler which sets $is_logged_in, $user_id, etc.
require_once "session_handler.php";
require_once "header-loader.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Computers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
  <link rel="stylesheet" href="categories.css" />
</head>
<body>
  
  <div class="container my-5">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-4">
      <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
      <span class="breadcrumb-item active text-secondary fw-bold" aria-current="page">Computers</span>
    </nav>

    <!-- Products Section -->
    <div class="products-section border rounded p-3">
      <h5 class="fw-bold mb-4">Our Products</h5>

      <!-- Row of Product Cards -->
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3" id="productRow">
  <?php
    require_once 'db_connection.php';

    $sql = "SELECT p.*, 
              COALESCE(AVG(r.rating), 0) AS avg_rating, 
              COUNT(r.review_id) AS review_count 
            FROM products p 
            LEFT JOIN reviews r ON p.product_id = r.product_id 
            WHERE p.category = 'Computers' 
            GROUP BY p.product_id";

    $result = $conn->query($sql);

    $wishlist_items = array();
    if ($is_logged_in && isset($user_id)) {
      $wishlist_sql = "SELECT product_id FROM wishlist WHERE user_id = ?";
      $stmt = $conn->prepare($wishlist_sql);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $wishlist_result = $stmt->get_result();

      while ($wishlist_row = $wishlist_result->fetch_assoc()) {
        $wishlist_items[] = $wishlist_row['product_id'];
      }
      $stmt->close();
    }

    if ($result && $result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
        $in_wishlist = in_array($row['product_id'], $wishlist_items);
        $imagePaths = explode(',', $row['images']);
        $firstImage = !empty($imagePaths[0]) ? trim($imagePaths[0]) : 'images/default-placeholder.jpg';
  ?>
    <div class="col">
      <a href="product_info.php?id=<?php echo $row['product_id']; ?>" class="product-card-link text-decoration-none text-dark">
        <div class="product-card p-3 d-flex flex-column position-relative h-100">
          <!-- Heart Icon (Wishlist) -->
          <?php if ($is_logged_in): ?>
            <button
              class="icon-btn wishlist-icon logged-in-wishlist <?php echo $in_wishlist ? 'in-wishlist' : ''; ?>"
              title="<?php echo $in_wishlist ? 'Already in wishlist' : 'Add to Wishlist'; ?>"
              data-product-id="<?php echo $row['product_id']; ?>"
              data-in-wishlist="<?php echo $in_wishlist ? '1' : '0'; ?>"
              onclick="event.preventDefault(); event.stopPropagation();"
            >
              <i class="bi <?php echo $in_wishlist ? 'bi-heart-fill text-danger' : 'bi-heart'; ?>"></i>
            </button>
          <?php else: ?>
            <button
              class="icon-btn wishlist-icon"
              title="Add to Wishlist"
              data-bs-toggle="modal" 
              data-bs-target="#loginModal"
              onclick="event.preventDefault(); event.stopPropagation();"
            >
              <i class="bi bi-heart"></i>
            </button>
          <?php endif; ?>

          <!-- Product Image -->
          <div class="text-center mb-2">
            <img
              src="<?php echo htmlspecialchars($firstImage); ?>"
              alt="<?php echo htmlspecialchars($row['name']); ?>"
              class="img-fluid product-img"
            />
          </div>

          <!-- Add To Cart Button -->
          <?php if ($is_logged_in): ?>
            <button 
              class="btn btn-danger w-100 btn-sm mb-2 btn-add-to-cart"
              data-product-id="<?php echo $row['product_id']; ?>"
              onclick="event.preventDefault(); event.stopPropagation();"
            >
              Add To Cart
            </button>
          <?php else: ?>
            <button 
              class="btn btn-danger w-100 btn-sm mb-2"
              data-bs-toggle="modal" 
              data-bs-target="#loginModal"
              onclick="event.preventDefault(); event.stopPropagation();"
            >
              Add To Cart
            </button>
          <?php endif; ?>

          <!-- Product Name -->
          <p class="mb-0 fw-semibold product-link">
            <?php echo htmlspecialchars($row['name']); ?>
          </p>

          <!-- Price -->
          <p class="text-danger mb-1">
            $<?php echo number_format($row['price'], 2); ?>
            <span class="text-muted text-decoration-line-through ms-1">
              $<?php echo number_format($row['price'] + 200, 2); ?>
            </span>
          </p>

          <!-- Star Rating -->
          <?php
            $avg = floatval($row['avg_rating']);
            $count = intval($row['review_count']);
            $full = floor($avg);
            $half = ($avg - $full) >= 0.5 ? 1 : 0;
            $empty = 5 - $full - $half;
          ?>
          <div class="star-rating text-warning" style="pointer-events: none;">
            <?php for ($i = 0; $i < $full; $i++) echo '<i class="bi bi-star-fill"></i>'; ?>
            <?php if ($half) echo '<i class="bi bi-star-half"></i>'; ?>
            <?php for ($i = 0; $i < $empty; $i++) echo '<i class="bi bi-star"></i>'; ?>
            <span class="text-muted">(<?php echo $count; ?>)</span>
          </div>
        </div>
      </a>
    </div>
        <?php
            endwhile;
          else:
          
            echo "<p>No computer products found.</p>";
          endif;

          // Close the DB connection
          $conn->close();
        ?>

      </div>
    </div>
  </div>


  <?php require_once "footer.php"; ?>

  <!-- Login Modal (for non-logged-in users) -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Please Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          You must be logged in to perform this action.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
          <a href="login.php" class="btn btn-danger">Login</a>
        </div>
      </div>
    </div>
  </div>

  <script src="hfload.js"></script>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="categories.js"></script>
  <script src="loginheader.js"></script>
  <?php require_once "categories_common_script.php"; ?>
  
</body>
</html>
