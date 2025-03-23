<?php
require_once 'session_handler.php';
// Now you can use $_SESSION variables on this page.
require_once "header-loader.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="about.css">
</head>
<body>
   
<div class="container mt-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">About</li>
        </ol>
    </nav>

    <!-- Our Story Section -->
    <div class="row align-items-center my-5">
        <div class="col-lg-6">
            <h2 class="fw-bold">Our Story</h2>
            <p class="text-muted">
                Launched in 2025, MV Electronics is premier online shopping marketplace 
                with an active presence in Canada, supported by a wide range of tailored marketing, 
                brand and service solutions. MV Electronics has 10,500 sellers and 300 brands and serves 5 
                million customers across the region.
            </p>
            <p class="text-muted">
                MV Electronics has more than 1 million products to offer, growing at a very fast rate. 
                MV Electronics  aims to enhance customer experiences across categories ranging from 
                consumers.
            </p>
        </div>
        <div class="col-lg-6 text-center">
            <img src="images/shopping.avif" class="img-fluid rounded" alt="Shopping Image">
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="row text-center my-5">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <i class="bi bi-bag fs-1"></i>
                <h4 class="fw-bold mt-2">10.5k</h4>
                <p class="text-muted">Sellers active on site</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card active-stat">
                <i class="bi bi-cash-coin fs-1"></i>
                <h4 class="fw-bold mt-2">33k</h4>
                <p class="text-muted">Monthly Product Sale</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <i class="bi bi-people fs-1"></i>
                <h4 class="fw-bold mt-2">45.5k</h4>
                <p class="text-muted">Customers active on site</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <i class="bi bi-graph-up fs-1"></i>
                <h4 class="fw-bold mt-2">25k</h4>
                <p class="text-muted">Annual gross sale</p>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="row text-center my-5">
        <div class="col-md-4">
            <div class="team-card">
                <img src="images/mandeep.jpg" class="img-fluid rounded-circle mb-3" alt="Mandeep">
                <h5 class="fw-bold">Mandeep Singh</h5>
                <p class="text-muted">Founder & Chairman</p>
                <div class="social-icons">
                    <i class="bi bi-instagram"></i>
                    <i class="bi bi-twitter-x"></i>
                    <a href="https://www.linkedin.com/in/mandeep-singh-3ab425228" target="_blank"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="team-card">
                <img src="images/Varun.jpg" class="img-fluid rounded-circle mb-3" alt="Varun">
                <h5 class="fw-bold">Varun Garg</h5>
                <p class="text-muted">Founder & Chairman</p>
                <div class="social-icons">
                    <i class="bi bi-instagram"></i>
                    <i class="bi bi-twitter-x"></i>
                    <a href = "https://www.linkedin.com/in/varun-garg-762289192" target = "_blank"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="team-card">
                <img src="images/ubc.png" class="img-fluid rounded-circle mb-3" alt="UBC">
                <h5 class="fw-bold">UBC</h5>
                <p class="text-muted">Partner</p>
                <div class="social-icons">
                    <a href="https://www.instagram.com/ubcokanagan/?hl=en" target="_blank"> <i class="bi bi-instagram"></i></a>
                    <a href="https://x.com/ubcokanagan?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor" target="_blank"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://www.linkedin.com/company/ubcokanagan/?originalSubdomain=ca" target="_blank"><i class="bi bi-linkedin"></i></a>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row text-center my-5">
        <div class="col-md-4">
            <i class="bi bi-truck fs-1"></i>
            <h6 class="fw-bold mt-2">FREE AND FAST DELIVERY</h6>
            <p class="text-muted">Free delivery for all orders over $500</p>
        </div>
        <div class="col-md-4">
            <i class="bi bi-headset fs-1"></i>
            <h6 class="fw-bold mt-2">24/7 CUSTOMER SERVICE</h6>
            <p class="text-muted">Friendly 24/7 customer support</p>
        </div>
        <div class="col-md-4">
            <i class="bi bi-shield-check fs-1"></i>
            <h6 class="fw-bold mt-2">MONEY BACK GUARANTEE</h6>
            <p class="text-muted">We return money within 30 days</p>
        </div>
    </div>
</div>



<?php
require_once "footer.php";
?>

<script src = "hfload.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
