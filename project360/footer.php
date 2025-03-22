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
                <a href="home.php"><img class="figure-img img-fluid rounded" src="images/Logo.png" alt="website logo"></a>
               </figure>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3 text-center">
                <h5>Support</h5>
                <p>3333 University Way, Kelowna, BC</p>
                <p id="email"><a href="mailto:mvelectronics31@gmail.com">Email: mvelectronics31@gmail.com</a></p>
                <p id="phone">
                    <a href="tel:+12345678902">Phone: +1-234-567-8902</a>
                </p>
            </div>

            <div class="col-md-3 col-sm-6 mb-3 text-center">
                <h5>Account</h5>
                <ul class="list-unstyled">
    <li>
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="logout.php" class="text-white">Logout</a>
        <?php else: ?>
            <a href="login.php" class="text-white">Login / Register</a>
        <?php endif; ?>
    </li>
    <?php if (!isset($_SESSION["user_id"])): ?>
        <li><a href="Admin_login.html" class="text-white">Admin Login</a></li>
    <?php endif; ?>
    <li><a href="home.php" class="text-white">Shop</a></li>
</ul>

            </div>
            <div class="col-md-3 col-sm-6 mb-3 text-center">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="privacy.php" class="text-white">Privacy Policy</a></li>
                    <li><a href="terms.php" class="text-white">Terms of Use</a></li>
                    <li><a href="faq.php" class="text-white">FAQ</a></li>
                    <li><a href="contact.php" class="text-white">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>