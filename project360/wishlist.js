// Ensure the DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
 
  const wishlistContainer = document.getElementById("wishlistContainer");
  const wishlistHeader    = document.getElementById("wishlistHeader");
  const moveAllBtn        = document.getElementById("moveAllButton");

  // ----------------------------------------------------------------
  // ORIGINAL localStorage cart code (disabled):
  // let cart = JSON.parse(localStorage.getItem("cart")) || [];
  // function saveCartAndUpdateCount() {
  //   localStorage.setItem("cart", JSON.stringify(cart));
  //   if (typeof window.updateCartCount === "function") {
  //     window.updateCartCount(); 
  //   }
  // }
  // ----------------------------------------------------------------

  //-----------------------------------
  // UTILITY: Recount how many items are in the wishlist DOM (local count)
  //-----------------------------------
  function updateWishlistCount() {
    const itemCount = wishlistContainer.querySelectorAll(".product-card").length;
    wishlistHeader.textContent = `Wishlist (${itemCount})`;
    if (typeof window.updateWishlistIconCount === "function") {
      window.updateWishlistIconCount(itemCount);
    }
  }

  //-----------------------------------
  // FUNCTION: Refresh header wishlist count from server
  //-----------------------------------
  function refreshHeaderWishlistCount() {
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

  //-----------------------------------
  // REMOVE an item from DB & DOM (trash icon)
  //-----------------------------------
  function removeItemFromDB(productId, colEl) {
    fetch(`wishlist.php?action=remove&product_id=${productId}`)
      .then(response => response.text())
      .then(data => {
        console.log("Server remove response:", data);
        // Remove from DOM
        colEl.remove();
        // Update local DOM count
        updateWishlistCount();
        // Refresh header wishlist count from server
        refreshHeaderWishlistCount();
      })
      .catch(err => {
        console.error("Error removing wishlist item:", err);
      });
  }

  //-----------------------------------
  // ORIGINAL function to add an item to localStorage CART (disabled):
  // function addToCart(product) {
  //   const existingProduct = cart.find(item => item.name === product.name);
  //   if (existingProduct) {
  //     existingProduct.quantity += 1;
  //   } else {
  //     cart.push({ ...product, quantity: 1 });
  //   }
  //   saveCartAndUpdateCount();
  //
  //   // Show confirmation modal
  //   const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
  //   document.getElementById('cartModalBody').textContent = `${product.name} added to cart!`;
  //   cartModal.show();
  // }
  //-----------------------------------
  // EVENT: Clicking inside the wishlistContainer
  //-----------------------------------
  wishlistContainer.addEventListener("click", function(e) {
    const removeBtn = e.target.closest(".remove-wishlist-item");
    if (removeBtn) {
      // TRASH ICON was clicked
      const productId = removeBtn.getAttribute("data-product-id");
      const colEl = removeBtn.closest(".col");
      removeItemFromDB(productId, colEl);
      return;
    }

    const addCartBtn = e.target.closest(".add-to-cart-btn");
    if (addCartBtn) {
      // ADD TO CART was clicked
      const productId   = addCartBtn.getAttribute("data-product-id");
      const productName = addCartBtn.getAttribute("data-product-name");
      const productPrice = parseFloat(addCartBtn.getAttribute("data-product-price")) || 0;
      const productImage = addCartBtn.getAttribute("data-product-image");
    
      // Use fetch to add the item to the cart DB (like on Phones)
      fetch(`cart.php?product_id=${productId}&quantity=1`)
        .then(response => response.text())
        .then(data => {
          console.log("Add to cart response:", data);
          refreshCartCount();
          // remove the item from the wishlist 
          const colEl = addCartBtn.closest(".col");
    if (colEl) {
      removeItemFromDB(productId, colEl);
    }
          // Show confirmation modal
          const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
          document.getElementById('cartModalBody').textContent = "Added to cart successfully!";
          cartModal.show();
        })
        .catch(err => console.error("Error adding to cart:", err));
      return;
    }
  });

  //-----------------------------------
  // EVENT: "Move All To Bag" => remove each from DB, add to cart
  //-----------------------------------
  moveAllBtn.addEventListener("click", function() {
    const allCols = wishlistContainer.querySelectorAll(".col");
    if (!allCols.length) return;
    
    // Array to hold all fetch promises for moveAll operations
    let allPromises = [];
    
    allCols.forEach(col => {
      const removeBtn = col.querySelector(".remove-wishlist-item");
      const cartBtn   = col.querySelector(".add-to-cart-btn");
      if (!removeBtn || !cartBtn) return;
      
      const productId = removeBtn.getAttribute("data-product-id");
      
      // Remove from wishlist DB and then from the DOM
      let removePromise = fetch(`wishlist.php?action=remove&product_id=${productId}`)
        .then(response => response.text())
        .then(data => {
           console.log("MoveAll => remove response:", data);
           // Remove from DOM
           col.remove();
        })
        .catch(err => console.error("MoveAll => remove error:", err));
        
      // Add to cart DB
      let addPromise = fetch(`cart.php?product_id=${productId}&quantity=1`)
        .then(response => response.text())
        .then(data => {
           console.log("MoveAll => add to cart response:", data);
        })
        .catch(err => console.error("MoveAll => add to cart error:", err));
        
      // Combine both promises for this product
      allPromises.push(Promise.all([removePromise, addPromise]));
    });
    
    // When all fetch operations have finished:
    Promise.all(allPromises).then(() => {
      updateWishlistCount();       // Update local DOM count
      refreshHeaderWishlistCount(); // Update header count from server
      if (typeof refreshCartCount === "function") {
         refreshCartCount();       // Update cart count, if available
      }
      
      // Show confirmation modal instead of an alert
      const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
      document.getElementById('cartModalBody').textContent = "All items added to cart successfully!";
      cartModal.show();
    });
  });
  

  //-----------------------------------
  // On load, do a final local count and update header from server
  //-----------------------------------
  updateWishlistCount();
  refreshHeaderWishlistCount();
  // ----------------------------------------------------------------
  // ORIGINAL: saveCartAndUpdateCount(); // updates cart count if you have one
  // ----------------------------------------------------------------
});

