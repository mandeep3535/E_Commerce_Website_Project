<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Products</title>
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
  <div id="loginheader"></div>
  <div class="container my-5">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-4">
      <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
      <span class="breadcrumb-item active text-secondary fw-bold" aria-current="page">Gaming</span>
    </nav>

    <!-- Products Section -->
    <div class="products-section border rounded p-3">
      <h5 class="fw-bold mb-4">Our Products</h5>

      <!-- Row of Product Cards -->
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3" id="productRow">
        <?php
          require_once 'db_connection.php';
          // Fetch products where category='Gaming' AND best_seller=0
          $sql = "SELECT * FROM products 
                  WHERE category = 'Gaming'
                    AND best_seller = 0";
          $result = $conn->query($sql);

          if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
              <div class="col">
                <div class="product-card p-3 d-flex flex-column position-relative h-100" onclick="event.stopPropagation();">
                  <!-- Heart Icon -->
                  <button class="icon-btn wishlist-icon" title="Add to Wishlist" onclick="event.stopPropagation();">
                    <i class="bi bi-heart"></i>
                  </button>

                  <!-- Product Image -->
                  <div class="text-center mb-2">
                    <img
                      src="<?php echo htmlspecialchars($row['images']); ?>"
                      alt="<?php echo htmlspecialchars($row['name']); ?>"
                      class="img-fluid product-img"
                    />
                  </div>

                  <!-- Add To Cart Button -->
                  <button class="btn btn-danger w-100 btn-sm mb-2 btn-add-to-cart" onclick="event.stopPropagation();">
                    Add To Cart
                  </button>

                  <!-- Product Info -->
                  <p class="mb-0 fw-semibold">
                    <a href="product_info.php?id=<?php echo $row['product_id']; ?>" class="product-link">
                      <?php echo htmlspecialchars($row['name']); ?>
                    </a>
                  </p>
                  <p class="text-danger mb-1">
                    $<?php echo number_format($row['price'], 2); ?>
                    <span class="text-muted text-decoration-line-through ms-1">
                      $<?php echo number_format($row['price'] + 200, 2); ?>
                    </span>
                  </p>

                  <!-- Star Rating (static) -->
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
        <?php
            endwhile;
          else:
            echo "<p>No gaming products found.</p>";
          endif;
          $conn->close();
        ?>
      </div>
    </div>
  </div>

  <div id="footer"></div>

  <!-- Cart Modal -->
  <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="cartModalLabel">Cart</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="cartModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <a href="cart.html" class="btn btn-danger">View Cart</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Wishlist Modal -->
  <div class="modal fade" id="wishlistModal" tabindex="-1" aria-labelledby="wishlistModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="wishlistModalLabel">Wishlist</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="wishlistModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <a href="wishlist.html" class="btn btn-danger">View Wishlist</a>
        </div>
      </div>
    </div>
  </div>

  <script src="hfload.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="categories.js"></script>
  <script src="loginheader.js"></script>
</body>
</html>
