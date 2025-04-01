<?php
// Prevent caching issues
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$login_err = "";

// Include database connection if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'db_connection.php';

    // Function to sanitize user inputs
    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Get and sanitize user inputs (username/email and password)
    $user_input = sanitize_input($_POST["user_input"]); // Can be email or username
    $password = $_POST["password"]; 

 
    $sql = "SELECT * FROM users WHERE email = ? OR user_name = ?";
    
    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        // User found
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user["password"])) {
            // Password is correct - set session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $user["user_id"]; 
            $_SESSION["email"] = $user["email"];

            // Redirect to dashboard
            header("Location: home.php");
            exit;
        } else {
            $login_err = "Invalid password";
        }
    } else {
        $login_err = "No account found with that email/username";
    }

    // Close statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MV Electronics - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <link rel = "stylesheet" href = "footer.css">
</head>
<body>

<!--Header - Conditionally loaded based on login status-->
<?php
if (isset($_SESSION["user_id"])) {
    include "loginheader.php";
} else {
    include "header.php";
}
?>

<nav class="breadcrumb mt-4 ms-5">
    <a class="breadcrumb-item text-decoration-none text-muted" href="home.php">Home</a>
    <span class="breadcrumb-item active text-secondary fw-bold">Login</span>
</nav>

<!-- Login Section -->
<div class="container-fluid login-container">
    <div class="row align-items-center">
        <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-center text-md-start px-4 p-md-0">
            <img src="images/loginpagephoto.webp" alt="Shopping" class="img-fluid">
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-center text-md-start py-4 p-md-0">
            <h2>Log in to MV Electronics</h2>
            <p>Enter your details below</p>
            
            <?php if (!empty($login_err)): ?>
                <div class="alert alert-danger w-75">
                    <?php echo $login_err; ?>
                </div>
            <?php endif; ?>
            
            <form class="w-75" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <!-- Changed input type to text and name to "user_input" -->
                    <input type="text" name="user_input" class="form-control" placeholder="Email or Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Log In</button>
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mt-2">
                        <a href="signup.html" class="text-danger">Sign up</a>
                    </div>
                    <div class="col-md-6 text-center text-md-end mt-2">
                        <a href="forgotpassword.html" class="text-danger">Forgot Password?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once "footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
