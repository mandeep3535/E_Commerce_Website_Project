// Instantiate the modal 
const feedbackModalElement = document.getElementById('feedbackModal');
const feedbackModal = new bootstrap.Modal(feedbackModalElement);

// -----------------------------------------------------
// Helper function to display a Bootstrap modal with a title and message
// -----------------------------------------------------
function showModal(title, message) {
    feedbackModalElement.querySelector('.modal-title').textContent = title;
    feedbackModalElement.querySelector('.modal-body').textContent = message;
    feedbackModal.show();
}

// -----------------------------------------------------
// Update Cart Totals Based on the Rendered Table Rows
// -----------------------------------------------------
function updateCart() {
    const cartRows = document.querySelectorAll(".cart-table tbody tr");
    let originalTotal = 0;
    
    cartRows.forEach((row) => {
        if (!row.isConnected) return;
        const priceCell = row.querySelector(".cart-price, .product-price");
        const basePrice = parseFloat(priceCell.dataset.price);
        const quantitySelect = row.querySelector(".quantity-select");
        const quantity = parseInt(quantitySelect.value);
        const rowSubtotal = basePrice * quantity;
        row.querySelector(".subtotal").textContent = "$" + rowSubtotal.toFixed(2);
        originalTotal += rowSubtotal;
    });

    const formatTotal = (amount) => `$${amount.toFixed(2)}`;

    const cartSubtotalEl = document.getElementById("cartSubtotal");
    const cartTotalEl = document.getElementById("cartTotal");
    
    cartSubtotalEl.textContent = formatTotal(originalTotal);
    cartTotalEl.textContent = formatTotal(originalTotal);
  
}

// -----------------------------------------------------
// Update Cart Count 
// -----------------------------------------------------
function updateCartCount() {
    let totalQuantity = 0;
    const rows = document.querySelectorAll(".cart-table tbody tr");
    rows.forEach(row => {
        const qtySelect = row.querySelector(".quantity-select");
        if (qtySelect) {
            totalQuantity += parseInt(qtySelect.value);
        }
    });

    const badges = document.querySelectorAll("#cartCountBadge, #cartCountBadgeMobile");
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

// -----------------------------------------------------
// Load Cart
// -----------------------------------------------------
function loadCart() {
    updateCart();
    updateCartCount();
    bindQuantityListeners(); // re-bind after loading in case elements were re-rendered
}

// -----------------------------------------------------
// Remove an Item from the Cart
// -----------------------------------------------------
function removeItem(event) {
    const row = event.target.closest("tr");
    if (!row) return;
    const productId = row.getAttribute("data-product-id");
    if (!productId) return;

    fetch(`cart.php?action=remove&product_id=${productId}`)
        .then(response => response.text())
        .then(data => {
            console.log("Server remove response:", data);
            showModal("Product Removed", "The item has been removed from your cart.");
            row.remove();
            updateCart();
            updateCartCount();
        })
        .catch(err => {
            console.error("Error removing cart item:", err);
            showModal("Error", "There was an error removing the cart item. Please try again.");
        });
}

// -----------------------------------------------------
// Update Quantity: Send an AJAX request to update DB
// -----------------------------------------------------
function updateQuantity(productId, quantity) {
    fetch(`cart.php?action=update&product_id=${productId}&quantity=${quantity}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            console.log("Update response:", data);
            showModal("Quantity Updated", "Your cart has been updated.");
            updateCart();
            updateCartCount();
        })
        .catch(error => {
            console.error('There was an error updating the quantity:', error);
            showModal("Error", "There was an error updating the quantity. Please try again.");
        });
}

// -----------------------------------------------------
// Dedicated handler for quantity change events
// -----------------------------------------------------
function quantityChangeHandler(event) {
    const selectEl = event.target;
    const productId = selectEl.getAttribute("data-product-id");
    const newQuantity = selectEl.value;
    updateQuantity(productId, newQuantity);
}

// -----------------------------------------------------
// Bind quantity change event listeners
// -----------------------------------------------------
function bindQuantityListeners() {
    document.querySelectorAll(".cart-table .quantity-select").forEach(select => {
        select.removeEventListener("change", quantityChangeHandler);
        select.addEventListener("change", quantityChangeHandler);
    });
}

// -----------------------------------------------------
// Initial Setup: Bind event listeners and load cart
// -----------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".cart-table .remove-item").forEach(button => {
        button.addEventListener("click", removeItem);
    });

    // Use our dedicated handler for quantity changes
    bindQuantityListeners();

    loadCart();
});

// -----------------------------------------------------
//  Add to Cart function if needed elsewhere
// -----------------------------------------------------
function addToCart() {
    const productImage = document.getElementById("mainProductImage");
    const relativeImagePath = productImage.getAttribute('src');
    // Retrieve product id from a DOM element
    const productId = document.getElementById("productID").value;
  
    const product = {
        id: productId,
        name: document.getElementById("productName").textContent,
        price: parseFloat(document.getElementById("productPrice").textContent.replace("$", "")), 
        quantity: parseInt(document.getElementById("quantityInput").value),
        image: relativeImagePath 
    };

    fetch(`cart.php?product_id=${encodeURIComponent(product.id)}`)
        .then(response => response.text())
        .then(data => {
            console.log("Add to cart response:", data);
            updateCartCount();
        })
        .catch(err => console.error("Error adding to cart:", err));
}

/*
function updateWishlistCount() {
    let wishlist = []; // localStorage handling removed
    let totalItems = wishlist.length;
  
    const wishlistBadgeDesktop = document.getElementById("wishlistCountBadge");
    const wishlistBadgeMobile = document.getElementById("wishlistCountBadgeMobile");
  
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
  
window.addEventListener("storage", function () {
    updateWishlistCount();
});
  
if (typeof wishlistBtn !== "undefined") {
    wishlistBtn.addEventListener("click", function () {
        let wishlist = []; // localStorage handling removed
        wishlist.push(product);
        updateWishlistCount();
    });
}
*/