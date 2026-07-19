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

// Basic brute-force slowdown: after 5 failed attempts, force a wait.
// This is per-browser-session, not a full production-grade rate limiter,
// but it stops naive automated guessing.
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_locked_until'] = 0;
}

if (time() < $_SESSION['login_locked_until']) {
    $waitSeconds = $_SESSION['login_locked_until'] - time();
    echo json_encode(['success' => false, 'message' => "Too many attempts. Try again in $waitSeconds seconds."]);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, username, password FROM vendors WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vendor = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$vendor || !password_verify($password, $vendor['password'])) {
    $_SESSION['login_attempts']++;
    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['login_locked_until'] = time() + 30; // 30 second lockout
        $_SESSION['login_attempts'] = 0;
    }
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit;
}

// Successful login - reset the counter
$_SESSION['login_attempts'] = 0;
$_SESSION['login_locked_until'] = 0;

$_SESSION['vendor_id'] = $vendor['id'];
$_SESSION['vendor_username'] = $vendor['username'];

echo json_encode(['success' => true]);
