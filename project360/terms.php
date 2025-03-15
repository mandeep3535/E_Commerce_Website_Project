<?php
require_once "session_handler.php";
require_once "header-loader.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Use - MV Electronics</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="terms.css">
</head>
<body>

 

    <!-- Breadcrumb Navigation -->
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active text-secondary fw-bold" aria-current="page">Terms of Use</li>
            </ol>
        </nav>
    </div>

    <!-- Terms of Use Section -->
    <div class="container my-5">
        <h2 class="fw-bold text-center mb-4">Terms of Use</h2>
        
        <p class="text-muted text-center">Effective Date: March 2025</p>

        <div class="card p-4 shadow-sm">
            <h4 class="fw-bold">1. Introduction</h4>
            <p>Welcome to <strong>MV Electronics</strong>. By accessing and using our website, you agree to comply with the following Terms of Use.</p>
            
            <h4 class="fw-bold mt-4">2. User Responsibilities</h4>
            <p>By using our website, you agree to:</p>
            <ul>
                <li>Provide accurate information when making a purchase or registering an account.</li>
                <li>Use our website for lawful purposes only.</li>
                <li>Respect copyright, trademarks, and intellectual property rights.</li>
            </ul>

            <h4 class="fw-bold mt-4">3. Prohibited Activities</h4>
            <p>You are prohibited from:</p>
            <ul>
                <li>Attempting to hack, disrupt, or damage our services.</li>
                <li>Using automated systems (bots, scrapers) to access our content.</li>
                <li>Impersonating another user or providing false information.</li>
            </ul>

            <h4 class="fw-bold mt-4">4. Product Information & Pricing</h4>
            <p>We strive to provide accurate product details and pricing but reserve the right to correct errors and update information at any time.</p>

            <h4 class="fw-bold mt-4">5. Limitation of Liability</h4>
            <p>MV Electronics is not responsible for:</p>
            <ul>
                <li>Any losses or damages resulting from improper use of our products.</li>
                <li>Temporary website downtime due to maintenance or technical issues.</li>
                <li>Third-party services linked on our website.</li>
            </ul>

            <h4 class="fw-bold mt-4">6. Returns & Refunds</h4>
            <p>Customers may return eligible products within 30 days of purchase. Read our <a href="refund.html" class="text-danger">Refund Policy</a> for details.</p>

            <h4 class="fw-bold mt-4">7. Account Termination</h4>
            <p>We reserve the right to terminate accounts found violating these Terms.</p>

            <h4 class="fw-bold mt-4">8. Dispute Resolution</h4>
            <p>Any disputes shall be resolved under applicable Canadian laws. If you have concerns, please contact our support team.</p>

            <h4 class="fw-bold mt-4">9. Changes to Terms</h4>
            <p>We may update these Terms periodically. Continued use of our website means you accept the latest version.</p>

            <h4 class="fw-bold mt-4">10. Contact Us</h4>
            <p>If you have questions, reach out to us at <a href="mailto:support@mvelectronics.com" class="text-danger">support@mvelectronics.com</a>.</p>
        </div>
    </div>

 <?php require_once "footer.php"; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="hfload.js"></script>

</body>
</html>
