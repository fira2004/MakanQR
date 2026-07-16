<?php include "auth_check.php"; require_vendor_login(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Vendor Dashboard</title>

<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="images/logo.png" alt="MakanQR" class="logo-image"></a>
        <h2>Vendor Dashboard</h2>
    </div>
    <nav>
        <a href="vendor.php">Orders</a>
        <a href="manage_food.php">Manage Food</a>
        <a href="admin.php">Analytics</a>
        <a href="vendorLogout.php">Logout (<?php echo htmlspecialchars($_SESSION['vendor_username']); ?>)</a>
    </nav>
</header>

<section class="dashboard">

    <h1>Incoming Orders</h1>

    <div id="vendorOrders"></div>

</section>

<script src="vendor.js"></script>

</body>
</html>
