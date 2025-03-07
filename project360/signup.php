<?php
header('Content-Type: application/json'); // Ensure proper JSON response

include 'db_connection.php'; // Make sure db_connection.php has no echo

$response = array("status" => "error", "message" => "Something went wrong.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Server-side validation
    $errors = [];
    
    // Username validation
    if (strlen($name) < 3 || strlen($name) > 30) {
        $errors[] = "Username must be between 3 and 30 characters";
    }
    
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $name)) {
        $errors[] = "Username can only contain letters, numbers, and spaces";
    }
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    } else {
        // Check if email already exists
        $check_email = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($check_email);
        if ($result && $result->num_rows > 0) {
            $errors[] = "Email already in use. Please use a different email or login instead";
        }
    }
    
    // Password validation
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    // Image validation
    if (!isset($_FILES["image"]) || $_FILES["image"]["error"] != 0) {
        $errors[] = "Please upload a profile image";
    } else {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES["image"]["type"];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed";
        }
        
        // Check file size (2MB max)
        if ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
            $errors[] = "Image file size must be less than 2MB";
        }
    }
    
    // If there are validation errors, return them
    if (!empty($errors)) {
        $response["message"] = implode("<br>", $errors);
        echo json_encode($response);
        exit;
    }
    
    // If validation passes, proceed with registration
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle image upload
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO users (user_name, email, password, profile_image) VALUES ('$name', '$email', '$hashed_password', '$target_file')";
        if ($conn->query($sql) === TRUE) {
            $response["status"] = "success";
            $response["message"] = "Registration successful! You can now login with your credentials.";
        } else {
            $response["message"] = "Database error: " . $conn->error;
        }
    } else {
        $response["message"] = "Error uploading image. Please try again.";
    }
}

echo json_encode($response);
exit;
?>