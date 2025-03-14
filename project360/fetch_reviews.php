<?php
require_once 'db_connection.php';
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$reviews = array();
if($product_id > 0){
    $sql = "SELECT r.*, u.user_name, u.first_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.user_id 
            WHERE r.product_id = ? 
            ORDER BY r.created_at DESC
            LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $reviews[] = $row;
    }
    $stmt->close();
}
$conn->close();
header('Content-Type: application/json');
echo json_encode($reviews);
?>
