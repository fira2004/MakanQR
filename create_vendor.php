<?php
// Even with an invite code, this endpoint should still be deleted once
// your staff accounts are set up - it's not meant to be a permanent
// self-service registration page.
include "db.php";

// The invite code is set as a server-side environment variable (same
// pattern as DB_HOST etc.) so it's never visible in the code or on GitHub.
// On Render: Environment tab -> add VENDOR_INVITE_CODE with a secret value
// only you and your staff know. Locally, it falls back to a placeholder -
// change VENDOR_INVITE_CODE_FALLBACK below for local testing.
define('VENDOR_INVITE_CODE', getenv('VENDOR_INVITE_CODE') ?: 'CHANGE_ME_LOCAL_TEST_CODE');

$message = "";
$showLoginLink = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inviteCode = $_POST['invite_code'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!hash_equals(VENDOR_INVITE_CODE, $inviteCode)) {
        // Deliberately vague - doesn't reveal whether the code was close/wrong
        // vs some other field being wrong, so it can't be brute-forced by feedback.
        $message = "Invalid invite code, username, or password.";
    } elseif ($username === '' || strlen($username) < 3) {
        $message = "Username must be at least 3 characters.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
    } else {
        // Check for an existing username first for a clearer error than a raw SQL failure
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM vendors WHERE username = ?");
        mysqli_stmt_bind_param($checkStmt, "s", $username);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $message = "That username is already taken.";
            mysqli_stmt_close($checkStmt);
        } else {
            mysqli_stmt_close($checkStmt);

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO vendors (username, password) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $username, $hash);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Vendor account '$username' created successfully.";
                $showLoginLink = true;
            } else {
                $message = "Something went wrong. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Vendor Account</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <h2>Create Vendor Account (Staff Only)</h2>
    </div>
</header>

<section class="dashboard">
    <?php if ($message): ?>
        <p><strong style="color:<?php echo $showLoginLink ? '#10b981' : '#dc2626'; ?>;"><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <?php if ($showLoginLink): ?>
        <p><a href="vendor_login.php" class="btn-primary">Go to Vendor Login →</a></p>
    <?php else: ?>
    <form method="POST" style="max-width:320px;">
        <p>
            <label>Staff Invite Code<br>
            <input type="password" name="invite_code" required autocomplete="off"></label>
        </p>
        <p>
            <label>Username<br>
            <input type="text" name="username" required></label>
        </p>
        <p>
            <label>Password (min. 8 characters)<br>
            <input type="password" name="password" required minlength="8"></label>
        </p>
        <button type="submit">Create Vendor Account</button>
    </form>
    <p style="font-size:0.85em; color:#6b7280;">Ask your manager for the invite code.</p>
    <p style="font-size:0.9rem;"><a href="vendor_login.php">← Back to Login</a></p>
    <?php endif; ?>
</section>

</body>
</html>
