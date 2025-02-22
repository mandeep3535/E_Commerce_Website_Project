fetch("header.html")
.then(response => response.text())
.then(data => document.getElementById("header").innerHTML = data);
fetch("footer.html")
.then(response => response.text())
.then(data => document.getElementById("footer").innerHTML = data);
fetch("loginheader.html")
  .then(response => response.text())
  .then(html => {
    document.getElementById("loginheader").innerHTML = html;
    // Now that the navbar is inserted, call the badge updates
    updateCartCount();
    updateWishlistCount();
  });