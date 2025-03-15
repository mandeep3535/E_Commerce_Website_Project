<?php
// get_cart_items.php
require_once "session_handler.php";
require_once "db_connection.php";

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(["error" => "Not authorized"]);
  exit;
}

$user_id = (int) $_SESSION['user_id'];
$sql = "SELECT c.product_id AS id, c.quantity, p.name, p.price, p.images as image 
        FROM cart c 
        INNER JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = [];

// Determine coupon status from session
$couponApplied = isset($_SESSION['coupon']) && $_SESSION['coupon'] === 'MV50';

while ($row = $result->fetch_assoc()) {
    if ($couponApplied) {
        // Apply discount: divide the price by 2
        $row['price'] = $row['price'] / 2;
    }
    $cartItems[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode([
  "cart" => $cartItems,
  "couponApplied" => $couponApplied
]);
?>
