<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MV Electronics - Homepage</title>

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
  <link rel="stylesheet" href="home.css" />
</head>
<body>
    <div id = "header"></div>

<!--  MAIN SECTION WITH SIDEBAR & CAROUSEL -->
<div class="container-fluid my-4">
 <!-- Breadcrumb -->
 <nav class="breadcrumb mb-4">
  <span class="breadcrumb-item text-secondary fw-bold" href="home.php">Home</span></nav>
  <div class="row">
    <!-- Sidebar -->
    <div class="col-lg-2 d-none d-lg-block ms-5">
      <ul class="list-group border-0">
        <a href = "phone.html"><li class="list-group-item border-0 py-2 ">Phones</li></a>
        <a href = "computer.html"><li class="list-group-item border-0 py-2 ">Computers</li></a>
        <a href = "smartwatch.html"><li class="list-group-item border-0 py-2 ">SmartWatch</li></a>
          <a href = "headphones.html"><li class="list-group-item border-0 py-2 ">Headphones</li></a>    
          <a href = "gaming.html"> <li class="list-group-item border-0 py-2">Gaming</li></a>
       
      </ul>
    </div>

    <!-- Carousel -->
<div class="col-lg-8 col-md-12 col-sm-6 container-fluid p-0">
  <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner rounded shadow-sm">
      <!-- Slide 1 -->
      <div class="carousel-item active">
        <img src="images/iphone.jpg" class="d-block w-100 carousel-img" alt="Promo Slide 1" />
      </div>
      <!-- Slide 2 -->
      <div class="carousel-item">
        <img src="images/laptop.avif" class="d-block w-100 carousel-img" alt="Promo Slide 2" />
      </div>
      <!-- Slide 3 -->
      <div class="carousel-item">
        <img src="images/headphone.avif" class="d-block w-100 carousel-img" alt="Promo Slide 3" />
      </div>
    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
  </div>
</div>

<!--   BROWSE BY CATEGORY SECTION-->
<section class="container my-5">
  <div class="d-flex align-items-center mb-3">
    <div class="category-label bg-danger text-white me-2"></div>
    <h5 class="text-danger mb-0">Categories</h5>
  </div>
  <h2 class="fw-bold">Browse By Category</h2>
  <div class="row mt-4 g-3 justify-content-center">
    <!-- Category Card 1 -->
  <div class="col-6 col-md-4 col-lg-2" onclick=location.href="phone.html">
      <div class="cat-card text-center p-3">
        <i class="bi bi-phone fs-1"></i>
        <p class="mt-2">Phones</p>
      </div>
    </div>
    <!-- Category Card 2 -->
    <div class="col-6 col-md-4 col-lg-2" onclick=location.href="computer.html">                   
      <div class="cat-card text-center p-3">
        <i class="bi bi-laptop fs-1"></i>
        <p class="mt-2">Computers</p>
      </div>
    </div>
    <!-- Category Card 3 -->
    <div class="col-6 col-md-4 col-lg-2" onclick=location.href="smartwatch.html">
      <div class="cat-card text-center p-3">
        <i class="bi bi-watch fs-1"></i>
        <p class="mt-2">SmartWatch</p>
      </div>
    </div>

    <!-- Category Card 4 -->
    <div class="col-6 col-md-4 col-lg-2" onclick=location.href="headphones.html">
      <div class="cat-card text-center p-3">
        <i class="bi bi-headphones fs-1"></i>
        <p class="mt-2">HeadPhones</p>
      </div>
    </div>
    <!-- Category Card 5 -->
    <div class="col-6 col-md-4 col-lg-2" onclick=location.href="gaming.html">
      <div class="cat-card text-center p-3">
        <i class="bi bi-controller fs-1"></i>
        <p class="mt-2">Gaming</p>
      </div>
    </div>
  </div>

