// the DOM to load
document.addEventListener("DOMContentLoaded", function () {
  // Capture the main image element and store its default source
  const mainImageElement = document.getElementById("mainProductImage");
  const defaultImageSrc = mainImageElement.getAttribute("src");
  
  // Single changeMainImage function – no duplicate definitions.
  window.changeMainImage = function (imageUrl, element) {
      // Update the main image's src with the provided URL.
      mainImageElement.src = imageUrl;
      
      // Remove "active" class from all thumbnail cards.
      document.querySelectorAll('.thumb-card').forEach(card => card.classList.remove('active'));
      
      // Add "active" class to the clicked thumbnail.
      element.classList.add('active');
  };

  // Quantity and Price Update Section
  let quantityInput = document.getElementById("quantityInput");
  let productPriceElement = document.getElementById("productPrice");

  // Parse the base price from the productPrice element 
  let basePrice = parseFloat(productPriceElement.innerText.replace("$", "").trim());

  function updatePrice() {
      let quantity = parseInt(quantityInput.value);
      if (isNaN(quantity) || quantity < 1) {
          quantity = 1;
          quantityInput.value = 1;
      }
      let totalPrice = (basePrice * quantity).toFixed(2);
      productPriceElement.innerText = `$${totalPrice}`;
  }

  // Increase quantity button
  document.getElementById("increaseQty").addEventListener("click", function () {
      quantityInput.value = parseInt(quantityInput.value) + 1;
      updatePrice();
  });

  // Decrease quantity button
  document.getElementById("decreaseQty").addEventListener("click", function () {
      if (parseInt(quantityInput.value) > 1) {
          quantityInput.value = parseInt(quantityInput.value) - 1;
          updatePrice();
      }
  });

  // Update price on manual input change
  quantityInput.addEventListener("input", function () {
      updatePrice();
  });

  // Initialize price on page load
  updatePrice();

  // Cart count update function
  function updateCartCount() {
      let cart = JSON.parse(localStorage.getItem("cart")) || [];
      let totalQuantity = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);

      let cartBadge = document.getElementById("cartCountBadge");
      let cartBadgeMobile = document.getElementById("cartCountBadgeMobile");

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
  }

  // Update cart count on load 
  setTimeout(updateCartCount, 500);
  updateCartCount();

  // Buy Now Button – Always use the default image from page load
  document.getElementById("buyNowBtn").addEventListener("click", function() {
      // Get product details, but override the image with defaultImageSrc
      const product = {
          name: document.getElementById("productName").textContent,
          price: parseFloat(document.getElementById("productPrice").innerText.replace("$", "")), 
          quantity: parseInt(document.getElementById("quantityInput").value),
          image: defaultImageSrc  // Always use the original default image
      };

      const cart = JSON.parse(localStorage.getItem("cart")) || [];
      const existingProduct = cart.find(item => item.name === product.name);

      if (existingProduct) {
          existingProduct.quantity += product.quantity;
      } else {
          cart.push(product);
      }

      localStorage.setItem("cart", JSON.stringify(cart));
      updateCartCount();

      // Show the cart modal with success message
      const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
      const cartModalBody = document.getElementById('cartModalBody');
      cartModalBody.textContent = `${product.quantity} × ${product.name} added to cart!`;
      cartModal.show();
  });

  // Ensure cart count updates when page loads
  updateCartCount();

  // addToCart function (if needed elsewhere)
  function addToCart() {
      const imagePath = mainImageElement.getAttribute("src");
      const product = {
          name: document.getElementById("productName").textContent,
          price: parseFloat(document.getElementById("productPrice").textContent.replace("$", "")),
          quantity: parseInt(document.getElementById("quantityInput").value),
          image: imagePath
      };

      const cart = JSON.parse(localStorage.getItem("cart")) || [];
      cart.push(product);
      localStorage.setItem("cart", JSON.stringify(cart));
  }

  /* Update Wishlist Count */
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
      } else {
        setTimeout(updateWishlistCount, 500);
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

  // WishList Button – add product to wishlist
  const wishlistBtn = document.getElementById("wishlistBtn");
  wishlistBtn.addEventListener("click", function () {
      const product = {
          name: document.getElementById("productName").textContent,
          price: parseFloat(document.getElementById("productPrice").innerText.replace("$", "")),
          image: mainImageElement.getAttribute("src")
      };

      let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
      const existingProduct = wishlist.find(item => item.name === product.name);
      const wishlistModal = new bootstrap.Modal(document.getElementById('wishlistModal'));
      const wishlistModalBody = document.getElementById('wishlistModalBody');

      if (!existingProduct) {
          wishlist.push(product);
          localStorage.setItem("wishlist", JSON.stringify(wishlist));
          updateWishlistCount();
          wishlistModalBody.textContent = `${product.name} has been added to your wishlist!`;
      } else {
          wishlistModalBody.textContent = `${product.name} is already in your wishlist!`;
      }
      wishlistModal.show();
  });

  // Listen to storage events (for multiple tabs)
  window.addEventListener("storage", function () {
      updateCartCount();
      updateWishlistCount();
  });
});
