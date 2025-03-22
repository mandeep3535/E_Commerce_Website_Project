document.addEventListener("DOMContentLoaded", () => {
  // Modal helper (assumes a Bootstrap modal with id "feedbackModal")
  function showModal(title, message) {
    const modalElement = document.getElementById("feedbackModal");
    if (!modalElement) return;
    modalElement.querySelector(".modal-title").textContent = title;
    modalElement.querySelector(".modal-body").textContent = message;
    let modal = new bootstrap.Modal(modalElement);
    modal.show();
  }

  const orderContainer = document.getElementById("orderItems");
  const orderSubtotalElem = document.getElementById("orderSubtotal");
  const orderTotalElem = document.getElementById("orderTotal");
  const shippingElem = document.getElementById("shippingCost");
  const hiddenTotalPrice = document.getElementById("hiddenTotalPrice");
  const hiddenCartItems = document.getElementById("hiddenCartItems");
  const hiddenPaymentMethod = document.getElementById("hiddenPaymentMethod");

  // Coupon elements
  const couponInput = document.getElementById("couponInput");
  const applyCouponBtn = document.getElementById("applyCoupon");
  const couponMessageEl = document.getElementById("couponMessage");
  const paymentMethodRadios = document.querySelectorAll('.payment-method-radio');

  let couponApplied = false; // determined by the server
  let cart = []; // will be filled by the server fetch

  // Initialize coupon UI state
  couponApplied = false;
  couponInput.value = "";
  couponMessageEl.textContent = "Apply Coupon MV50 for 50% off";
  couponMessageEl.classList.remove("text-success");
  couponMessageEl.classList.add("text-danger");
  applyCouponBtn.textContent = "Apply Coupon";

  // Function to render the order summary.
  // When a coupon is active, item.price is the discounted price.
  // We want to display that price in red.


  function extractFirstImage(images) {
    try {
      const parsed = JSON.parse(images);
      if (Array.isArray(parsed)) {
        return parsed[0];
      }
    } catch (e) {
      if (images.includes(",")) {
        return images.split(",")[0].trim();
      }
    }
    return images; // fallback
  }

  
  const renderOrder = () => {
    let subtotal = 0;
    let orderItemsHtml = "";
    let processedCart = [];

    if (cart.length === 0) {
      orderItemsHtml = "<p>Your cart is empty.</p>";
    } else {
      cart.forEach((item) => {
        // Use final price (server already applied discount if coupon is active)
        const lineSubtotal = item.price * item.quantity;
        subtotal += lineSubtotal;

        // Prepare cart item for form submission
        processedCart.push({
          id: item.id,
          name: item.name,
          price: item.price,
          quantity: item.quantity,
          image: item.image
        });

        // Decide styling: if couponApplied, show price in red; otherwise, normal styling.
        const priceStyle = couponApplied ? "color: red; font-weight: bold;" : "font-weight: bold;";
        
        orderItemsHtml += `
          <div class="d-flex align-items-center mb-2">
            <img src="${extractFirstImage(item.image)}" alt="${item.name}" width="40" class="me-2" />

            <div class="flex-grow-1">
              <div>${item.name} (x${item.quantity})</div>
              <div style="${priceStyle}">$${lineSubtotal.toFixed(2)}</div>
            </div>
          </div>
        `;
      });
    }

    orderContainer.innerHTML = orderItemsHtml;
    orderSubtotalElem.textContent = "$" + subtotal.toFixed(2);

    // Shipping: if subtotal > 0 but less than $500, add $15; otherwise free.
    let shipping = 0;
    if (subtotal > 0 && subtotal < 500) {
      shipping = 15;
    }
    shippingElem.textContent = shipping === 0 ? "Free" : "$" + shipping.toFixed(2);

    const totalPrice = subtotal + shipping;
    orderTotalElem.textContent = "$" + totalPrice.toFixed(2);

    if (hiddenTotalPrice) hiddenTotalPrice.value = totalPrice.toFixed(2);
    if (hiddenCartItems) hiddenCartItems.value = JSON.stringify(processedCart);

    console.log("Processed cart for submission:", processedCart);
  };

  // Fetch cart data from the server (including coupon status)
  fetch('get_cart_items.php')
    .then(response => response.json())
    .then(data => {
      // Expected structure: { cart: [ {id, name, price, quantity, image}, ... ], couponApplied: <boolean> }
      cart = data.cart || [];
      couponApplied = data.couponApplied || false;
      if (couponApplied) {
        couponInput.value = "MV50";
        couponMessageEl.textContent = "Coupon code MV50 is applied.";
        couponMessageEl.classList.remove("text-danger");
        couponMessageEl.classList.add("text-success");
        applyCouponBtn.textContent = "Remove Coupon";
      }
      renderOrder();
    })
    .catch(err => {
      console.error("Error fetching cart items from server:", err);
      cart = [];
      renderOrder();
    });

  // Coupon logic: apply or remove coupon via server
  applyCouponBtn.addEventListener("click", (e) => {
    e.preventDefault();
    const couponCode = couponInput.value.trim();

    if (couponApplied) {
      // Remove coupon: send an empty coupon
      fetch('apply_coupon.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'coupon='
      })
        .then(response => response.json())
        .then(data => {
          couponApplied = false;
          couponMessageEl.textContent = "Apply Coupon MV50 for 50% off";
          couponMessageEl.classList.remove("text-success");
          couponMessageEl.classList.add("text-danger");
          applyCouponBtn.textContent = "Apply Coupon";
          couponInput.value = "";
          showModal("Coupon Removed", "Coupon has been removed. Prices updated to normal.");
          return fetch('get_cart_items.php');
        })
        .then(resp => resp.json())
        .then(updatedData => {
          cart = updatedData.cart || [];
          couponApplied = updatedData.couponApplied || false;
          renderOrder();
        })
        .catch(err => {
          console.error("Error removing coupon:", err);
        });
    } else {
      // Apply coupon
      fetch('apply_coupon.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'coupon=' + encodeURIComponent(couponCode)
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            couponApplied = true;
            couponMessageEl.textContent = `Coupon code ${data.coupon} is applied.`;
            couponMessageEl.classList.remove("text-danger");
            couponMessageEl.classList.add("text-success");
            applyCouponBtn.textContent = "Remove Coupon";
            showModal("Coupon Applied", "Coupon MV50 applied! Prices reduced.");
          } else {
            couponApplied = false;
            couponMessageEl.textContent = data.message;
            couponMessageEl.classList.remove("text-success");
            couponMessageEl.classList.add("text-danger");
            applyCouponBtn.textContent = "Apply Coupon";
          }
          return fetch('get_cart_items.php');
        })
        .then(resp => resp.json())
        .then(updatedData => {
          cart = updatedData.cart || [];
          couponApplied = updatedData.couponApplied || false;
          renderOrder();
        })
        .catch(err => {
          console.error("Error applying coupon:", err);
        });
    }
  });

  // Update hidden payment method when selection changes
  paymentMethodRadios.forEach(radio => {
    radio.addEventListener('change', function() {
      hiddenPaymentMethod.value = this.value;
    });
  });

  // Validate form submission
  document.getElementById("deliveryForm").addEventListener("submit", function(e) {
    if (cart.length === 0) {
      e.preventDefault();
      alert("Your cart is empty. Please add items before placing an order.");
      return;
    }
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        isValid = false;
        field.classList.add('is-invalid');
      } else {
        field.classList.remove('is-invalid');
      }
    });
    if (!isValid) {
      e.preventDefault();
      alert("Please fill in all required fields.");
    }
  });
});
