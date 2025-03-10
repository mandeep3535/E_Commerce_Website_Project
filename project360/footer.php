<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-3 px-5 p-md-5">
               <figure>
                <a href="#"><img class="figure-img img-fluid rounded" src="images/Logo.png" alt="website logo"></a>
               </figure>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3 text-center">
                <h5>Support</h5>
                <p>3333 University Way, Kelowna, BC</p>
                <p id="email"><a href="mailto:official.mvelectronics@gmail.com">Email: official.mvelectronics@gmail.com</a></p>
                <p id="phone">
                    <a href="tel:+12345678902">Phone: +1-234-567-8902</a>
                </p>
            </div>

            <div class="col-md-3 col-sm-6 mb-3 text-center">
                <h5>Account</h5>
                <ul class="list-unstyled">
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <li><a href="dashboard.php" class="text-white">My Account</a></li>
                        <li><a href="logout.php" class="text-white">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="text-white">Login / Register</a></li>
                    <?php endif; ?>
                    <li><a href="Admin_login.html" class="text-white">Admin Login</a></li>
                    <li><a href="home.html" class="text-white">Shop</a></li>
                </ul>
            </div>
            <div class="col-md-3 col-sm-6 mb-3 text-center">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="privacy.html" class="text-white">Privacy Policy</a></li>
                    <li><a href="terms.html" class="text-white">Terms of Use</a></li>
                    <li><a href="faq.html" class="text-white">FAQ</a></li>
                    <li><a href="contact.html" class="text-white">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>