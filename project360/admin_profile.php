<?php
// Include session check
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit;
}

// Get current admin's ID from session
$currentAdminId = $_SESSION['admin_id'] ?? '';
$currentAdminName = $_SESSION['admin_name'] ?? 'Admin';
$currentAdminRole = $_SESSION['admin_role'] ?? 'Administrator';

include 'db_connection.php';

// Function to sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle different request types
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Fetch admin data
if ($action == 'fetch') {
    // Use the ID from session instead of GET parameter
    $adminId = $currentAdminId;
    
    $sql = "SELECT user_id, full_name, email, phone, role, profile_image FROM admins WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $adminId); 
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Admin not found"]);
    }
    
    $stmt->close();
}
// Handle immediate image update
else if ($action == 'update_image' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    
    $success = false;
    $message = "No image provided";
    $target_file = "";
    
    // Process image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "images/";
        $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = "admin_" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check !== false) {
            // Check file size - limit to 5MB
            if ($_FILES["profile_image"]["size"] <= 5000000) {
                // Allow certain file formats
                if (in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
                    // Make sure directory exists
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }
                    
                    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                        // Update database with new image path
                        $img_sql = "UPDATE admins SET profile_image = ? WHERE user_id = ?";
                        $img_stmt = $conn->prepare($img_sql);
                        $img_stmt->bind_param("ss", $target_file, $user_id);
                        $success = $img_stmt->execute();
                        $img_stmt->close();
                        
                        // Update session to reflect new image
                        $_SESSION['admin_profile_image'] = $target_file;
                        
                        $message = $success ? "Profile image updated successfully" : "Database update failed";
                    } else {
                        $message = "Failed to move uploaded file";
                    }
                } else {
                    $message = "Only JPG, JPEG, PNG & GIF files are allowed";
                }
            } else {
                $message = "File is too large, maximum 5MB allowed";
            }
        } else {
            $message = "File is not a valid image";
        }
    }
    
    // Return result
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "file_path" => $success ? $target_file : ""
    ]);
}
// Update admin profile (other fields)
else if ($action == 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $role = sanitize($_POST['role']);
    
    // Update text data
    $sql = "UPDATE admins SET full_name = ?, email = ?, phone = ?, role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $full_name, $email, $phone, $role, $user_id);
    $success = $stmt->execute();
    
    // Update session variables
    if ($success) {
        $_SESSION['admin_name'] = $full_name;
        $_SESSION['admin_role'] = $role;
    }
    
    // Return result
    echo json_encode([
        "success" => $success,
        "message" => $success ? "Profile updated successfully" : "Failed to update profile"
    ]);
    
    $stmt->close();
}

