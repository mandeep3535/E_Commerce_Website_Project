<?php
// Include your database connection
include_once 'db_connection.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if request is POST and product_id is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    
    // Get product images before deleting
    $sql = "SELECT images FROM Products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $images = json_decode($row['images'], true);
        
        // Delete product from database
        $sql = "DELETE FROM Products WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $productId);
        
        if ($stmt->execute()) {
            // Delete associated image files
            if (!empty($images) && is_array($images)) {
                foreach ($images as $image) {
                    $imagePath = "uploads/products/" . $image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }
            
            echo json_encode(["success" => true, "message" => "Product deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete product: " . $stmt->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Product not found"]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
?>