</section>
<!--  BEST SELLING PRODUCTS-->
<section class="container my-5">
  <div class="d-flex align-items-center mb-3">
    <div class="category-label bg-danger text-white me-2"></div>
    <h6 class="text-danger mb-0">This Month</h6>
  </div>
  <div class="d-flex justify-content-between align-items-center">
    <h2 class="fw-bold">Best Selling Products</h2>
  </div>

  <!-- Products Row -->
  <div class="row mt-4 g-3">
    <?php
      // 1. Include your database connection
      require_once 'db_connection.php';

      // 2. Fetch best-selling products (example: only those with best_seller = 1)
      $sql = "SELECT * FROM products WHERE best_seller = 1 LIMIT 4";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
          <div class="col-6 col-md-4 col-lg-3">
            <div class="product-card p-3 bg-white position-relative">
              <!-- Icons -->
              <div class="product-icons position-absolute top-0 end-0 m-2">
                <button class="btn btn-link p-0 wishlist-icon" title="Add to Wishlist">
                  <i class="bi bi-heart fs-5"></i>
                </button>
              </div>
              
              <!-- Product Image -->
              <img
                src="<?php echo htmlspecialchars($row['images']); ?>"
                alt="<?php echo htmlspecialchars($row['name']); ?>"
                class="img-fluid mb-3 product-img"
              />

              <!-- Product Name -->
              <h6 class="mb-1"><?php echo htmlspecialchars($row['name']); ?></h6>

              <!-- Product Price (you can add discount logic if you like) -->
              <p class="text-danger mb-0">
                $<?php echo number_format($row['price'], 2); ?>
                <!-- Optional  discount -->
                <span class="text-muted text-decoration-line-through">$<?php echo number_format($row['price'] + 100, 2); ?></span>
              </p>

              <!-- Static star rating or dynamic if you store ratings in DB -->
              <div class="text-warning small mb-1">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <span class="text-muted">(65)</span>
              </div>

              <!-- Add To Cart Button -->
              <button class="btn btn-danger w-100 btn-sm mt-2 btn-add-to-cart">
                Add To Cart
              </button>
            </div>
          </div>
    <?php
        endwhile;
      else:
        echo "<p>No best-selling products found.</p>";
      endif;

      // 3. Close the DB connection
      $conn->close();
    ?>
  </div>
</section>


<!--   BIG BANNER (Music)-->
<section class="container-fluid my-5">
  <div class="row g-0 position-relative banner-dark align-items-center text-white">
    <!-- Text & Timer -->
    <div class="col-lg-6 p-5">
      <h6 class="text-success fw-bold">Categories</h6>
      <h2 class="fw-bold">Enhance Your Music Experience</h2>
      <div class="d-flex align-items-center my-4 fs-5" id="countdown">
        <div class="me-3 text-center">
            <div id="days">00</div>
            <small>Days</small>
        </div>
        <div class="me-3 text-center">
            <div id="hours">00</div>
            <small>Hours</small>
        </div>
        <div class="me-3 text-center">
            <div id="minutes">00</div>
            <small>Minutes</small>
        </div>
        <div class="me-3 text-center">
            <div id="seconds">00</div>
            <small>Seconds</small>
        </div>
    </div>

    <!-- JavaScript File for timer -->
    <script src="timer.js"></script>

      <a href="cart.html" class="btn btn-success px-4 py-2">Buy Now!</a>

    </div>
    <!-- Image -->
    <div class="col-lg-6">
      <img
        src="images/JBL.png"
        alt="JBL Speaker"
        class="img-fluid"
      />
    </div>
  </div>
</section>

<!--  FEATURES (Icons) -->
<section class="container text-center my-5">
  <div class="row g-4">
    <!-- Feature 1 -->
    <div class="col-md-4">
      <i class="bi bi-truck fs-1 text-dark mb-2"></i>
      <h6 class="fw-bold">FREE AND FAST DELIVERY</h6>
      <p class="text-muted">
        Free delivery for all orders over $140
      </p>
    </div>
    <!-- Feature 2 -->
    <div class="col-md-4">
      <i class="bi bi-headset fs-1 text-dark mb-2"></i>
      <h6 class="fw-bold">24/7 CUSTOMER SERVICE</h6>
      <p class="text-muted">
        Friendly 24/7 customer support
      </p>
    </div>
    <!-- Feature 3 -->
    <div class="col-md-4">
      <i class="bi bi-shield-check fs-1 text-dark mb-2"></i>
      <h6 class="fw-bold">MONEY BACK GUARANTEE</h6>
      <p class="text-muted">
        We return money within 30 days
      </p>
    </div>
  </div>
</section>


<div id = "footer"></div>

<!-- Cart Modal -->
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
        <a href="cart.html" class="btn btn-danger">Go to Cart</a>
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
      <div class="modal-body" id="wishlistModalBody">
  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="wishlist.html" class="btn btn-primary">View Wishlist</a>
      </div>
    </div>
  </div>
</div>

<script src = "hfload.js"></script>
<script src = "bestSeller.js"></script>
<!-- Bootstrap JS -->
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
></script>

</body>
</html>
