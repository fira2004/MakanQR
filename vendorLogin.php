<?php
include "auth_check.php";
include "db.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, username, password FROM vendors WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vendor = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$vendor || !password_verify($password, $vendor['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit;
}

$_SESSION['vendor_id'] = $vendor['id'];
$_SESSION['vendor_username'] = $vendor['username'];

echo json_encode(['success' => true]);
