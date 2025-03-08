<?php
// Include database connection
require_once 'db_connection.php';

header('Content-Type: application/json');
$response = array("status" => "error", "message" => "Product not found");

if (isset($_GET['id'])) {
    $product_id = $conn->real_escape_string($_GET['id']);
    
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
        exit;
    }
    
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>