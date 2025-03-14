// This runs when the page first loads
document.addEventListener("DOMContentLoaded", function () {
  updateWishlistBadge();
  updateCartCount();
  refreshWishlistCount();
});

// This event fires even when a page is restored from cache (e.g. using the back button)
window.addEventListener("pageshow", function () {
  updateWishlistBadge();
  refreshWishlistCount();
});

function refreshWishlistCount() {
  fetch("wishlist-count.php")
    .then(res => res.json())
    .then(data => {
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

function updateCartCount() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let totalQuantity = cart.reduce((sum, item) => sum + item.quantity, 0);

  let cartBadge = document.getElementById("cartCountBadge");
  let cartBadgeMobile = document.getElementById("cartCountBadgeMobile");

  if (totalQuantity > 0) {
    cartBadge.innerText = totalQuantity;
    cartBadge.style.display = "inline-block";
    cartBadgeMobile.innerText = totalQuantity;
    cartBadgeMobile.style.display = "inline-block";
  } else {
    cartBadge.style.display = "none";
    cartBadgeMobile.style.display = "none";
  }
}

window.addEventListener("storage", function () {
  updateCartCount();
  updateWishlistBadge();
});

function updateWishlistBadge() {
  console.log('updateWishlistBadge called');
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
