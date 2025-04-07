document.addEventListener("DOMContentLoaded", function () {
  refreshWishlistCount();
  refreshCartCount();
});

window.addEventListener("pageshow", function () {
  refreshWishlistCount();
  refreshCartCount();
});

// Listen for storage changes in other tabs/windows
window.addEventListener("storage", function () {
  refreshWishlistCount();
  refreshCartCount();
});

function refreshWishlistCount() {
  fetch("wishlist-count.php")
    .then(res => res.json())
    .then(data => {
      console.log("Fetched wishlist count:", data.count);
      const newCount = data.count || 0;
      
      // Update Mobile Badge
      const wishlistBadgeMobile = document.getElementById("wishlistCountBadgeMobile");
      if (wishlistBadgeMobile) {
        if (newCount > 0) {
          wishlistBadgeMobile.innerText = newCount;
          wishlistBadgeMobile.style.display = "inline-block";
        } else {
          wishlistBadgeMobile.style.display = "none";
        }
      }
      
      // Update Desktop Badge
      const wishlistBadgeDesktop = document.getElementById("wishlistCountBadgeDesktop");
      if (wishlistBadgeDesktop) {
        if (newCount > 0) {
          wishlistBadgeDesktop.innerText = newCount;
          wishlistBadgeDesktop.style.display = "inline-block";
        } else {
          wishlistBadgeDesktop.style.display = "none";
        }
      }
    })
    .catch(err => console.error("Error fetching wishlist count:", err));
}

function refreshCartCount() {
  console.log('refreshCartCount called');
  fetch("cart-count.php")
    .then(res => res.json())
    .then(data => {
      console.log("Fetched cart count:", data.count);
      const newCount = data.count || 0;
      
      // Update Mobile Badge
      const cartBadgeMobile = document.getElementById("cartCountBadgeMobile");
      if (cartBadgeMobile) {
        if (newCount > 0) {
          cartBadgeMobile.innerText = newCount;
          cartBadgeMobile.style.display = "inline-block";
        } else {
          cartBadgeMobile.style.display = "none";
        }
      }
      
      // Update Desktop Badge
      const cartBadgeDesktop = document.getElementById("cartCountBadge");
      if (cartBadgeDesktop) {
        if (newCount > 0) {
          cartBadgeDesktop.innerText = newCount;
          cartBadgeDesktop.style.display = "inline-block";
        } else {
          cartBadgeDesktop.style.display = "none";
        }
      }
    })
    .catch(err => console.error("Error fetching cart count:", err));
}