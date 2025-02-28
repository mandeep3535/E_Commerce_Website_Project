document.addEventListener("DOMContentLoaded", () => {
    const orderContainer = document.getElementById("orderItems");
    const orderSubtotalElem = document.getElementById("orderSubtotal");
    const orderTotalElem = document.getElementById("orderTotal");
    const shippingElem = document.getElementById("shippingCost");

    // Get coupon input and button elements
    const couponInput = document.getElementById("couponInput");
    const applyCouponBtn = document.getElementById("applyCoupon");

    // Retrieve the cart data from localStorage (or use an empty array)
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    // Global flag to indicate if coupon discount has been applied
    let couponApplied = false;

    // Function to render order summary based on provided pricing callback
    const renderOrder = (pricingCallback) => {
      let subtotal = 0;
      let orderItemsHtml = "";

      if (cart.length === 0) {
        orderItemsHtml = "<p>Your cart is empty.</p>";
      } else {
        cart.forEach((item) => {
          // Calculate discounted price using the callback
          const discountedPrice = pricingCallback(item.price);
          const itemSubtotal = discountedPrice * item.quantity;
          subtotal += itemSubtotal;

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
      orderTotalElem.textContent = "$" + (subtotal + shipping).toFixed(2);
    };

    // Initial render without coupon discount
    renderOrder(price => price);

    // Coupon logic: If the coupon code is "MV50", alert, apply discount, and update rendering.
    applyCouponBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if (couponApplied) {
        alert("Coupon already applied.");
        return;
      }
      const couponCode = couponInput.value.trim();
      if (couponCode.toUpperCase() === "MV50") {
        alert("Coupon MV50 applied! Prices reduced by half.");
        couponApplied = true;
        // Re-render order with prices reduced by half
        renderOrder(price => price / 2);
      } else {
        alert("Invalid coupon code");
      }
    });
  });