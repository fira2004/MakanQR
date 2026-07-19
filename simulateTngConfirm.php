<?php
include "db.php";
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$orderId = isset($input['order_id']) ? (int)$input['order_id'] : 0;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Invalid order']);
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE orders SET payment_status = 'paid' WHERE id = ? AND payment_method = 'tng'");
mysqli_stmt_bind_param($stmt, "i", $orderId);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo json_encode(['success' => (bool)$ok]);