// If not an AJAX request, display the HTML
if (empty($action)) {
    // Get profile image from session or database
    $profileImage = "";
    
    // To fetch profile image from database
    try {
        $stmt = $conn->prepare("SELECT profile_image FROM admins WHERE user_id = ?");
        $stmt->bind_param("s", $currentAdminId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!empty($row['profile_image'])) {
                $profileImage = $row['profile_image'];
            } else {
                // Default image if not set in database
                $profileImage = "images/default_profile.jpg";
            }
        }
    } catch (Exception $e) {
        // If there's an error, use default image
        $profileImage = "images/default_profile.jpg";
    }

    // Close connection for this part
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand fw-bold" href="admin.php">
            <i class="bi bi-speedometer2 me-2"></i> Admin Panel
        </a>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">Admin Profile</h2>
        <div class="card p-4">
            <div class="text-center position-relative">
                <img id="profileImage" src="<?php echo htmlspecialchars($profileImage); ?>" alt="Admin Profile" class="rounded-circle" width="100" height="100">
                <label for="imageUpload" class="position-absolute bottom-0 end-0 bg-light rounded-circle p-1" style="cursor: pointer;">
                    <i class="bi bi-camera"></i>
                </label>
                <input type="file" id="imageUpload" class="d-none" accept="image/*">
                <h3 class="mt-2" id="displayName"><?php echo htmlspecialchars($currentAdminName); ?></h3>
                <p class="text-muted" id="displayRole"><?php echo htmlspecialchars($currentAdminRole); ?></p>
            </div>
            <hr>
            <form id="profileForm" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label"><strong>User ID:</strong></label>
                        <input type="text" id="user_id" name="user_id" class="form-control" readonly>
                        
                        <label class="form-label mt-2"><strong>Full Name:</strong></label>
                        <input type="text" id="full_name" name="full_name" class="form-control" disabled>
                        
                        <label class="form-label mt-2"><strong>Email:</strong></label>
                        <input type="email" id="email" name="email" class="form-control" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Phone:</strong></label>
                        <input type="text" id="phone" name="phone" class="form-control" disabled>
                        
                        <label class="form-label mt-2"><strong>Role:</strong></label>
                        <input type="text" id="role" name="role" class="form-control" disabled>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <button type="button" id="editButton" class="btn btn-danger">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </button>
                    <button type="submit" id="saveButton" class="btn btn-success d-none">
                        <i class="bi bi-check-circle me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fetch admin data when page loads
            fetchAdminData();
            
            // Edit button functionality
            $('#editButton').click(function() {
                let inputs = $('#profileForm input').not('#user_id'); // Don't allow editing user_id
                inputs.prop('disabled', false);
                $('#editButton').addClass('d-none');
                $('#saveButton').removeClass('d-none');
            });

            // Image upload and immediate update functionality
            $('#imageUpload').change(function(event) {
                if (event.target.files && event.target.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profileImage').attr('src', e.target.result);
                        
                        // Immediately upload the image to server
                        let formData = new FormData();
                        formData.append('profile_image', event.target.files[0]);
                        formData.append('user_id', $('#user_id').val());
                        
                        $.ajax({
                            url: 'admin_profile.php?action=update_image',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                let result = JSON.parse(response);
                                if (result.success) {
                                    // Show success message
                                    alert('Profile picture updated successfully!');
                                } else {
                                    alert('Failed to update profile picture: ' + result.message);
                                }
                            },
                            error: function() {
                                alert('An error occurred while updating the profile picture.');
                            }
                        });
                    }
                    reader.readAsDataURL(event.target.files[0]);
                }
            });

            // Submit form functionality
            $('#profileForm').submit(function(event) {
                event.preventDefault();
                
                // Create FormData object for form data
                let formData = new FormData(this);
                
                // Send AJAX request to update profile
                $.ajax({
                    url: 'admin_profile.php?action=update',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        let result = JSON.parse(response);
                        if (result.success) {
                            alert('Profile updated successfully!');
                            
                            // Update display name and role
                            $('#displayName').text($('#full_name').val());
                            $('#displayRole').text($('#role').val());
                            
                            // Disable form fields again
                            $('#profileForm input').not('#user_id').prop('disabled', true);
                            $('#editButton').removeClass('d-none');
                            $('#saveButton').addClass('d-none');
                            
                            // Refresh data
                            fetchAdminData();
                        } else {
                            alert('Failed to update profile. Please try again.');
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
        
        // Function to fetch admin data
        function fetchAdminData() {
            // Get data for the currently logged in admin
            $.ajax({
                url: 'admin_profile.php?action=fetch',
                type: 'GET',
                success: function(response) {
                    try {
                        let data = JSON.parse(response);
                        if (!data.error) {
                            // Populate form fields
                            $('#user_id').val(data.user_id);
                            $('#full_name').val(data.full_name);
                            $('#email').val(data.email);
                            $('#phone').val(data.phone);
                            $('#role').val(data.role);
                            
                            // Update display name and role
                            $('#displayName').text(data.full_name);
                            $('#displayRole').text(data.role);
                            
                            // Update profile image if available
                            if (data.profile_image) {
                                $('#profileImage').attr('src', data.profile_image);
                            }
                        } else {
                            alert('Failed to load admin data: ' + data.error);
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                },
                error: function() {
                    alert('Failed to connect to the server. Please try again.');
                }
            });
        }
    </script>
</body>
</html>
<?php
}
?>