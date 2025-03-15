<?php
session_start();
if (isset($_POST['coupon']) && strtoupper($_POST['coupon']) === 'MV50') {
    $_SESSION['coupon'] = 'MV50';
    echo json_encode(["success" => true, "coupon" => "MV50"]);
} else {
    unset($_SESSION['coupon']);
    echo json_encode(["success" => false, "message" => "Invalid coupon"]);
}
?>
