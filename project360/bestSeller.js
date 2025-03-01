// home-products.js

// Function to update the cart count badge(s)
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
  
  // Function to update the wishlist count badge(s)
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
  
  // Wait for the DOM to load
  document.addEventListener("DOMContentLoaded", function () {
  
    // === CART FUNCTIONALITY ===
    const addToCartButtons = document.querySelectorAll("button.btn-add-to-cart");
    addToCartButtons.forEach(button => {
      button.addEventListener("click", function () {
        const card = button.closest(".product-card");
        if (!card) return;
        
        const productNameEl = card.querySelector("h6, p.fw-semibold");
        const productName = productNameEl ? productNameEl.innerText.trim() : "Unknown Product";
        
        const priceEl = card.querySelector("p.text-danger");
        const priceText = priceEl ? priceEl.innerText.trim() : "";
        const priceMatch = priceText.match(/\$(\d+(\.\d+)?)/);
        const productPrice = priceMatch ? parseFloat(priceMatch[1]) : 0;
        
        const productImageEl = card.querySelector("img.product-img");
        const productImage = productImageEl ? productImageEl.getAttribute("src") : "";
        
        const product = {
          name: productName,
          price: productPrice,
          quantity: 1,
          image: productImage
        };
        
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        const existingProduct = cart.find(item => item.name === product.name);
        if (existingProduct) {
          existingProduct.quantity += product.quantity;
        } else {
          cart.push(product);
        }
        localStorage.setItem("cart", JSON.stringify(cart));
        updateCartCount();
        
        // Show confirmation modal or alert
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
    const wishlistButtons = document.querySelectorAll("button.wishlist-icon");
    wishlistButtons.forEach(button => {
      button.addEventListener("click", function () {
        const card = button.closest(".product-card");
        if (!card) return;
        
        const productNameEl = card.querySelector("h6, p.fw-semibold");
        const productName = productNameEl ? productNameEl.innerText.trim() : "Unknown Product";
        
        const priceEl = card.querySelector("p.text-danger");
        const priceText = priceEl ? priceEl.innerText.trim() : "";
        const priceMatch = priceText.match(/\$(\d+(\.\d+)?)/);
        const productPrice = priceMatch ? parseFloat(priceMatch[1]) : 0;
        
        const productImageEl = card.querySelector("img.product-img");
        const productImage = productImageEl ? productImageEl.getAttribute("src") : "";
        
        const product = {
          name: productName,
          price: productPrice,
          image: productImage
        };
        
        let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
        const existingProduct = wishlist.find(item => item.name === product.name);
        if (!existingProduct) {
          wishlist.push(product);
          localStorage.setItem("wishlist", JSON.stringify(wishlist));
          updateWishlistCount();
          
          if (typeof bootstrap !== "undefined") {
            const wishlistModalElement = document.getElementById("wishlistModal");
            if (wishlistModalElement) {
              const wishlistModal = new bootstrap.Modal(wishlistModalElement);
              const wishlistModalBody = document.getElementById("wishlistModalBody");
              if (wishlistModalBody) {
                wishlistModalBody.textContent = `${product.name} added to wishlist!`;
              }
              wishlistModal.show();
            } else {
              alert(`${product.name} added to wishlist!`);
            }
          } else {
            alert(`${product.name} added to wishlist!`);
          }
        } else {
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
    
    updateCartCount();
    updateWishlistCount();
  });
  