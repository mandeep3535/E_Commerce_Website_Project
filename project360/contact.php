<?php
require_once 'session_handler.php';
require_once "header-loader.php";

require 'mailer/PHPMailer.php';
require 'mailer/SMTP.php';
require 'mailer/Exception.php';

$showSuccessModal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = htmlspecialchars(trim($_POST["name"]));
    $email   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone   = htmlspecialchars(trim($_POST["phone"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
         // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mvelectronics31@gmail.com'; 
        $mail->Password   = 'dzvbtsppjvyoukhk';    
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Email content
        $mail->setFrom($email, $name); // from user
        $mail->addAddress('mvelectronics31@gmail.com'); // to you
        $mail->addReplyTo($email, $name);

        $mail->Subject = "New Contact Form Submission from $name";
        $mail->Body    = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message";

        $mail->send();
        $showSuccessModal = true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        echo "<div style='color:red;'>Mailer Error: " . $e->getMessage() . "</div>";
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="contact.css">
</head>
<body>

<div class="container mt-5 mb-5">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Contact</li>
    </ol>
  </nav>

  <div class="row mt-4">
    <!-- Contact Info -->
    <div class="col-md-4 lg-4">
      <div class="contact-box p-4 shadow-sm rounded">
        <div class="d-flex align-items-start">
          <i class="bi bi-telephone-fill contact-icon me-3"></i>
          <div>
            <h5 class="fw-bold">Call</h5>
            <p class="text-muted">We are available 24/7, 7 days a week.</p>
            <p class="fw-bold">
              Phone: <a href="tel:+123456789" class="text-dark text-decoration-none">+123456789</a>
            </p>
          </div>
        </div>
        <hr>
        <div class="d-flex align-items-start">
          <i class="bi bi-envelope-fill contact-icon me-3"></i>
          <div>
            <h5 class="fw-bold">Write</h5>
            <p class="text-muted">Fill out our form and we will contact you within 24 hours.</p>
            <p class="fw-bold">
              Email: <a href="mailto:mvelectronics31@gmail.com" class="text-dark text-decoration-none">mvelectronics31@gmail.com</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="col-md-8">
      <div class="contact-form p-4 shadow-sm rounded">
        <form id="contactForm" method="POST" action="">
          <div class="row">
            <div class="col-sm-12 col-md-4 mb-3">
              <input type="text" class="form-control form-input" name="name" placeholder="Your Name *" required>
            </div>
            <div class="col-sm-12 col-md-4 mb-3">
              <input type="email" class="form-control form-input" name="email" placeholder="Your Email *" required>
            </div>
            <div class="col-sm-12 col-md-4 mb-3">
              <input type="tel" class="form-control form-input" name="phone" placeholder="Your Phone *" required>
            </div>
          </div>
          <div class="mb-3">
            <textarea class="form-control form-textarea" name="message" rows="5" placeholder="Your Message" required></textarea>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-danger px-4">Send Message</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Thank you!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Your response has been received. We will contact you shortly! Thank you!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php require_once "footer.php"; ?>
<script src="hfload.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($showSuccessModal): ?>
<script>
  const successModal = new bootstrap.Modal(document.getElementById("successModal"));
  successModal.show();
</script>
<?php endif; ?>

</body>
</html>
