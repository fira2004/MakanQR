<?php
include "auth_check.php";
require_vendor_login(true);
include "db.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

$id = isset($input['id']) ? (int)$input['id'] : 0;
$name = trim($input['name'] ?? '');
$desc = trim($input['desc'] ?? '');
$price = isset($input['price']) ? (float)$input['price'] : 0;
$category = $input['category'] ?? '';
$image = trim($input['image'] ?? '');
$availability = in_array($input['availability'] ?? '', ['Available', 'Not Available'], true)
    ? $input['availability'] : 'Available';

if (!$id || $name === '' || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid food id, name, or price']);
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE foods SET name = ?, description = ?, price = ?, category = ?, image = ?, availability = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "ssdsssi", $name, $desc, $price, $category, $image, $availability, $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}
mysqli_stmt_close($stmt);
