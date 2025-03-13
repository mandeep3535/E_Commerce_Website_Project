<?php
// Include the database connection
require_once 'db_connection.php';

// Initialize variables
$message = '';
$error = '';

// Handle form submission for updating users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_users'])) {
    // Process the form data
    foreach ($_POST['users'] as $id => $user) {
        $userId = mysqli_real_escape_string($conn, $id);
        $firstName = mysqli_real_escape_string($conn, $user['first_name']);
        $lastName = mysqli_real_escape_string($conn, $user['last_name']);
        $email = mysqli_real_escape_string($conn, $user['email']);
        $userName = mysqli_real_escape_string($conn, $user['user_name']);
        $status = isset($user['status']) ? 'active' : 'inactive';

        // Update the user in the database
        $query = "UPDATE users SET 
                  first_name = '$firstName', 
                  last_name = '$lastName', 
                  email = '$email',
                  user_name = '$userName',
                  status = '$status'
                  WHERE user_id = '$userId'";
        
        if (!mysqli_query($conn, $query)) {
            $error = "Error updating user: " . mysqli_error($conn);
            break;
        }
    }
    
    if (empty($error)) {
        $message = "Users updated successfully!";
    }
}

// Handle user deletion directly in this file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = mysqli_real_escape_string($conn, $_POST['user_id']);
    
    // Delete the user
    $query = "DELETE FROM users WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $message = "User deleted successfully!";
    } else {
        $error = "Error deleting user: " . mysqli_error($conn);
    }
}

// Fetch all users from the database
$query = "SELECT user_id, first_name, last_name, email, user_name, status FROM users";
$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    $error = "Error: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Users - Admin Panel</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <!-- Bootstrap Icons -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
  />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="admin.user.css"/>
  <style>
    .edit-mode input, .edit-mode select {
      border: 1px solid #ced4da;
      background-color: #fff;
    }
    .view-mode input, .view-mode select {
      border: none;
      background-color: transparent;
      pointer-events: none;
    }
    .status-active {
      color: #198754;
      font-weight: bold;
    }
    .status-inactive {
      color: #dc3545;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- Top Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand fw-bold" href="admin.php">
        <i class="bi bi-speedometer2 me-2"></i> Admin Panel
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Left Side Navigation Links -->
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a href="admin.php" class="nav-link">
                    <i class="bi bi-house-door me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="admin.product.html" class="nav-link ">
                    <i class="bi bi-box me-2"></i> Products
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_order.php" class="nav-link">
                    <i class="bi bi-cart me-2"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a href="admin.user.php" class="nav-link active">
                    <i class="bi bi-people me-2"></i> Users
                </a>
            </li>
        </ul>
    </div>
</nav>
  <!-- Main Content -->
  <div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Registered Users</h2>

      <!-- Edit/Save Buttons -->
      <div>
        <button
          class="btn btn-danger me-2"
          id="editButton"
          onclick="enableEditing()"
        >
          <i class="bi bi-pencil"></i>
          Edit
        </button>
        <button
          class="btn btn-success d-none"
          id="saveButton"
          onclick="document.getElementById('userForm').submit();"
        >
          <i class="bi bi-check"></i>
          Save
        </button>
      </div>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Users Table -->
    <form id="userForm" method="post" action="">
      <div class="table-responsive mt-3">
        <table class="table table-hover view-mode" id="userTable">
          <thead class="table-dark">
            <tr>
              <th>User ID</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Email</th>
              <th>Username</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($result && mysqli_num_rows($result) > 0): 
                while ($row = mysqli_fetch_assoc($result)): 
                    $isActive = ($row['status'] === 'active' || $row['status'] === NULL);
            ?>
            <tr>
              <td><?php echo $row['user_id']; ?></td>
              <td>
                <input type="text" class="form-control-plaintext" 
                       name="users[<?php echo $row['user_id']; ?>][first_name]" 
                       value="<?php echo htmlspecialchars($row['first_name']); ?>">
              </td>
              <td>
                <input type="text" class="form-control-plaintext" 
                       name="users[<?php echo $row['user_id']; ?>][last_name]" 
                       value="<?php echo htmlspecialchars($row['last_name']); ?>">
              </td>
              <td>
                <input type="email" class="form-control-plaintext" 
                       name="users[<?php echo $row['user_id']; ?>][email]" 
                       value="<?php echo htmlspecialchars($row['email']); ?>">
              </td>
              <td>
                <input type="text" class="form-control-plaintext" 
                       name="users[<?php echo $row['user_id']; ?>][user_name]" 
                       value="<?php echo htmlspecialchars($row['user_name']); ?>">
              </td>
              <td>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" 
                         name="users[<?php echo $row['user_id']; ?>][status]" 
                         <?php echo $isActive ? 'checked' : ''; ?> disabled>
                  <span class="status-<?php echo $isActive ? 'active' : 'inactive'; ?>">
                    <?php echo $isActive ? 'Active' : 'Inactive'; ?>
                  </span>
                </div>
              </td>
              <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $row['user_id']; ?>)">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
            <?php 
                endwhile; 
            else: 
            ?>
            <tr>
              <td colspan="7" class="text-center">No users found</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <input type="hidden" name="update_users" value="1">
    </form>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this user? This action cannot be undone.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <form id="deleteForm" method="post" action="">
            <input type="hidden" id="deleteUserId" name="user_id" value="">
            <input type="hidden" name="delete_user" value="1">
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JS -->
  <script>
    function enableEditing() {
      document.getElementById('userTable').classList.remove('view-mode');
      document.getElementById('userTable').classList.add('edit-mode');
      
      // Enable form fields
      const inputs = document.querySelectorAll('#userTable input:not([type="checkbox"]), #userTable select');
      inputs.forEach(input => {
        input.classList.remove('form-control-plaintext', 'form-select-plaintext');
        input.classList.add('form-control', 'form-select');
        input.removeAttribute('readonly');
      });
      
      // Enable checkboxes
      const checkboxes = document.querySelectorAll('#userTable input[type="checkbox"]');
      checkboxes.forEach(checkbox => {
        checkbox.removeAttribute('disabled');
      });
      
      // Show save button, hide edit button
      document.getElementById('editButton').classList.add('d-none');
      document.getElementById('saveButton').classList.remove('d-none');
    }
    
    function confirmDelete(userId) {
      document.getElementById('deleteUserId').value = userId;
      const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
      deleteModal.show();
    }
  </script>
</body>
</html>