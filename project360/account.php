<?php

require_once 'session_handler.php'; 
require_once 'db_connection.php';   // Database connection 

// Initialize variables
$userName     = '';
$firstName    = '';
$lastName     = '';
$email        = '';
$profileImage = '';
$userId       = 0;
$errorMessage = '';

// 2. Check if the user is logged in and fetch user data
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $sql  = "SELECT user_name, first_name, last_name, email, profile_image, password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $userName      = $row['user_name'];
        $firstName     = $row['first_name'];
        $lastName      = $row['last_name'];
        $email         = $row['email'];
        $profileImage  = $row['profile_image'];
        $storedPassword= $row['password']; // hashed password
    }
    $stmt->close();
}

// 3. Handle form submission (before sending any HTML)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFirstName    = $_POST['first_name']       ?? '';
    $newLastName     = $_POST['last_name']        ?? '';
    $newEmail        = $_POST['email']            ?? '';
    $newPassword     = $_POST['password']         ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $oldPassword     = $_POST['old_password']     ?? '';
    $newProfileImage = $_FILES['profile_image']['name'] ?? '';

    $passwordMatch   = ($newPassword === $confirmPassword);
    $passwordUpdated = false;

    // Only verify old password if the user is changing the password
    if (!empty($newPassword)) {
        if (empty($oldPassword)) {
            $errorMessage = "Old password is required to change the password.";
        } else {
            // Verify old password
            if (!password_verify($oldPassword, $storedPassword)) {
                $errorMessage = "Old password is incorrect!";
            } elseif ($passwordMatch) {
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $passwordUpdated = true;
            } else {
                $errorMessage = "New password and confirm password do not match!";
            }
        }
    }

    // If no error, update user information
    if (empty($errorMessage)) {
        if (!empty($newProfileImage)) {
            $imagePath = 'uploads/' . basename($newProfileImage);

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $imagePath)) {
                // Update with new profile image
                if ($passwordUpdated) {
                    $sql  = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, profile_image = ? WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssi", $newFirstName, $newLastName, $newEmail, $newPasswordHash, $imagePath, $userId);
                } else {
                    $sql  = "UPDATE users SET first_name = ?, last_name = ?, email = ?, profile_image = ? WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssi", $newFirstName, $newLastName, $newEmail, $imagePath, $userId);
                }
                $stmt->execute();
                $stmt->close();
            } else {
                $errorMessage = "There was an error uploading your profile image.";
            }
        } else {
            // No new image
            if ($passwordUpdated) {
                $sql  = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $newFirstName, $newLastName, $newEmail, $newPasswordHash, $userId);
            } else {
                $sql  = "UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $newFirstName, $newLastName, $newEmail, $userId);
            }
            $stmt->execute();
            $stmt->close();
        }

        // If still no error after DB updates, set success message and redirect
        if (empty($errorMessage)) {
            $_SESSION['success_message'] = "Your profile has been updated successfully!";
            header("Location: account.php");
            exit();
        }
    }
}


require_once 'header-loader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Edit Profile</title>

    <link rel="stylesheet" href="account.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel = "stylesheet" href = "footer.css">
    <link rel = "stylesheet" href = "header.css">
</head>
<body>


<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active text-secondary fw-bold" aria-current="page">My Account</li>
        </ol>
    </nav>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- Sidebar (Left) -->
        <div class="col-md-3 mt-5 text-center text-lg-start">
            <h6 class="fw-bold">Manage Account</h6>
            <ul class="list-unstyled">
                <li><a href="wishlist.php" > Wishlist</a></li>
                <li><a href="order-history.php"> Orders</a></li>
            </ul>
        </div>

        <!-- Profile Form (Right) -->
        <div class="col-md-9 mt-5">
            <div class="card shadow-sm p-4">
                <h5 class="text-dark fw-bold">Edit Your Profile</h5>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php 
                            echo $_SESSION['success_message']; 
                            unset($_SESSION['success_message']); 
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Show error if it exists -->
                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger mt-2"><?php echo $errorMessage; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" 
                                   name="first_name" 
                                   value="<?php echo htmlspecialchars($firstName); ?>" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" 
                                   name="last_name" 
                                   value="<?php echo htmlspecialchars($lastName); ?>" 
                                   required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($email); ?>" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="profile_image">
                            <?php if (!empty($profileImage)): ?>
                                <img src="<?php echo htmlspecialchars($profileImage); ?>" 
                                     alt="Profile Image" 
                                     class="mt-2" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            <?php endif; ?>
                        </div>
                    </div>

                    <h6 class="mt-4">Password Changes</h6>
                    <div class="mt-2">
                        <input type="password" class="form-control mb-2" 
                               name="old_password" 
                               placeholder="Current Password">
                        <input type="password" class="form-control mb-2" 
                               name="password" 
                               placeholder="New Password">
                        <input type="password" class="form-control mb-3" 
                               name="confirm_password" 
                               placeholder="Confirm New Password">
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-light me-3">Cancel</button>
                        <button type="submit" class="btn btn-danger">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "footer.php"; ?>

<script src = "loginheader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
