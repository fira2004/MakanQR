<?php
include "auth_check.php";
require_vendor_login(true);
include "db.php";
header("Content-Type: application/json");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM foods ORDER BY id DESC");
$foods = [];
while ($row = mysqli_fetch_assoc($result)) {
    $foods[] = $row;
}

echo json_encode($foods);
