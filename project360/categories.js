
// Function to update the cart count badge
function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let totalQuantity = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
  
    // Update both desktop and mobile cart count badges 
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
  
  // Function to update the wishlist count badge
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
  
  // Wait for the DOM to load before attaching event listeners
  document.addEventListener("DOMContentLoaded", function () {
    // === CART FUNCTIONALITY ===
    // Select all Add To Cart buttons using a unique class
    const addToCartButtons = document.querySelectorAll("button.btn-add-to-cart");
  
    addToCartButtons.forEach(button => {
      button.addEventListener("click", function () {
        // Find the product card container (assuming the button is inside it)
        const card = button.closest(".product-card");
        if (!card) return;
  
        // Extract the product name from the card
        const productNameEl = card.querySelector("p.fw-semibold");
        const productName = productNameEl ? productNameEl.innerText.trim() : "Unknown Product";
  
        // Extract the price from the price element 
        const priceEl = card.querySelector("p.text-danger.mb-1");
        const priceText = priceEl ? priceEl.innerText.trim() : "";
        const priceMatch = priceText.match(/\$(\d+(\.\d+)?)/);
        const productPrice = priceMatch ? parseFloat(priceMatch[1]) : 0;
  
        // Get the product image source
        const productImageEl = card.querySelector("img.product-img");
        const productImage = productImageEl ? productImageEl.getAttribute("src") : "";
  
        // Create a product object 
        const product = {
          name: productName,
          price: productPrice,
          quantity: 1,
          image: productImage
        };
  
        // Retrieve the current cart from localStorage
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
  
        // Check if the product already exists in the cart; if so, increment its quantity
        const existingProduct = cart.find(item => item.name === product.name);
        if (existingProduct) {
          existingProduct.quantity += product.quantity;
        } else {
          cart.push(product);
        }
        // Save the updated cart to localStorage
        localStorage.setItem("cart", JSON.stringify(cart));
        // Update the cart count on the page
        updateCartCount();
  
        // Show a confirmation modal 
        if (typeof bootstrap !== "undefined") {
          const cartModalElement = document.getElementById("cartModal");
          if (cartModalElement) {
            const cartModal = new bootstrap.Modal(cartModalElement);
            const cartModalBody = document.getElementById("cartModalBody");
            if (cartModalBody) {
              cartModalBody.textContent = `${product.quantity} × ${product.name} added to cart!`;
            }
            cartModal.show();
          } else {
            alert(`${product.quantity} × ${product.name} added to cart!`);
          }
        } else {
          alert(`${product.quantity} × ${product.name} added to cart!`);
        }
      });
    });
  
    // === WISHLIST FUNCTIONALITY ===
    // Select all wishlist icon buttons (heart icons) in each product card
    const wishlistButtons = document.querySelectorAll("button.wishlist-icon");
  
    wishlistButtons.forEach(button => {
      button.addEventListener("click", function () {
        // Find the product card container
        const card = button.closest(".product-card");
        if (!card) return;
  
        // Extract product details
        const productNameEl = card.querySelector("p.fw-semibold");
        const productName = productNameEl ? productNameEl.innerText.trim() : "Unknown Product";
  
        const priceEl = card.querySelector("p.text-danger.mb-1");
        const priceText = priceEl ? priceEl.innerText.trim() : "";
        const priceMatch = priceText.match(/\$(\d+(\.\d+)?)/);
        const productPrice = priceMatch ? parseFloat(priceMatch[1]) : 0;
  
        const productImageEl = card.querySelector("img.product-img");
        const productImage = productImageEl ? productImageEl.getAttribute("src") : "";
  
        // Create a wishlist product object (quantity is not needed for wishlist)
        const product = {
          name: productName,
          price: productPrice,
          image: productImage
        };
  
        // Retrieve the current wishlist from localStorage
        let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
  
        // Check if the product already exists in the wishlist
        const existingProduct = wishlist.find(item => item.name === product.name);
        if (!existingProduct) {
          wishlist.push(product);
          localStorage.setItem("wishlist", JSON.stringify(wishlist));
          updateWishlistCount();
  
          // Show a confirmation modal if available, or fallback to an alert
          if (typeof bootstrap !== "undefined") {
            const wishlistModalElement = document.getElementById("wishlistModal");
            if (wishlistModalElement) {
              const wishlistModal = new bootstrap.Modal(wishlistModalElement);
              const wishlistModalBody = document.getElementById("wishlistModalBody");
              if (wishlistModalBody) {
                wishlistModalBody.textContent = `${product.name} has been added to your wishlist!`;
              }
              wishlistModal.show();
            } else {
              alert(`${product.name} has been added to your wishlist!`);
            }
          } else {
            alert(`${product.name} has been added to your wishlist!`);
          }
        } else {
          // If already in wishlist, notify the user
          if (typeof bootstrap !== "undefined") {
            const wishlistModalElement = document.getElementById("wishlistModal");
            if (wishlistModalElement) {
              const wishlistModal = new bootstrap.Modal(wishlistModalElement);
              const wishlistModalBody = document.getElementById("wishlistModalBody");
              if (wishlistModalBody) {
                wishlistModalBody.textContent = `${product.name} is already in your wishlist!`;
              }
              wishlistModal.show();
            } else {
              alert(`${product.name} is already in your wishlist!`);
            }
          } else {
            alert(`${product.name} is already in your wishlist!`);
          }
        }
      });
    });
  
    // Update both cart and wishlist counts on page load
    updateCartCount();
    updateWishlistCount();
  });
  