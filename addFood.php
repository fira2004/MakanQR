<?php
include "auth_check.php";
require_vendor_login(true);
include "db.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

$name = trim($input['name'] ?? '');
$desc = trim($input['desc'] ?? '');
$price = isset($input['price']) ? (float)$input['price'] : 0;
$category = $input['category'] ?? '';
$image = trim($input['image'] ?? '');
$availability = in_array($input['availability'] ?? '', ['Available', 'Not Available'], true)
    ? $input['availability'] : 'Available';

if ($name === '' || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Name and a valid price are required']);
    exit;
}

$stmt = mysqli_prepare($conn, "INSERT INTO foods (name, description, price, category, image, availability) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssdsss", $name, $desc, $price, $category, $image, $availability);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}
mysqli_stmt_close($stmt);
