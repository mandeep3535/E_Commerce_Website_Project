// Ensure the DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
 
  const wishlistContainer = document.getElementById("wishlistContainer");
  const wishlistHeader    = document.getElementById("wishlistHeader");
  const moveAllBtn        = document.getElementById("moveAllButton");

  // We keep the cart in localStorage
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  //-----------------------------------
  // UTILITY: Update cart in localStorage, then update the top icon
  //-----------------------------------
  function saveCartAndUpdateCount() {
    localStorage.setItem("cart", JSON.stringify(cart));
    if (typeof window.updateCartCount === "function") {
      window.updateCartCount(); 
    }
  }

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
  // ADD an item to localStorage CART
  //-----------------------------------
  function addToCart(product) {
    const existingProduct = cart.find(item => item.name === product.name);
    if (existingProduct) {
      existingProduct.quantity += 1;
    } else {
      cart.push({ ...product, quantity: 1 });
    }
    saveCartAndUpdateCount();

    // Show confirmation modal
    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
    document.getElementById('cartModalBody').textContent = `${product.name} added to cart!`;
    cartModal.show();
  }

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
      const productName  = addCartBtn.getAttribute("data-product-name");
      const productPrice = parseFloat(addCartBtn.getAttribute("data-product-price")) || 0;
      const productImage = addCartBtn.getAttribute("data-product-image");

      addToCart({ name: productName, price: productPrice, image: productImage });
      return;
    }
  });

  //-----------------------------------
  // EVENT: "Move All To Bag" => remove each from DB, add to cart
  //-----------------------------------
  moveAllBtn.addEventListener("click", function() {
    const allCols = wishlistContainer.querySelectorAll(".col");
    if (!allCols.length) return;

    allCols.forEach(col => {
      const removeBtn  = col.querySelector(".remove-wishlist-item");
      const cartBtn    = col.querySelector(".add-to-cart-btn");
      if (!removeBtn || !cartBtn) return;

      // 1) Remove from DB
      const productId = removeBtn.getAttribute("data-product-id");
      fetch(`wishlist.php?action=remove&product_id=${productId}`)
        .then(r => r.text())
        .then(resp => {
          console.log("MoveAll => server says:", resp);
        })
        .catch(err => console.error("MoveAll => error:", err));

      // 2) Add to localStorage cart
      const productName  = cartBtn.getAttribute("data-product-name");
      const productPrice = parseFloat(cartBtn.getAttribute("data-product-price")) || 0;
      const productImage = cartBtn.getAttribute("data-product-image");

      const existingProduct = cart.find(item => item.name === productName);
      if (existingProduct) {
        existingProduct.quantity += 1;
      } else {
        cart.push({ name: productName, price: productPrice, image: productImage, quantity: 1 });
      }
    });

    // Clear the DOM
    wishlistContainer.innerHTML = `<p class="text-muted">You have no items in your wishlist.</p>`;
    updateWishlistCount();
    saveCartAndUpdateCount();
    
    // Refresh header wishlist count from server after move all
    refreshHeaderWishlistCount();

    // Possibly show a message
    alert("All items moved to cart!");
  });

  //-----------------------------------
  // On load, do a final local count and update header from server
  //-----------------------------------
  updateWishlistCount();
  refreshHeaderWishlistCount();
  saveCartAndUpdateCount(); // updates cart count if you have one
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