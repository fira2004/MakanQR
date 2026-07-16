<?php
// ONE-TIME SETUP — delete this file once you've created your vendor account(s).
include "db.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $message = "Username and password are required.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO vendors (username, password) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $username, $hash);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Vendor account '$username' created successfully.";
            $showLoginLink = true;
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Vendor Account</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <h2>Create Vendor Account (one-time setup)</h2>
    </div>
</header>

<section class="dashboard">
    <?php if ($message): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <?php if (!empty($showLoginLink)): ?>
        <p><a href="vendor_login.php" class="btn-primary">Go to Vendor Login →</a></p>
        <p style="font-size:0.85em;">Remember to delete create_vendor.php once you're set up.</p>
    <?php else: ?>
    <form method="POST" style="max-width:320px;">
        <p>
            <label>Username<br>
            <input type="text" name="username" required></label>
        </p>
        <p>
            <label>Password<br>
            <input type="password" name="password" required></label>
        </p>
        <button type="submit">Create Vendor Account</button>
    </form>
    <?php endif; ?>
</section>

</body>
</html>
