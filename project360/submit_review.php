<?php
session_start();
require_once 'db_connection.php';

$response = array("status" => "error", "message" => "Something went wrong.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = (int) $_POST['product_id'];
    
    // Only allow logged-in users to submit a review
    if (!isset($_SESSION['user_id'])) {
        $response["message"] = "You must be logged in to submit a review.";
        echo json_encode($response);
        exit;
    }
    
    // Set the user_id before the duplicate-check
    $user_id = $_SESSION['user_id'];
    
    $rating = (int) $_POST['rating'];
    $comment = $conn->real_escape_string(trim($_POST['comment']));
    
    // Validate rating and comment
    if ($rating < 1 || $rating > 5) {
        $response["message"] = "Rating must be between 1 and 5.";
        echo json_encode($response);
        exit;
    }
    if (empty($comment)) {
        $response["message"] = "Review comment cannot be empty.";
        echo json_encode($response);
        exit;
    }
    
    // Check if the user has already submitted a review for this product
    $sqlCheck = "SELECT review_id FROM reviews WHERE product_id = ? AND user_id = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $product_id, $user_id);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    
    if ($stmtCheck->num_rows > 0) {
        // Fetch the existing review id
        $stmtCheck->bind_result($existing_review_id);
        $stmtCheck->fetch();
        $stmtCheck->close();
        
        // Update the existing review
        $sqlUpdate = "UPDATE reviews SET comment = ?, rating = ? WHERE review_id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("sii", $comment, $rating, $existing_review_id);
        if ($stmtUpdate->execute()) {
            $response["status"] = "success";
            $response["message"] = "Review updated successfully.";
        } else {
            $response["message"] = "Error updating review: " . $stmtUpdate->error;
        }
        $stmtUpdate->close();
    } else {
        $stmtCheck->close();
        // No review exists, so insert a new one
        $sqlInsert = "INSERT INTO reviews (comment, product_id, rating, user_id) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("sisi", $comment, $product_id, $rating, $user_id);
        if ($stmtInsert->execute()) {
            $response["status"] = "success";
            $response["message"] = "Review submitted successfully.";
        } else {
            $response["message"] = "Error submitting review: " . $stmtInsert->error;
        }
        $stmtInsert->close();
    }
}
$conn->close();
echo json_encode($response);
?>
