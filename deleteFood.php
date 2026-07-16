<?php
include "auth_check.php";
require_vendor_login(true);
include "db.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$id = isset($input['id']) ? (int)$input['id'] : 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid food id']);
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM foods WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    // Most likely a foreign-key conflict if this food is referenced by past orders
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}
mysqli_stmt_close($stmt);
