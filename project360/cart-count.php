<?php
// cart-count.php
require_once "session_handler.php";
require_once "db_connection.php";
header('Content-Type: application/json');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(["count" => 0]);
    exit;
}

$user_id = (int) $_SESSION["user_id"];
$sql = "SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total = $row["total"] ? (int)$row["total"] : 0;

echo json_encode(["count" => $total]);

$stmt->close();
$conn->close();
?>
