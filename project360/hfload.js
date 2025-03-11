fetch("footer.html")
.then(response => response.text())
.then(data => document.getElementById("footer").innerHTML = data);

        function loadHeaderScripts() {
           
            updateCartCount();
            updateWishlistCount();
            const mobileCart = document.querySelector(".d-lg-none .bi-cart");
            const mobileWishlist = document.querySelector(".d-lg-none .bi-heart");
    
            if (mobileCart) {
                mobileCart.addEventListener("click", () => window.location.href = "cart.html");
            }
            if (mobileWishlist) {
                mobileWishlist.addEventListener("click", () => window.location.href = "wishlist.html");
            }
        }