<?php include "auth_check.php"; require_vendor_login(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <div class="logo">
        <h2>📊 Admin Dashboard</h2>
    </div>
    <nav>
        <a href="vendor.php">Orders</a>
        <a href="manage_food.php">Manage Food</a>
        <a href="admin.php">Analytics</a>
        <a href="vendorLogout.php">Logout (<?php echo htmlspecialchars($_SESSION['vendor_username']); ?>)</a>
    </nav>
</header>

<section class="dashboard">

    <div class="analytics-grid">

        <div class="analytics-card">
            <h3>Total Orders</h3>
            <h1 id="totalOrders">0</h1>
        </div>

        <div class="analytics-card">
            <h3>Total Revenue</h3>
            <h1 id="totalRevenue">
                RM 0
            </h1>
        </div>

        <div class="analytics-card">
            <h3>Popular Food</h3>
            <h1 id="popularFood">
                -
            </h1>
        </div>

    </div>

</section>

<script src="admin.js"></script>

</body>
</html>
