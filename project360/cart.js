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
        const strikeThrough = (amount) => `<span style="text-decoration: line-through; color:red;">$$${amount.toFixed(2)}</span>`;
        cartSubtotalEl.innerHTML = `${strikeThrough(originalTotal)} ${formatTotal(finalTotal)}`;
        cartTotalEl.innerHTML = `${strikeThrough(originalTotal)} ${formatTotal(finalTotal)}`;
    } else {
        cartSubtotalEl.textContent = formatTotal(originalTotal);
        cartTotalEl.textContent = formatTotal(originalTotal);
    }
}

// Combined coupon handling
function applyCoupon() {
    const couponInput = document.getElementById("couponCodeInput").value.trim().toUpperCase();
    if (couponInput === "MONHALF") {
        couponUsed = true;
        alert("Coupon MONHALF applied! Prices reduced by half.");
    } else {
        alert("Invalid coupon code!");
    }
    updateCart();
}

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
});

// Simplified add to cart function
function addToCart() {
  const productImage = document.getElementById("mainProductImage");
  const relativeImagePath = productImage.getAttribute('src');
  
  const product = {
    name: document.getElementById("productName").textContent, // Get from HTML element
    price: parseFloat(document.getElementById("productPrice").textContent.replace("$", "")), 
    quantity: parseInt(document.getElementById("quantityInput").value),
    image:  relativeImagePath // Updated ID
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