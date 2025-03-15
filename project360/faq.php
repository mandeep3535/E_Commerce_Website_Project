<?php
require_once "session_handler.php";
require_once "header-loader.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - MV Electronics</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="faq.css">
</head>
<body>



    <!-- Breadcrumb Navigation -->
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active text-secondary fw-bold" aria-current="page">FAQ</li>
            </ol>
        </nav>
    </div>

    <!-- FAQ Section -->
    <div class="container my-5">
        <h2 class="fw-bold text-center mb-4">Frequently Asked Questions</h2>
        
        <p class="text-muted text-center">Find answers to common questions about our products, orders, shipping, and more.</p>

        <div class="card p-4 shadow-sm">
            <div class="accordion" id="faqAccordion">

                <!-- Question 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            How can I track my order?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Once your order has been shipped, you will receive a tracking number via email. You can also track your order by logging into your account.
                        </div>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            What is your return policy?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We accept returns within 30 days of purchase. Items must be in their original condition and packaging.
                        </div>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Do you offer international shipping?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Currently, we ship within Canada and the United States. We are working to expand our international shipping options soon.
                        </div>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            What payment methods do you accept?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We accept Visa, MasterCard, PayPal, and Apple Pay. All transactions are encrypted for security.
                        </div>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            How do I cancel my order?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            If your order has not yet been shipped, you can cancel it by contacting our support team at <a href="mailto:support@mvelectronics.com" class="text-danger">support@mvelectronics.com</a>.
                        </div>
                    </div>
                </div>

                <!-- Question 6 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                            How long does shipping take?
                        </button>
                    </h2>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Standard shipping takes 5-7 business days, while express shipping takes 2-3 business days. Shipping times may vary based on location.
                        </div>
                    </div>
                </div>

                <!-- Question 7 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                            Do you offer warranties on products?
                        </button>
                    </h2>
                    <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, all our products come with a standard 1-year manufacturer warranty. Some products may offer extended warranty options.
                        </div>
                    </div>
                </div>

                <!-- Question 8 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                            Can I change my shipping address after placing an order?
                        </button>
                    </h2>
                    <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            If your order has not yet been shipped, you can update your shipping address by contacting our customer support.
                        </div>
                    </div>
                </div>

                <!-- Question 9 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                            Do you offer gift cards?
                        </button>
                    </h2>
                    <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, we offer digital gift cards that can be purchased on our website and used for any order.
                        </div>
                    </div>
                </div>

                <!-- Question 10 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                            How do I contact customer support?
                        </button>
                    </h2>
                    <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You can reach our support team via email at <a href="mailto:support@mvelectronics.com" class="text-danger">support@mvelectronics.com</a> or through our <a href="contact.html" class="text-danger">Contact Us</a> page.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php require_once "footer.php"; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="hfload.js"></script>

</body>
</html>
