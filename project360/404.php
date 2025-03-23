<?php
require_once "session_handler.php";
require_once "header-loader.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>404 - Page Not Found | MV Electronics</title>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="home.css" />
  <link rel="stylesheet" href="header.css" />
  <link rel="stylesheet" href="footer.css" />
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
</head>
<body>

<!-- MAIN CONTENT -->
<div class="container text-center my-5">
  <h1 class="display-4 text-danger fw-bold">404</h1>
  <p class="lead">Oops! The page you're looking for doesn't exist.</p>
  <p>It might have been moved, deleted, or never existed.</p>
  <a href="index.php?page=home" class="btn btn-danger mt-3">
    <i class="bi bi-arrow-left"></i> Back to Homepage
  </a>

  <!-- Optional image or icon -->
  
</div>

<?php require_once "footer.php"; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="loginheader.js"></script>

</body>
</html>
