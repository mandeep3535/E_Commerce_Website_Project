document.addEventListener("DOMContentLoaded", function () {
    // DOM Elements
    const wishlistContainer = document.getElementById("wishlistContainer");
    const wishlistHeader = document.getElementById("wishlistHeader");
    const moveAllBtn = document.getElementById("moveAllButton");
  
    // Initialize Data
    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
  
    // Update wishlist count display
    const updateWishlistCount = () => {
      wishlistHeader.textContent = `Wishlist (${wishlist.length})`;
    };
  
    // Render wishlist items
    const renderWishlist = () => {
      wishlistContainer.innerHTML = wishlist.length === 0 
        ? `<div class="col">Your wishlist is empty!</div>`
        : wishlist.map(item => `
            <div class="col">
              <div class="product-card h-100 p-3 position-relative d-flex flex-column">
                <button class="icon-btn remove-wishlist-item">
                  <i class="bi bi-trash"></i>
                </button>
                <div class="text-center mb-2">
                  <img src="${item.image}" alt="${item.name}" class="img-fluid product-img" />
                </div>
                <button class="btn btn-danger btn-sm mb-1 add-to-cart-btn">Add To Cart</button>
                <p class="mb-0 fw-semibold">${item.name}</p>
                <p class="text-danger mb-0">$${item.price}</p>
              </div>
            </div>
          `).join('');
    };
  
    // Remove item from wishlist
    const removeItem = (productName) => {
      wishlist = wishlist.filter(item => item.name !== productName);
      localStorage.setItem("wishlist", JSON.stringify(wishlist));
      renderWishlist();
      updateWishlistCount();
    };
  
    // Add item to cart
    const addToCart = (product) => {
      const existingProduct = cart.find(item => item.name === product.name);
      existingProduct ? existingProduct.quantity += 1 : cart.push({...product, quantity: 1});
      localStorage.setItem("cart", JSON.stringify(cart));
      // ❗️ Remove it from the wishlist as well
    removeItem(product.name);
      // Show confirmation modal
      new bootstrap.Modal(document.getElementById('cartModal')).show();
      document.getElementById('cartModalBody').textContent = `${product.name} added to cart!`;
    };
  
    // Event Delegation
    wishlistContainer.addEventListener('click', (e) => {
      const card = e.target.closest('.product-card');
      if (!card) return;
  
      // Handle remove button
      if (e.target.closest('.remove-wishlist-item')) {
        const productName = card.querySelector('.product-img').alt;
        removeItem(productName);
      }
  
      // Handle add to cart button
      if (e.target.closest('.add-to-cart-btn')) {
        const product = wishlist.find(item => item.name === card.querySelector('.product-img').alt);
        addToCart(product);
      }
    });
  
    // Move all to cart
    moveAllBtn.addEventListener('click', () => {
      cart = [...cart, ...wishlist.map(item => ({...item, quantity: 1}))];
      localStorage.setItem("cart", JSON.stringify(cart));
      wishlist = [];
      localStorage.removeItem("wishlist");
      renderWishlist();
      updateWishlistCount();
    });
  
    // Initial render
    renderWishlist();
    updateWishlistCount();
  });
  
/*Just for you section*/
  document.addEventListener("DOMContentLoaded", function() {
    // 1. Select the "Just For You" container
    const justForYouContainer = document.getElementById("justForYouContainer");
  
    // 2. Attach an event listener for clicks on any "Add To Cart" button inside
    justForYouContainer.addEventListener("click", function(e) {
      // Check if the click was on (or inside) a button that says "Add To Cart"
      if (e.target.closest(".btn.btn-danger")) {
        // 3. Find the closest .product-card
        const productCard = e.target.closest(".product-card");
        if (!productCard) return;
  
        // 4. Extract product info from the DOM
        const imgEl = productCard.querySelector(".product-img");
        const titleEl = productCard.querySelector(".fw-semibold"); 
        const priceEl = productCard.querySelector(".text-danger");
  
        // Name (from <p class="fw-semibold">... </p> or img alt)
        const productName = titleEl ? titleEl.textContent.trim() : imgEl.alt;
        // Price (parse from text content like "$960")
        const rawPrice = priceEl ? priceEl.textContent.replace("$", "").trim() : "0";
        const productPrice = parseFloat(rawPrice) || 0;
        // Image src
        const productImage = imgEl ? imgEl.src : "";
  
        // 5. Build the product object
        const product = {
          name: productName,
          price: productPrice,
          image: productImage,
          quantity: 1 // Default quantity
        };
  
        // 6. Add to cart in localStorage
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        // Check if item already in cart
        const existingItem = cart.find(item => item.name === productName);
        if (existingItem) {
          existingItem.quantity += 1; 
        } else {
          cart.push(product);
        }
        localStorage.setItem("cart", JSON.stringify(cart));
  
        // 7. Show add to cart modal 
        const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
        document.getElementById('cartModalBody').textContent = `${product.name} added to cart!`;
        cartModal.show();
        
        // If you have a global function to update cart count in the navbar:
        if (window.updateCartCount) {
          window.updateCartCount();
        }
      }
    });
  });
  