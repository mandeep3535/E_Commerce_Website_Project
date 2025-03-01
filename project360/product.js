function changeMainImage(imageSrc, element) {
    document.getElementById("mainProductImage").src ="images/" + imageSrc;

    document.querySelectorAll('.thumb-card').forEach(card => card.classList.remove('active'));
    
    element.classList.add('active');
}

document.addEventListener("DOMContentLoaded", function () {
    let quantityInput = document.getElementById("quantityInput");
    let productPriceElement = document.getElementById("productPrice");

    // Fetch and store the original price dynamically
    let basePrice = parseFloat(productPriceElement.innerText.replace("$", "").trim());

    function updatePrice() {
        let quantity = parseInt(quantityInput.value);

        // Ensure quantity is at least 1
        if (isNaN(quantity) || quantity < 1) {
            quantity = 1;
            quantityInput.value = 1;
        }

        // Calculate and update price in the same h4 element
        let totalPrice = (basePrice * quantity).toFixed(2);
        productPriceElement.innerText = `$${totalPrice}`;
    }

    // Increase quantity
    document.getElementById("increaseQty").addEventListener("click", function () {
        quantityInput.value = parseInt(quantityInput.value) + 1;
        updatePrice();
    });

    // Decrease quantity
    document.getElementById("decreaseQty").addEventListener("click", function () {
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
            updatePrice();
        }
    });

    // Update price when manually changing the input
    quantityInput.addEventListener("input", function () {
        updatePrice();
    });

    // Initialize Price on page load
    updatePrice();
});

function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let totalQuantity = cart.reduce((sum, item) => sum + (item.quantity || 1), 0); // Ensure quantity exists

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

// Ensure cart count updates when the page loads
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(updateCartCount, 500); // 🔥 Delay in case elements are loaded later
});

// Ensure cart count updates correctly when the page loads
document.addEventListener("DOMContentLoaded", function () {
updateCartCount();
});

document.getElementById("buyNowBtn").addEventListener("click", function() {
// Get current product details from the page
const product = {
    name: document.getElementById("productName").textContent,
    price: parseFloat(document.getElementById("productPrice").innerText.replace("$", "")), 
    quantity: parseInt(document.getElementById("quantityInput").value),
    image: document.getElementById("mainProductImage").getAttribute("src") // Get actual src attribute
};

// Rest of your cart logic...
const cart = JSON.parse(localStorage.getItem("cart")) || [];
const existingProduct = cart.find(item => item.name === product.name);

if (existingProduct) {
    existingProduct.quantity += product.quantity;
} else {
    cart.push(product);
}

localStorage.setItem("cart", JSON.stringify(cart));
updateCartCount();

// Show the Modal
const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
const cartModalBody = document.getElementById('cartModalBody');

cartModalBody.textContent = `${product.quantity} × ${product.name} added to cart!`;

// Show the modal
cartModal.show();
});

// Ensure the cart count updates correctly when switching pages or reloading
document.addEventListener("DOMContentLoaded", function () {
updateCartCount();
});

// product.html
function addToCart() {
// Get the RELATIVE path from the src attribute
const imagePath = document.getElementById("mainProductImage").getAttribute("src");

const product = {
    name: document.getElementById("productName").textContent,
    price: parseFloat(document.getElementById("productPrice").textContent.replace("$", "")),
    quantity: parseInt(document.getElementById("quantityInput").value),
    image: imagePath // Will store "images/havit.jpg" (relative path)
};

// Save to localStorage
const cart = JSON.parse(localStorage.getItem("cart")) || [];
cart.push(product);
localStorage.setItem("cart", JSON.stringify(cart));
}

/* Update Wishlist count */
function updateWishlistCount() {
    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    let totalItems = wishlist.length;
  
    // Desktop badge
    let wishlistBadgeDesktop = document.getElementById("wishlistCountBadge");
    // Mobile badge (if you have one)
    let wishlistBadgeMobile = document.getElementById("wishlistCountBadgeMobile");
  
    if (wishlistBadgeDesktop) {
      if (totalItems > 0) {
        wishlistBadgeDesktop.innerText = totalItems;
        wishlistBadgeDesktop.style.display = "inline-block";
      } else {
        wishlistBadgeDesktop.style.display = "none";
      }
    } else {
      // Retry if the badge wasn't found 
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
  
  document.addEventListener("DOMContentLoaded", function () {
    updateWishlistCount();
  });
  

//WishList Btn
document.addEventListener("DOMContentLoaded", function () {
    const wishlistBtn = document.getElementById("wishlistBtn");
  
    wishlistBtn.addEventListener("click", function () {
      const product = {
        // Grab these details dynamically from your product page
        name: document.getElementById("productName").textContent,
        price: parseFloat(document.getElementById("productPrice").innerText.replace("$", "")),
        image: document.getElementById("mainProductImage").getAttribute("src")
      };
  
      // Get existing wishlist or create a new array if none
      let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
  
      // Check if this product already exists in the wishlist
      const existingProduct = wishlist.find(item => item.name === product.name);
      if (!existingProduct) {
        wishlist.push(product);
        localStorage.setItem("wishlist", JSON.stringify(wishlist));
  
       // Update the wishlist count right after adding
      updateWishlistCount();

        //  Show success modal
        const wishlistModal = new bootstrap.Modal(document.getElementById('wishlistModal'));
        const wishlistModalBody = document.getElementById('wishlistModalBody');
        wishlistModalBody.textContent = `${product.name} has been added to your wishlist!`;
        
        wishlistModal.show();
      } else {
        // Show "already in wishlist" modal
        const wishlistModal = new bootstrap.Modal(document.getElementById('wishlistModal'));
        const wishlistModalBody = document.getElementById('wishlistModalBody');
        wishlistModalBody.textContent = `${product.name} is already in your wishlist!`;
        
        wishlistModal.show();
      }
    });
  });
  
  window.addEventListener("storage", function () {
    updateCartCount();
    updateWishlistCount();
  });
  
