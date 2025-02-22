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

 // Ensure cart count updates when the page loads
 document.addEventListener("DOMContentLoaded", function () {
     updateCartCount();
 });

 // Listen for storage changes (Handles changes made in different tabs/windows)
 window.addEventListener("storage", function () {
     updateCartCount();
 });

 /*update wishlist count*/
 function updateWishlistCount() {
  // 1. Read the wishlist from localStorage
  let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];

  // 2. The total number of items is just the length
  let totalItems = wishlist.length;

  // 3. Get references to the desktop & mobile badges
  const wishlistBadgeDesktop = document.getElementById("wishlistCountBadge");
  const wishlistBadgeMobile = document.getElementById("wishlistCountBadgeMobile");

  // 4. Show or hide each badge
  if (totalItems > 0) {
    if (wishlistBadgeDesktop) {
      wishlistBadgeDesktop.innerText = totalItems;
      wishlistBadgeDesktop.style.display = "inline-block";
    }
    if (wishlistBadgeMobile) {
      wishlistBadgeMobile.innerText = totalItems;
      wishlistBadgeMobile.style.display = "inline-block";
    }
  } else {
    if (wishlistBadgeDesktop) wishlistBadgeDesktop.style.display = "none";
    if (wishlistBadgeMobile) wishlistBadgeMobile.style.display = "none";
  }
}

// Run once DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  updateWishlistCount();
});

// Listen for storage changes (in case another tab updates localStorage)
window.addEventListener("storage", function () {
  updateWishlistCount();
});

wishlistBtn.addEventListener("click", function () {
  let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];

  // ... check if product is already in the wishlist ...
  wishlist.push(product);
  localStorage.setItem("wishlist", JSON.stringify(wishlist));

  // Update the badge count
  updateWishlistCount();
});

