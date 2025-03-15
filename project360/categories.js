// Function to update the cart count badge using the server endpoint
function updateCartCountFromServer() {
  fetch("cart-count.php")
    .then(response => response.json())
    .then(data => {
      const cartBadge = document.getElementById("cartCountBadge");
      const cartBadgeMobile = document.getElementById("cartCountBadgeMobile");
      const totalQuantity = data.count || 0;

      if (totalQuantity > 0) {
        if (cartBadge) {
          cartBadge.innerText = totalQuantity;
          cartBadge.style.display = "inline-block";
        }
        if (cartBadgeMobile) {
          cartBadgeMobile.innerText = totalQuantity;
          cartBadgeMobile.style.display = "inline-block";
        }
      } else {
        if (cartBadge) cartBadge.style.display = "none";
        if (cartBadgeMobile) cartBadgeMobile.style.display = "none";
      }
    })
    .catch(error => console.error("Error fetching cart count:", error));
}

// Function to send AJAX request to add product to the cart in the database
/*
function addToCart(productId) {
  fetch(`cart.php?action=add&product_id=${productId}`)
    .then(response => response.text())
    .then(text => {
      console.log("Server add-to-cart response:", text);
      // Update Cart Count from the server
      updateCartCountFromServer();
      // Show Bootstrap Modal with the returned message
      const cartModal = bootstrap.Modal.getOrCreateInstance(document.getElementById("cartModal"));
      document.getElementById("cartModalBody").textContent = text;
      cartModal.show();
    })
    .catch(error => {
      console.error("Error adding to cart:", error);
      alert("Error adding to cart: " + error);
    });
}*/

// Wait for DOM to load before attaching event listeners
document.addEventListener("DOMContentLoaded", function () {
  // Select all "Add To Cart" buttons (server-based functionality)
  /*
  document.querySelectorAll(".btn-add-to-cart").forEach(button => {
    button.addEventListener("click", function () {
      const productId = this.dataset.productId;
      console.log("Clicked Add to Cart for Product ID:", productId);
      addToCart(productId);
    });
  });*/

  // Update cart count on page load
  updateCartCountFromServer();

  // --- If you have wishlist functionality, leave that code as is ---
  function updateWishlistCount() {
    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    let totalItems = wishlist.length;

    let wishlistBadgeDesktop = document.getElementById("wishlistCountBadge");
    let wishlistBadgeMobile = document.getElementById("wishlistCountBadgeMobile");

    if (wishlistBadgeDesktop) {
      if (totalItems > 0) {
        wishlistBadgeDesktop.innerText = totalItems;
        wishlistBadgeDesktop.style.display = "inline-block";
      } else {
        wishlistBadgeDesktop.style.display = "none";
      }
    }
    if (wishlistBadgeMobile) {
      if (totalItems > 0) {
        wishlistBadgeMobile.innerText = totalItems;
        wishlistBadgeMobile.style.display = "inline-block";
      } else {
        wishlistBadgeMobile.style.display = "none";
      }
    }
  }
  
  updateWishlistCount();
});
