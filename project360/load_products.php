<?php
// Include your existing database connection
include_once 'db_connection.php';

// Get all products
$sql = "SELECT * FROM Products ORDER BY product_id DESC";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Convert JSON images back to array
        $row['images'] = json_decode($row['images'], true);
        $products[] = $row;
    }
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($products);
$conn->close();
?>