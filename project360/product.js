// product.js
document.addEventListener("DOMContentLoaded", function () {
    // Capture the main image element and store its default source.
    const mainImageElement = document.getElementById("mainProductImage");
    const defaultImageSrc = mainImageElement.getAttribute("src");
  
    // Single changeMainImage function.
    window.changeMainImage = function (imageUrl, element) {
      mainImageElement.src = imageUrl;
      // Remove "active" class from all thumbnail cards.
      document.querySelectorAll('.thumb-card').forEach(card => card.classList.remove('active'));
      // Add "active" class to the clicked thumbnail.
      element.classList.add('active');
    };
  
    // Quantity and Price Update Section.
    const quantityInput = document.getElementById("quantityInput");
    const productPriceElement = document.getElementById("productPrice");
  
    // Parse the base price from the productPrice element.
    const basePrice = parseFloat(productPriceElement.textContent.replace(/[$,]/g, "").trim());

    function updatePrice() {
      let quantity = parseInt(quantityInput.value);
      if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
        quantityInput.value = 1;
      }
      const totalPrice = (basePrice * quantity).toFixed(2);
      productPriceElement.innerText = `$${Number(totalPrice).toLocaleString()}`;
    }
    
  
    // Increase quantity button.
    document.getElementById("increaseQty").addEventListener("click", function () {
      quantityInput.value = parseInt(quantityInput.value) + 1;
      updatePrice();
    });
  
    // Decrease quantity button.
    document.getElementById("decreaseQty").addEventListener("click", function () {
      if (parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
        updatePrice();
      }
    });
  
    // Update price on manual input change.
    quantityInput.addEventListener("input", updatePrice);
  
    // Initialize price on page load.
    updatePrice();
  });
  