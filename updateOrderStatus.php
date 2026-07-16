<?php
include "auth_check.php";
require_vendor_login(true);
// Vendor dashboard posts here to move an order to its next status.
// This is the write side that makes the customer's status page and the
// vendor dashboard agree on reality.
include "db.php";
header("Content-Type: application/json");

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$orderId = isset($input['order_id']) ? (int)$input['order_id'] : 0;
$status = $input['status'] ?? '';

$allowedStatuses = ['Pending', 'Preparing', 'Ready', 'Completed'];

if (!$orderId || !in_array($status, $allowedStatuses, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order id or status']);
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL Error: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "si", $status, $orderId);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo json_encode(['success' => (bool)$ok]);
