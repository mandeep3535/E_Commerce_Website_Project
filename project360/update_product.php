<?php

header('Content-Type: application/json');

include 'db_connection.php';

$response = array("status" => "error", "message" => "Something went wrong.");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    // Get form data
    $editId = $conn->real_escape_string($_POST['edit_id']);
    $productName = isset($_POST['productName']) ? $conn->real_escape_string($_POST['productName']) : '';
    $productID = isset($_POST['productID']) ? $conn->real_escape_string($_POST['productID']) : '';
    $productPrice = isset($_POST['productPrice']) ? floatval($_POST['productPrice']) : 0;
    $productStock = isset($_POST['productStock']) ? intval($_POST['productStock']) : 0;
    $productCategory = isset($_POST['productCategory']) ? $conn->real_escape_string($_POST['productCategory']) : '';
    $productDescription = isset($_POST['productDescription']) ? $conn->real_escape_string($_POST['productDescription']) : '';

    $errors = [];

    // Validation
    if (empty($productName) || empty($productID) || empty($productPrice) || empty($productStock) || empty($productCategory)) {
        $errors[] = "All fields are required.";
    }

    if ($productPrice <= 0) {
        $errors[] = "Price must be greater than 0.";
    }

    if ($productStock < 0) {
        $errors[] = "Stock cannot be negative.";
    }

    // Handle image uploads if new images are provided
    $uploadedImages = [];
    $imagesString = '';
    
    // First get existing images
    $stmt = $conn->prepare("SELECT images FROM products WHERE product_id = ?");
    $stmt->bind_param("s", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $imagesString = $row['images'];
    }
    $stmt->close();

    // Process new images if any
    if (isset($_FILES['productImages']) && is_array($_FILES['productImages']['name']) && !empty($_FILES['productImages']['name'][0])) {
        $imageCount = count($_FILES['productImages']['name']);
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxImages = min($imageCount, 3);

        for ($i = 0; $i < $maxImages; $i++) {
            if ($_FILES['productImages']['error'][$i] == 0) {
                $fileName = $_FILES['productImages']['name'][$i];
                $fileTmpName = $_FILES['productImages']['tmp_name'][$i];
                $fileType = $_FILES['productImages']['type'][$i];
                $fileSize = $_FILES['productImages']['size'][$i];

                // Validate file type
                if (!in_array($fileType, $allowed_types)) {
                    $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
                    continue;
                }

                // Check file size (Max 2MB)
                if ($fileSize > 2 * 1024 * 1024) {
                    $errors[] = "Each image file size must be less than 2MB.";
                    continue;
                }

                // Ensure uploads directory exists
                $uploadDir = "uploads/";
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate unique filename and save
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = time() . "_" . uniqid() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpName, $uploadPath)) {
                    $uploadedImages[] = $uploadPath; // Store file path
                } else {
                    $errors[] = "Error uploading image: " . $fileName;
                }
            }
        }
        
        // replace existing images with new ones
        if (!empty($uploadedImages)) {
            // Delete old images if needed
            $oldImages = explode(',', $imagesString);
            foreach ($oldImages as $oldImage) {
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
            
            $imagesString = implode(',', $uploadedImages);
        }
    }

    // Check for Errors
    if (!empty($errors)) {
        $response["message"] = implode("<br>", $errors);
        echo json_encode($response);
        exit;
    }

    // Update Product in Database
    $sql = "UPDATE products SET 
            product_id = ?, 
            name = ?, 
            category = ?, 
            description = ?, 
            price = ?, 
            stock = ?";
    
    $params = [$productID, $productName, $productCategory, $productDescription, $productPrice, $productStock];
    $types = "ssssdi";
    
    // Add images to update if we have new ones
    if (!empty($uploadedImages)) {
        $sql .= ", images = ?";
        $params[] = $imagesString;
        $types .= "s";
    }
    
    $sql .= " WHERE product_id = ?";
    $params[] = $editId;
    $types .= "s";
    
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $response["status"] = "success";
            $response["message"] = "Product updated successfully!";
        } else {
            $response["message"] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response["message"] = "Error preparing SQL statement.";
    }
} else {
    $response["message"] = "Invalid request method or missing product ID.";
}

echo json_encode($response);
exit;
?>