/* "Just for you" remains the same (adds to localStorage cart) */
document.addEventListener("DOMContentLoaded", function() {
  const justForYouContainer = document.getElementById("justForYouContainer");

  justForYouContainer.addEventListener("click", function(e) {
    if (e.target.closest(".btn.btn-danger")) {
      const productCard = e.target.closest(".product-card");
      if (!productCard) return;

      const imgEl   = productCard.querySelector(".product-img");
      const titleEl = productCard.querySelector(".fw-semibold");
      const priceEl = productCard.querySelector(".text-danger");

      const productName  = titleEl ? titleEl.textContent.trim() : (imgEl ? imgEl.alt : "Unknown");
      const rawPrice     = priceEl ? priceEl.textContent.replace("$", "").trim() : "0";
      const productPrice = parseFloat(rawPrice) || 0;
      const productImage = imgEl ? imgEl.src : "";

      // This section still uses localStorage for the "Just for you" section
      const product = { name: productName, price: productPrice, image: productImage, quantity: 1 };

      let cart = JSON.parse(localStorage.getItem("cart")) || [];
      const existingItem = cart.find(item => item.name === productName);
      if (existingItem) {
        existingItem.quantity += 1; 
      } else {
        cart.push(product);
      }
      localStorage.setItem("cart", JSON.stringify(cart));

      if (typeof window.updateCartCount === "function") {
        window.updateCartCount();
      }

      // Show add to cart modal 
      const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
      document.getElementById('cartModalBody').textContent = `${product.name} added to cart!`;
      cartModal.show();
    }
  });
});
