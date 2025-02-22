fetch("header.html")
.then(response => response.text())
.then(data => document.getElementById("header").innerHTML = data);
fetch("footer.html")
.then(response => response.text())
.then(data => document.getElementById("footer").innerHTML = data);
fetch("loginheader.html")
        .then(response => response.text())
        .then(data => {
            document.getElementById("loginheader").innerHTML = data;
            loadHeaderScripts(); // ✅ Ensure scripts run after loading
        });
        function loadHeaderScripts() {
            // ✅ Now call wishlist & cart update functions
            updateCartCount();
            updateWishlistCount();
    
            // ✅ Attach event listeners to mobile cart/wishlist
            const mobileCart = document.querySelector(".d-lg-none .bi-cart");
            const mobileWishlist = document.querySelector(".d-lg-none .bi-heart");
    
            if (mobileCart) {
                mobileCart.addEventListener("click", () => window.location.href = "cart.html");
            }
            if (mobileWishlist) {
                mobileWishlist.addEventListener("click", () => window.location.href = "wishlist.html");
            }
        }