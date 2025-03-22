<?php
require_once "session_handler.php";
require_once "header-loader.php";
require_once "db_connection.php"; // Include your DB connection here


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MV Electronics - Homepage</title>

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

    <?php
$search = '';
$results = [];

if (isset($_GET['query'])) {
    $search = trim($_GET['query']);

    if (!empty($search)) {
        $searchTerm = "%" . $conn->real_escape_string($search) . "%";
        $isNumeric = is_numeric($search);

        if ($isNumeric) {
            // Search by product_id or name/desc
            $stmt = $conn->prepare("SELECT * FROM products 
                WHERE product_id = ? 
                OR name LIKE ? 
                OR description LIKE ? 
                OR category LIKE ?");
            $stmt->bind_param("isss", $search, $searchTerm, $searchTerm, $searchTerm);
        } else {
            // Search by name, desc, category
            $stmt = $conn->prepare("SELECT * FROM products 
                WHERE name LIKE ? 
                OR description LIKE ? 
                OR category LIKE ?");
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        $stmt->close();
    }
}
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4">Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>

    <?php if (empty($results)): ?>
    <p>No products found matching your search.</p>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($results as $product): ?>
            <?php
                // Extract first image
                $firstImage = 'images/default.jpg'; // fallback
                $images = $product['images'];
                $decoded = json_decode($images, true);
                if (is_array($decoded)) {
                    $firstImage = trim($decoded[0]);
                } else if (strpos($images, ',') !== false) {
                    $parts = explode(',', $images);
                    $firstImage = trim($parts[0]);
                } else if (!empty($images)) {
                    $firstImage = $images;
                }

                $link = "product_info.php?id=" . urlencode($product['product_id']);
            ?>
            <div class="col-6 col-md-4 col-lg-2" onclick="location.href='<?php echo $link; ?>'" style="cursor:pointer;">
                <div class="cat-card text-center p-3 h-100 shadow-sm rounded">
                    <img src="<?php echo htmlspecialchars($firstImage); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="img-fluid mb-2" style=" object-fit: contain;">
                    <p class="mb-1 fw-semibold"><?php echo htmlspecialchars($product['name']); ?></p>
                    <p class="text-muted small mb-0">$<?php echo number_format($product['price'], 2); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


</div>

<?php include_once "footer.php"; ?>

<!-- Shared Wishlist and Cart Modals & JS Logic -->
<?php require_once "categories_common_script.php"; ?>

<!-- Custom JS -->

<script src="loginheader.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>