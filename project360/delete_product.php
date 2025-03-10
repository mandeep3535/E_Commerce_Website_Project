<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

$response = array("status" => "error", "message" => "Failed to delete product.");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $conn->real_escape_string($_POST['product_id']);
    
    // Get image paths before deleting to remove files
    $stmt = $conn->prepare("SELECT images FROM products WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Delete the product from database
        $delete_stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $delete_stmt->bind_param("s", $product_id);
        
        if ($delete_stmt->execute()) {
            // Delete image files
            $imagePaths = explode(',', $row['images']);
            foreach ($imagePaths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            $response["status"] = "success";
            $response["message"] = "Product deleted successfully!";
        }
        
        $delete_stmt->close();
    }
    
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>