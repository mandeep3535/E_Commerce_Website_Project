<?php
// wishlist-count.php
require_once "session_handler.php";
require_once "db_connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(["count" => 0]);
    exit;
}

$user_id = (int) $_SESSION["user_id"];
$sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc()["count"];

echo json_encode(["count" => (int)$count]);

$stmt->close();
$conn->close();
?>