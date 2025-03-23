<?php
$token = $_GET['token'] ?? '';
if (!$token) {
    die('Invalid reset link');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - MV Electronics</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <nav class="breadcrumb mb-4">
      <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
      <span class="breadcrumb-item active text-secondary fw-bold">Reset Password</span>
    </nav>
    <div class="row justify-content-center">
      <div class="col-12 col-md-6">
        <div class="card p-4 shadow-sm">
          <h2 class="text-center mb-4">Reset Your Password</h2>
          <form action="reset_password_handler.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="mb-3">
              <label for="new_password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-danger">Reset Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>