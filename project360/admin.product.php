error_reporting(E_ALL);
ini_set('display_errors', 1);



include_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productID = (int)$_POST["productID"]; // Ensure it matches INT type
    $productName = $_POST["productName"];
    $price = (float)$_POST["productPrice"]; // Ensure price is FLOAT
    $stock = (int)$_POST["productStock"];
    $category = $_POST["productCategory"];
    $description = $_POST["productDescription"];

    // Handle image uploads
    $uploadedImages = [];
    $uploadDir = "uploads/products/";
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (isset($_FILES["productImages"]) && !empty($_FILES["productImages"]["name"][0])) {
        $fileCount = count($_FILES["productImages"]["name"]);
        
        if ($fileCount > 3) {
            echo "<script>alert('Maximum 3 images allowed'); window.location='admin.product.html';</script>";
            exit();
        }
        
        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = time() . '_' . basename($_FILES["productImages"]["name"][$i]);
            $targetFilePath = $uploadDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            $allowTypes = ["jpg", "jpeg", "png", "gif"];
            if (in_array(strtolower($fileType), $allowTypes)) {
                if (move_uploaded_file($_FILES["productImages"]["tmp_name"][$i], $targetFilePath)) {
                    $uploadedImages[] = $fileName;
                } else {
                    echo "Failed to upload image: " . $_FILES["productImages"]["error"][$i];
                    exit();
                }
            } else {
                echo "<script>alert('Only JPG, JPEG, PNG, GIF files are allowed'); window.location='admin.product.html';</script>";
                exit();
            }
        }
    }

    $imagesJson = json_encode($uploadedImages);
    
    $sql = "INSERT INTO products (product_id, name, description, stock, price, category, images) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdiss", $productID, $productName, $description, $stock, $price, $category, $imagesJson);
    
    if (!$stmt->execute()) {
    die("❌ Database Insert Error: " . $stmt->error);
  } else {
    echo "<script>alert('✅ Product added successfully'); window.location='admin.product.html';</script>";
   }
    
    echo "<pre>";
print_r($_POST); // Check if form data is being received
print_r($_FILES); // Check if images are being uploaded
echo "</pre>";
exit();


    $stmt->close();
    $conn->close();
    exit();


}
