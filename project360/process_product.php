<?php
header('Content-Type: application/json'); // Ensure JSON response

include 'db_connection.php';

$response = array("status" => "error", "message" => "Something went wrong.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $productName = isset($_POST['productName']) ? $conn->real_escape_string($_POST['productName']) : '';
    $productID = isset($_POST['productID']) ? $conn->real_escape_string($_POST['productID']) : '';
    $productPrice = isset($_POST['productPrice']) ? floatval($_POST['productPrice']) : 0;
    $productStock = isset($_POST['productStock']) ? intval($_POST['productStock']) : 0;
    $productCategory = isset($_POST['productCategory']) ? $conn->real_escape_string($_POST['productCategory']) : '';
    $productDescription = isset($_POST['productDescription']) ? $conn->real_escape_string($_POST['productDescription']) : '';

    $errors = [];
    $uploadedImages = [];

    // **Validation**
    if (empty($productName) || empty($productID) || empty($productPrice) || empty($productStock) || empty($productCategory)) {
        $errors[] = "All fields are required.";
    }

    if ($productPrice <= 0) {
        $errors[] = "Price must be greater than 0.";
    }

    if ($productStock < 0) {
        $errors[] = "Stock cannot be negative.";
    }

    // **Image Upload Validation**
    if (isset($_FILES['productImages']) && is_array($_FILES['productImages']['name']) && !empty($_FILES['productImages']['name'][0])) {  
        // Count how many images were uploaded
        $imageCount = count($_FILES['productImages']['name']);
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxImages = min($imageCount, 3); // We'll only process up to 3

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
    } else {
        $errors[] = "Please upload at least one product image.";
    }

    // Check for Errors
    if (!empty($errors)) {
        $response["message"] = implode("<br>", $errors);
        echo json_encode($response);
        exit;
    }

    // 1) Check if product already exists in DB
    $checkSql = "SELECT images FROM products WHERE product_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $productID);
    $checkStmt->execute();
    $resultCheck = $checkStmt->get_result();

    if ($resultCheck->num_rows > 0) {
        // === Product exists => Merge newly uploaded images with existing ones ===
        $rowCheck = $resultCheck->fetch_assoc();
        $existingImagesStr = $rowCheck['images']; // e.g. "uploads/img1.png,uploads/img2.png"
        
        // Convert existing CSV to array
        $existingImages = array_filter(explode(',', $existingImagesStr));
        
        // Merge
        $allImages = array_merge($existingImages, $uploadedImages);

        // Limit to max 3 total
        $allImages = array_slice($allImages, 0, 3);

        // Convert back to CSV
        $imagesString = implode(',', $allImages);

        // Update the existing product
        $sqlUpdate = "UPDATE products
                      SET name = ?, category = ?, description = ?, images = ?, price = ?, stock = ?
                      WHERE product_id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        if ($stmtUpdate) {
            $stmtUpdate->bind_param("ssssdis",
                $productName,
                $productCategory,
                $productDescription,
                $imagesString,
                $productPrice,
                $productStock,
                $productID
            );
            if ($stmtUpdate->execute()) {
                $response["status"] = "success";
                $response["message"] = "Product updated successfully (images merged)!";
            } else {
                $response["message"] = "Database error (update): " . $stmtUpdate->error;
            }
            $stmtUpdate->close();
        } else {
            $response["message"] = "Error preparing UPDATE statement.";
        }
    } else {
        // === Product does not exist => Insert a new record ===
        $imagesString = implode(',', $uploadedImages);
        $sqlInsert = "INSERT INTO products (product_id, name, category, description, images, price, stock) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmtInsert = $conn->prepare($sqlInsert);
        if ($stmtInsert) {
            $stmtInsert->bind_param("sssssdi",
                $productID,
                $productName,
                $productCategory,
                $productDescription,
                $imagesString,
                $productPrice,
                $productStock
            );

            if ($stmtInsert->execute()) {
                $response["status"] = "success";
                $response["message"] = "Product added successfully!";
            } else {
                $response["message"] = "Database error (insert): " . $stmtInsert->error;
            }
            $stmtInsert->close();
        } else {
            $response["message"] = "Error preparing INSERT statement.";
        }
    }
    $checkStmt->close();

} else {
    $response["message"] = "Invalid request method.";
}

echo json_encode($response);
exit;
?>
