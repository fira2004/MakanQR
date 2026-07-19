<?php
include "auth_check.php";
// If already logged in, skip straight to the dashboard
if (isset($_SESSION['vendor_id'])) {
    header("Location: vendor.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vendor Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="images/logo.png" alt="MakanQR" class="logo-image"></a>
        <h2>Vendor Login</h2>
    </div>
</header>

<section class="dashboard">
    <div id="loginError" style="color:red;"></div>

    <form id="loginForm" style="max-width:320px;">
        <p>
            <label>Username<br>
            <input type="text" id="username" required></label>
        </p>
        <p>
            <label>Password<br>
            <input type="password" id="password" required></label>
        </p>
        <button type="submit">Log In</button>
    </form>

    <p style="font-size:0.9rem; margin-top:16px;">
        New staff member? <a href="create_vendor.php">Create Account</a>
    </p>
</section>

<script src="vendorLogin.js"></script>

</body>
</html>
