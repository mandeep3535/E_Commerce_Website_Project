<?php

header('Content-Type: application/json'); // Ensure proper JSON response

include 'db_connection.php'; // Make sure db_connection.php has no echo

$response = array("status" => "error", "message" => "Something went wrong.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name  = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $user_name  = isset($_POST['user_name']) ? $_POST['user_name'] : '';
    $email      = isset($_POST['email']) ? $_POST['email'] : '';
    $password   = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Server-side validation
    $errors = [];
    
    // First name validation: at least 2 characters and letters/spaces only
    if (strlen($first_name) < 2) {
        $errors[] = "First name must be at least 2 characters long";
    }
    if (!preg_match('/^[A-Za-z\s]+$/', $first_name)) {
        $errors[] = "First name can only contain letters and spaces";
    }
    
    // Last name validation: at least 2 characters and letters/spaces only
    if (strlen($last_name) < 2) {
        $errors[] = "Last name must be at least 2 characters long";
    }
    if (!preg_match('/^[A-Za-z\s]+$/', $last_name)) {
        $errors[] = "Last name can only contain letters and spaces";
    }
    
    // Username validation (3-30 characters, alphanumeric with spaces)
    if (strlen($user_name) < 3 || strlen($user_name) > 30) {
        $errors[] = "Username must be between 3 and 30 characters";
    }
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $user_name)) {
        $errors[] = "Username can only contain letters, numbers, and spaces";
    }
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    } else {
        // Escape email before using it in query
        $emailEscaped = mysqli_real_escape_string($conn, $email);
        // Check if email already exists
        $check_email = "SELECT * FROM users WHERE email = '$emailEscaped'";
        $result = $conn->query($check_email);
        if ($result && $result->num_rows > 0) {
            $errors[] = "Email already in use. Please use a different email or login instead";
        }
    }
    
    // Password validation (min 8 chars, at least 1 uppercase, 1 lowercase, 1 number)
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
        
        // Check file size (25MB max)
        if ($_FILES["image"]["size"] > 25 * 1024 * 1024) {
            $errors[] = "Image file size must be less than 25MB";
        }
    }
    
    // If there are validation errors, return them
    if (!empty($errors)) {
        $response["message"] = implode("<br>", $errors);
        echo json_encode($response);
        exit;
    }
    
    // If validation passes, proceed with registration
    
    // Escape input values
    $first_name   = mysqli_real_escape_string($conn, $first_name);
    $last_name    = mysqli_real_escape_string($conn, $last_name);
    $user_name    = mysqli_real_escape_string($conn, $user_name);
    // $email is already escaped above in $emailEscaped
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
        // Update SQL to include first_name and last_name fields
        $sql = "INSERT INTO users (first_name, last_name, user_name, email, password, profile_image) 
                VALUES ('$first_name', '$last_name', '$user_name', '$emailEscaped', '$hashed_password', '$target_file')";
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
