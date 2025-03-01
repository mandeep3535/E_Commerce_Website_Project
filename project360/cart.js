let couponUsed = false;

// Combined cart update function
function updateCart() {
    const cartRows = document.querySelectorAll(".cart-table tbody tr");
    let originalTotal = 0;

    cartRows.forEach((row) => {
        if (!row.isConnected) return;

        const priceCell = row.querySelector(".cart-price");
        const basePrice = parseFloat(priceCell.dataset.price);
        const quantitySelect = row.querySelector(".cart-quantity");
        const quantity = parseInt(quantitySelect.value);
        const rowSubtotal = basePrice * quantity;

        row.querySelector(".cart-subtotal").textContent = "$" + rowSubtotal;
        originalTotal += rowSubtotal;
    });

    const finalTotal = couponUsed ? originalTotal / 2 : originalTotal;
    const formatTotal = (amount) => `$${amount.toFixed(2)}`;

    // Update displays
    const cartSubtotalEl = document.getElementById("cartSubtotal");
    const cartTotalEl = document.getElementById("cartTotal");
    
    if (couponUsed) {
        const strikeThrough = (amount) => `<span style="text-decoration: line-through; color:red;">$${amount.toFixed(2)}</span>`;
        cartSubtotalEl.innerHTML = `${strikeThrough(originalTotal)} ${formatTotal(finalTotal)}`;
        cartTotalEl.innerHTML = `${strikeThrough(originalTotal)} ${formatTotal(finalTotal)}`;
    } else {
        cartSubtotalEl.textContent = formatTotal(originalTotal);
        cartTotalEl.textContent = formatTotal(originalTotal);
    }
    // Save the subtotal in localStorage for the checkout page
    localStorage.setItem("cartSubtotal", originalTotal);
}

// Combined coupon handling
function applyCoupon() {
    const couponInput = document.getElementById("couponCodeInput");
    const couponMessageEl = document.getElementById("couponMessage");
    const applyCouponBtn = document.getElementById("applyCouponBtn");
    const couponCode = couponInput.value.trim();
   // If coupon is already applied, remove it when button is clicked.
  if (couponUsed) {
    couponUsed = false;
    localStorage.removeItem("appliedCoupon");
    couponInput.value = "";
    couponMessageEl.textContent = "Apply Coupon MV50 for 50% off";
    couponMessageEl.classList.remove("text-success");
    couponMessageEl.classList.add("text-danger");
    applyCouponBtn.textContent = "Apply Coupon";
    updateCart();
    return;
  }
  // Otherwise, try to apply the coupon.
  if (couponCode === "") return; // do nothing if field is empty

    if (couponCode.toUpperCase() === "MV50") {
      couponUsed = true;
      localStorage.setItem("appliedCoupon", couponCode.toUpperCase());
      couponInput.value = couponCode.toUpperCase(); // Pre-fill field with valid coupon
      couponMessageEl.textContent = `Coupon code ${couponCode.toUpperCase()} is applied.`;
      couponMessageEl.classList.remove("text-danger");
      couponMessageEl.classList.add("text-success");
      applyCouponBtn.textContent = "Remove Coupon";
      alert("Coupon MV50 applied! Prices reduced by half.");
    } else {
      couponUsed = false;
      localStorage.removeItem("appliedCoupon");
      couponMessageEl.textContent = "Apply Coupon MV50 for 50% off";
      couponMessageEl.classList.remove("text-success");
      couponMessageEl.classList.add("text-danger");
      applyCouponBtn.textContent = "Apply Coupon";
      alert("Invalid coupon code!");
    }
    updateCart();
}

// Remove coupon if the input is cleared on blur
document.getElementById("couponCodeInput").addEventListener("blur", () => {
  const couponInput = document.getElementById("couponCodeInput");
  const couponMessageEl = document.getElementById("couponMessage");
  if (couponInput.value.trim() === "") {
    couponUsed = false;
    localStorage.removeItem("appliedCoupon");
    couponMessageEl.textContent = "";
    updateCart();
  }
});

// Unified cart count updater
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const totalQuantity = cart.reduce((sum, item) => sum + item.quantity, 0);

    // Get both desktop and mobile badges
    const badges = document.querySelectorAll("#cartCountBadge, #cartCountBadgeMobile");

    // If badges not found, retry after short delay
    if (badges.length === 0) {
        setTimeout(updateCartCount, 100);
        return;
    }

    badges.forEach(badge => {
        if (totalQuantity > 0) {
            badge.textContent = totalQuantity;
            badge.style.display = "inline-block";
        } else {
            badge.style.display = "none";
        }
    });
}

