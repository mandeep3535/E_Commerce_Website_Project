document.addEventListener("DOMContentLoaded", () => {
  const orderContainer = document.getElementById("orderItems");
  const orderSubtotalElem = document.getElementById("orderSubtotal");
  const orderTotalElem = document.getElementById("orderTotal");
  const shippingElem = document.getElementById("shippingCost");
  const hiddenTotalPrice = document.getElementById("hiddenTotalPrice");
  const hiddenCartItems = document.getElementById("hiddenCartItems");
  const hiddenPaymentMethod = document.getElementById("hiddenPaymentMethod");

  // Get coupon input, button, and message elements
  const couponInput = document.getElementById("couponInput");
  const applyCouponBtn = document.getElementById("applyCoupon");
  const couponMessageEl = document.getElementById("couponMessage");
  const paymentMethodRadios = document.querySelectorAll('.payment-method-radio');

  // Retrieve the cart data from localStorage 
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  // Global flag to indicate if coupon discount has been applied
  let couponApplied = false;

  // Check if a coupon code was previously applied
  const storedCoupon = localStorage.getItem("appliedCoupon");
  if (storedCoupon && storedCoupon.toUpperCase() === "MV50") {
    couponApplied = true;
    // Pre-fill the coupon input with the applied coupon code
    couponInput.value = storedCoupon;
    couponMessageEl.textContent = ` Coupon code ${storedCoupon} is applied.`;
    couponMessageEl.classList.remove("text-danger");
    couponMessageEl.classList.add("text-success");
    applyCouponBtn.textContent = "Remove Coupon";
  } else {
    couponMessageEl.textContent = "Apply Coupon MV50 for 50% off";
    couponMessageEl.classList.remove("text-success");
    couponMessageEl.classList.add("text-danger");
    applyCouponBtn.textContent = "Apply Coupon";
  }

  // Function to render order summary based on provided pricing callback
  const renderOrder = (pricingCallback) => {
    let subtotal = 0;
    let orderItemsHtml = "";
    let processedCart = [];

    if (cart.length === 0) {
      orderItemsHtml = "<p>Your cart is empty.</p>";
    } else {
      cart.forEach((item) => {
        // Calculate discounted price using the callback
        const discountedPrice = pricingCallback(item.price);
        const itemSubtotal = discountedPrice * item.quantity;
        subtotal += itemSubtotal;

        // Add this item to the processed cart with its final price
        processedCart.push({
          id: item.id,
          name: item.name,
          price: discountedPrice,
          quantity: item.quantity,
          image: item.image
        });

        // If coupon applied, show original price struck out and new price
        if (couponApplied) {
          orderItemsHtml += `
            <div class="d-flex align-items-center mb-2">
              <img src="${item.image}" alt="${item.name}" width="40" class="me-2" />
              <div class="flex-grow-1">
                <div>${item.name} (x${item.quantity})</div>
                <div>
                  <span class="text-decoration-line-through text-danger">$${item.price.toFixed(2)}</span>
                  <span class="fw-bold ms-2">$${discountedPrice.toFixed(2)}</span>
                </div>
              </div>
            </div>
          `;
        } else {
          orderItemsHtml += `
            <div class="d-flex align-items-center mb-2">
              <img src="${item.image}" alt="${item.name}" width="40" class="me-2" />
              <div class="flex-grow-1">
                <div>${item.name} (x${item.quantity})</div>
                <div class="fw-bold">$${itemSubtotal.toFixed(2)}</div>
              </div>
            </div>
          `;
        }
      });
    }

    orderContainer.innerHTML = orderItemsHtml;
    orderSubtotalElem.textContent = "$" + subtotal.toFixed(2);

    // Shipping logic: if subtotal is less than $500 and greater than 0, shipping is $15; otherwise, free.
    let shipping = 0;
    if (subtotal > 0 && subtotal < 500) {
      shipping = 15;
    }
    shippingElem.textContent = shipping === 0 ? "Free" : "$" + shipping.toFixed(2);
    
    const totalPrice = subtotal + shipping;
    orderTotalElem.textContent = "$" + totalPrice.toFixed(2);
    
    // Update hidden field with the total price for form submission
    if (hiddenTotalPrice) {
      hiddenTotalPrice.value = totalPrice.toFixed(2);
    }
    
    // Update hidden cart items for form submission
    if (hiddenCartItems) {
      hiddenCartItems.value = JSON.stringify(processedCart);
    }
  };

  // Initial render using discount callback if coupon is applied
  if (couponApplied) {
    renderOrder(price => price / 2);
  } else {
    renderOrder(price => price);
  }

  // Coupon logic: If the coupon code is "MV50", alert, apply discount, and update rendering.
  applyCouponBtn.addEventListener("click", (e) => {
    e.preventDefault();
    // If coupon already applied, remove it.
    if (couponApplied) {
      couponApplied = false;
      localStorage.removeItem("appliedCoupon");
      couponInput.value = "";
      couponMessageEl.textContent = "Apply Coupon MV50 for 50% off";
      couponMessageEl.classList.remove("text-success");
      couponMessageEl.classList.add("text-danger");
      applyCouponBtn.textContent = "Apply Coupon";
      renderOrder(price => price); 
      return;
    }
    
    const couponCode = couponInput.value.trim();
    if (couponCode.toUpperCase() === "MV50") {
      couponApplied = true;
      localStorage.setItem("appliedCoupon", couponCode.toUpperCase());
      couponInput.value = couponCode.toUpperCase();
      couponMessageEl.textContent = `Coupon code ${couponCode.toUpperCase()} is applied.`;
      couponMessageEl.classList.remove("text-danger");
      couponMessageEl.classList.add("text-success");
      applyCouponBtn.textContent = "Remove Coupon";
      alert("Coupon MV50 applied! Prices reduced by half.");
      renderOrder(price => price / 2);
    } else {
      couponApplied = false;
      localStorage.removeItem("appliedCoupon");
      couponMessageEl.textContent = "Apply Coupon MV50 for 50% off";
      couponMessageEl.classList.remove("text-success");
      couponMessageEl.classList.add("text-danger");
      applyCouponBtn.textContent = "Apply Coupon";
      alert("Invalid coupon code");
      renderOrder(price => price);
    }
  });
  
  // Handle payment method selection
  paymentMethodRadios.forEach(radio => {
    radio.addEventListener('change', function() {
      hiddenPaymentMethod.value = this.value;
    });
  });
  
  // Form submission validation
  document.getElementById("deliveryForm").addEventListener("submit", function(e) {
    // Check if cart is empty
    if (cart.length === 0) {
      e.preventDefault();
      alert("Your cart is empty. Please add items before placing an order.");
      return;
    }
    
    // Validate that all required fields are filled
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
  console.log("Original cart:", cart);
console.log("Processed cart for submission:", processedCart);
console.log("JSON to be submitted:", JSON.stringify(processedCart));
});