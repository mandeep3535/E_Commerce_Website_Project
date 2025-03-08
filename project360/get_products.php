<?php
// Include database connection
require_once 'db_connection.php';

// Query to get all products
$sql = "SELECT * FROM products ORDER BY product_id DESC";
$result = $conn->query($sql);

$products = [];

if ($result->num_rows > 0) {
    // Fetch all products
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($products);

// Close connection
$conn->close();
?>