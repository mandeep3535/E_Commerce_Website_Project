<?php
// This file contains all the JavaScript related to cart and wishlist functionality
?>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="cartModalLabel">Cart Update</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="cartModalBody">
        <!-- Message will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
        <a href="cart.php" class="btn btn-danger">Go to Cart</a>
      </div>
    </div>
  </div>
</div>

<!-- Wishlist Modal (for logged-in users only) -->
<?php if ($is_logged_in): ?>
<div class="modal fade" id="wishlistModal" tabindex="-1" aria-labelledby="wishlistModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="wishlistModalLabel">Wishlist</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="wishlistModalBody">
        <!-- Will be set dynamically via JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
        <a href="wishlist.php" class="btn btn-danger">View Wishlist</a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Wishlist Script - Only load for logged-in users -->
<?php if ($is_logged_in): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
  // For logged-in users only
  const wishlistButtons = document.querySelectorAll(".logged-in-wishlist");
  const wishlistModalEl = document.getElementById("wishlistModal");
  const wishlistModalInstance = bootstrap.Modal.getOrCreateInstance(wishlistModalEl);

  // Cleanup event when modal is closed
  wishlistModalEl.addEventListener('hidden.bs.modal', function () {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    const backdrops = document.getElementsByClassName('modal-backdrop');
    while (backdrops.length > 0) {
      backdrops[0].parentNode.removeChild(backdrops[0]);
    }
  });

  // Add click event listeners to each wishlist button
  wishlistButtons.forEach((button) => {
    button.addEventListener("click", function(e) {
      e.preventDefault();
      const productId = this.getAttribute("data-product-id");
      const inWishlist = this.getAttribute("data-in-wishlist") === "1";

      if (inWishlist) {
        // Already in wishlist
        document.getElementById("wishlistModalBody").innerHTML = "This product is already in your wishlist";
        wishlistModalInstance.show();
      } else {
        // Not in wishlist yet, so let's add it
        fetch('wishlist.php?product_id=' + productId)
          .then(response => response.text())
          .then(data => {
            // On success, mark it as in wishlist
            this.classList.add('in-wishlist');
            this.setAttribute("data-in-wishlist", "1");

            // Switch icon to heart-fill in red
            const icon = this.querySelector('i');
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill', 'text-danger');

            // Update the modal
            document.getElementById("wishlistModalBody").innerHTML = data;
            wishlistModalInstance.show();

            //fetch the latest wishlist count
            fetch("wishlist-count.php")
              .then(res => res.json())
              .then(data => {
                const wishlistBadgeMobile = document.getElementById("wishlistCountBadgeMobile");
                const wishlistBadgeDesktop = document.getElementById("wishlistCountBadgeDesktop");
                
                if (data.count > 0) {
                  // Update mobile badge
                  if (wishlistBadgeMobile) {
                    wishlistBadgeMobile.innerText = data.count;
                    wishlistBadgeMobile.style.display = "inline-block";
                  }
                  
                  // Update desktop badge
                  if (wishlistBadgeDesktop) {
                    wishlistBadgeDesktop.innerText = data.count;
                    wishlistBadgeDesktop.style.display = "inline-block";
                  }
                } else {
                  // Hide both badges
                  if (wishlistBadgeMobile) wishlistBadgeMobile.style.display = "none";
                  if (wishlistBadgeDesktop) wishlistBadgeDesktop.style.display = "none";
                }
              })
              .catch(err => console.error("Error fetching wishlist count:", err));
          })
          .catch(error => {
            console.error("Error adding product to wishlist:", error);
            document.getElementById("wishlistModalBody").innerHTML = "Error adding product to wishlist.";
            wishlistModalInstance.show();
          });
      }
    });
  });
});
</script>
<?php endif; ?>

<!-- Cart Script - Only for logged-in users --->
<?php if ($is_logged_in): ?>
<script>
document.querySelectorAll(".btn-add-to-cart").forEach(function(button) {
  button.addEventListener("click", function(e) {
    e.preventDefault();
    const productId = this.getAttribute("data-product-id");
    // Get quantity from the quantity input (default to 1 if not found)
    let quantity = 1;
    const quantityInput = document.getElementById("quantityInput");
    if (quantityInput) {
      quantity = parseInt(quantityInput.value) || 1;
    }
    const cartModalEl = document.getElementById("cartModal");
    const cartModalInstance = bootstrap.Modal.getOrCreateInstance(cartModalEl);
    const cartModalBody = document.getElementById("cartModalBody");

    // Append quantity to the URL query string
    fetch('cart.php?product_id=' + productId + '&quantity=' + quantity)
      .then(response => response.text())
      .then(data => {
        if (data.includes("Item already reached maximum quantity.")) {
          cartModalBody.innerHTML = "Error: Maximum quantity(10) reached for the product";
        } else if (data.includes("Added to cart") || data.includes("Item added successfully")) {
          cartModalBody.innerHTML = "Added to cart successfully!";
        } else {
          cartModalBody.innerHTML = "Error updating cart.";
        }
        cartModalInstance.show();
      })
      .catch(error => {
        console.error("Error:", error);
        cartModalBody.innerHTML = "Error updating cart.";
        cartModalInstance.show();
      });
  });
});

// Add event listener for when the cart modal is hidden, then update the cart count
const cartModalEl = document.getElementById('cartModal');
cartModalEl.addEventListener('hidden.bs.modal', function () {
    // Perform your cleanup if needed
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    const backdrops = document.getElementsByClassName('modal-backdrop');
    while(backdrops.length > 0) {
      backdrops[0].parentNode.removeChild(backdrops[0]);
    }
    // Refresh the cart count
    refreshCartCount();
});
</script>
<?php endif; ?>