// Consolidated cart loader
function loadCart() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const cartBody = document.getElementById("cartBody");
    cartBody.innerHTML = "";

    if (cart.length === 0) {
        cartBody.innerHTML = `<tr><td colspan="5" class="text-center">Your cart is empty.</td></tr>`;
        updateCartCount();
        return;
    }

    let totalPrice = 0;
    cart.forEach((product, index) => {
        const subtotal = product.price * product.quantity;
        totalPrice += subtotal;

        cartBody.innerHTML += `
            <tr>
                <td class="text-center">
                    <i class="bi bi-x-circle text-danger fs-5 remove-item" role="button" data-index="${index}"></i>
                </td>
                <td class="d-flex align-items-center">
                    <img src="${product.image}" alt="${product.name}" class="cart-img" />
                    <span class="ms-2">${product.name}</span>
                </td>
                <td class="cart-price" data-price="${product.price}">$${product.price}</td>
                <td style="max-width: 80px;">
                    <select class="form-select cart-quantity" data-index="${index}">
                        ${[1, 2, 3].map(q => `<option ${product.quantity === q ? "selected" : ""}>${q}</option>`).join('')}
                        ${product.quantity > 3 ? `<option selected>${product.quantity}</option>` : ''}
                    </select>
                </td>
                <td class="cart-subtotal">$${subtotal.toFixed(2)}</td>
            </tr>`;
    });

    // Event listeners for dynamic elements
    cartBody.querySelectorAll(".remove-item").forEach(button => {
        button.addEventListener("click", removeItem);
    });

    cartBody.querySelectorAll(".cart-quantity").forEach(select => {
        select.addEventListener("change", updateQuantity);
    });

    updateCart();
    updateCartCount(); 
}

// Item management functions
function removeItem(event) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.splice(event.target.dataset.index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
}

function updateQuantity(event) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const index = event.target.dataset.index;
    cart[index].quantity = parseInt(event.target.value);
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
}

// Initial setup
document.addEventListener("DOMContentLoaded", () => {
    // Static event listeners
    document.getElementById("updateCartBtn")?.addEventListener("click", updateCart);
    document.getElementById("applyCouponBtn").addEventListener("click", applyCoupon);
    document.getElementById("buyNowBtn")?.addEventListener("click", addToCart);

    // Initial load
    loadCart();

    // Pre-fill coupon if already applied
    const couponInput = document.getElementById("couponCodeInput");
    const couponMessageEl = document.getElementById("couponMessage");
    const applyCouponBtn = document.getElementById("applyCouponBtn");
    const storedCoupon = localStorage.getItem("appliedCoupon");
    if (storedCoupon && storedCoupon.toUpperCase() === "MV50") {
      couponUsed = true;
      couponInput.value = storedCoupon; // Pre-fill the coupon field
      couponMessageEl.textContent = `Coupon code ${storedCoupon} is applied.`;
      couponMessageEl.classList.remove("text-danger");
      couponMessageEl.classList.add("text-success");
      applyCouponBtn.textContent = "Remove Coupon";
      updateCart(); 
    }
    else {
        couponMessageEl.textContent = "Apply Coupon MV50 for 50% off";
        couponMessageEl.classList.remove("text-success");
  couponMessageEl.classList.add("text-danger");
  applyCouponBtn.textContent = "Apply Coupon";
      }
});

// Simplified add to cart function
function addToCart() {
  const productImage = document.getElementById("mainProductImage");
  const relativeImagePath = productImage.getAttribute('src');
  
  const product = {
    name: document.getElementById("productName").textContent, // Get from HTML element
    price: parseFloat(document.getElementById("productPrice").textContent.replace("$", "")), 
    quantity: parseInt(document.getElementById("quantityInput").value),
    image:  relativeImagePath 
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
}

function updateWishlistCount() {
    // getting the wishlist from localStorage
    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
  
    // The total number of items is just the length
    let totalItems = wishlist.length;
  
    // Get references to the desktop & mobile badges
    const wishlistBadgeDesktop = document.getElementById("wishlistCountBadge");
    const wishlistBadgeMobile = document.getElementById("wishlistCountBadgeMobile");
  
    // Show or hide each badge
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
  
document.addEventListener("DOMContentLoaded", function () {
    updateWishlistCount();
});
  
// Listen for storage changes (in case another tab updates localStorage)
window.addEventListener("storage", function () {
    updateWishlistCount();
});
  
wishlistBtn.addEventListener("click", function () {
    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
  
    // check if product is already in the wishlist ...
    wishlist.push(product);
    localStorage.setItem("wishlist", JSON.stringify(wishlist));
  
    // Update the badge count
    updateWishlistCount();
});
