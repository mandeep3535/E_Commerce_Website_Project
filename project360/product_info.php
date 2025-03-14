<?php
// Include database connection
require_once 'db_connection.php';
session_start();

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
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
                // exactly 3 thumbnails
                $thumbCount = 3;
                for ($i = 0; $i < $thumbCount; $i++) {
                    // If an image exists in the array, use it; otherwise, use default image.
                    $img = isset($images[$i]) ? $images[$i] : $defaultImage;
                    $activeClass = ($i === 0) ? ' active' : '';
                    ?>
                    <div class="thumb-card text-center border rounded<?php echo $activeClass; ?>"
                         onclick="changeMainImage('<?php echo htmlspecialchars($img); ?>', this)">
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
                <img id="mainProductImage" 
                     src="<?php echo htmlspecialchars(isset($images[0]) ? $images[0] : $defaultImage); ?>" 
                     class="img-fluid main-product-img" />
            </div>
        </div>

        <!-- Right Product Info -->
        <div class="col-md-5">
            <h3 class="fw-bold" id="productName"><?php echo htmlspecialchars($row['name']); ?></h3>
            <div class="d-flex align-items-center mb-2">
            <div class="text-warning me-2" id="averageRating">
        <!-- Average stars will be injected here dynamically -->
             </div>
                <small class="text-muted" id="reviewCount">(0 Reviews)</small>
                <span class="ms-3 text-success small">
                  <?php echo ($row['stock'] > 0) ? "In Stock" : "Out of Stock"; ?>
                </span>
            </div>

            <h4 class="text-danger text-md-start text-center" id="productPrice">
              $<?php echo number_format($row['price'], 2); ?>
            </h4>

            <p class="text-muted"><?php echo htmlspecialchars($row['description']); ?></p>
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
                        <small class="text-muted">Enter your postal code for Delivery Availability</small>
                    </div>
                </div>
            </div>
            <div class="border p-3">
                <div class="d-flex align-items-start">
                    <i class="bi bi-arrow-repeat fs-5 me-3 text-dark"></i>
                    <div>
                        <p class="fw-semibold mb-0">Return Delivery</p>
                        <small class="text-muted">Free 30 Days Delivery Returns.</small>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div> 

<!-- REVIEW FUNCTIONALITY VIA AJAX  -->
<div class="container mt-5" id="reviewsContainer">
  <h4>Customer Reviews</h4>
  <!-- Outer wrapper that sets a fixed height and scrolls -->
  <div style="max-height:500px; overflow-y:auto;">
    <!-- Row for the review cards -->
    <div class="row" id="reviewsRow">
      <!-- Each review will be inserted here as a col-md-4 card -->
    </div>
  </div>
</div>

<div class="container mt-5 mb-5" id="reviewFormContainer">
  <h4>Add a Review</h4>
  <form id="reviewForm">
      <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
      <?php if(!isset($_SESSION['user_id'])) { ?>
          <div class="mb-3">
              <label for="username" class="form-label">Your Name</label>
              <input type="text" class="form-control" name="username" required>
          </div>
      <?php } ?>
      <div class="mb-3">
          <label for="rating" class="form-label">Rating</label>
          <select name="rating" class="form-select" required>
              <option value="">Select Rating</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
          </select>
      </div>
      <div class="mb-3">
          <label for="comment" class="form-label">Review</label>
          <textarea name="comment" class="form-control" rows="3" required></textarea>
      </div>
      <button type="submit" class="btn btn-danger">Submit Review</button>
  </form>
  <div id="reviewMessage"></div>
</div>

<!-- Added to cart toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="bi bi-cart-check me-2"></i>
            <strong class="me-auto">Success!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">Product added to cart successfully!</div>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>

<script src="hfload.js"></script>
<script src="product.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Define current user ID inside a script tag
var currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; ?>;

document.addEventListener("DOMContentLoaded", function() {
    function fetchReviews() {
        fetch("fetch_reviews.php?product_id=<?php echo $productId; ?>")
            .then(response => response.json())
            .then(data => {
                // Update review count dynamically
                document.getElementById("reviewCount").textContent = "(" + data.length + " Reviews)";
                
                // Calculate average rating
                let sum = 0;
                data.forEach(review => {
                    sum += parseFloat(review.rating);
                });
                let avg = data.length ? (sum / data.length) : 0;
                let fullStars = Math.floor(avg);
                let halfStar = (avg - fullStars) >= 0.5 ? 1 : 0;
                let emptyStars = 5 - fullStars - halfStar;
                let starHtml = "";
                for(let i = 0; i < fullStars; i++){
                    starHtml += '<i class="bi bi-star-fill"></i>';
                }
                if(halfStar){
                    starHtml += '<i class="bi bi-star-half"></i>';
                }
                for(let i = 0; i < emptyStars; i++){
                    starHtml += '<i class="bi bi-star"></i>';
                }
                // Set the average rating stars
                document.getElementById("averageRating").innerHTML = starHtml;
                
                // Build the reviews HTML
                let html = "<h4>Customer Reviews</h4>";
                if(data.length > 0){
                    data.forEach(review => {
                        // Determine reviewer name; fallback to "Anonymous"
                        let reviewer = "Anonymous";
                        if(review.user_name && review.user_name.trim() !== ""){
                            reviewer = review.user_name;
                        } else if(review.first_name && review.first_name.trim() !== ""){
                            reviewer = review.first_name;
                        }
                        
                        html += `
                        <div class="card mb-2" 
                             data-review-id="${review.review_id}" 
                             data-review-comment="${review.comment}" 
                             data-review-rating="${review.rating}" 
                             data-review-user="${review.user_id}">
                            <div class="card-body">
                                <h6>${reviewer} <small class="text-muted">(${review.created_at})</small></h6>
                                <p>Rating: ${review.rating}/5</p>
                                <p>${review.comment.replace(/\n/g, '<br>')}</p>`;
                        
                        if(parseInt(review.user_id) === currentUserId && currentUserId > 0){
                            html += `<button class="btn btn-sm btn-warning edit-review-btn">Edit</button>`;
                        }
                        
                        html += `</div>
                        </div>`;
                    });
                } else {
                    html += "<p>No reviews yet. Be the first to review this product!</p>";
                }
                document.getElementById("reviewsContainer").innerHTML = html;
                
                // Attach click events to Edit buttons
                document.querySelectorAll('.edit-review-btn').forEach(function(btn) {
                    btn.addEventListener('click', function(){
                        const reviewCard = this.closest('.card');
                        const comment = reviewCard.getAttribute('data-review-comment');
                        const rating = reviewCard.getAttribute('data-review-rating');
                        
                        document.querySelector('#reviewForm textarea[name="comment"]').value = comment;
                        document.querySelector('#reviewForm select[name="rating"]').value = rating;
                        
                        document.querySelector('#reviewForm button[type="submit"]').textContent = "Update Review";
                    });
                });
            })
            .catch(error => console.error("Error fetching reviews:", error));
    }
    
    // Initial fetch of reviews
    fetchReviews();
    
    // Poll for new reviews every second
    setInterval(fetchReviews, 1000);
    
    <?php if(isset($_SESSION['user_id'])) { ?>
    document.getElementById("reviewForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("submit_review.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const reviewMessage = document.getElementById("reviewMessage");
            if(data.status === "success"){
                reviewMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                this.reset();
                // Reset submit button text
                document.querySelector('#reviewForm button[type="submit"]').textContent = "Submit Review";
                fetchReviews(); // Refresh reviews after successful submission
            } else {
                reviewMessage.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => console.error("Error submitting review:", error));
    });
    <?php } ?>
});
</script>

</body>
</html>
