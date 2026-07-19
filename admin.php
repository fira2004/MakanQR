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

    <h1 style="margin-top:40px;">Daily Sales (Last 30 Days)</h1>

    <div class="analytics-card" style="max-width:900px;">
        <canvas id="dailySalesChart" height="90"></canvas>
    </div>

    <div style="max-width:900px; margin-top:20px; overflow-x:auto;">
        <table id="dailySalesTable" style="width:100%; border-collapse:collapse; background:white; border-radius:12px; overflow:hidden;">
            <thead>
                <tr style="background:#0F172A; color:white;">
                    <th style="padding:12px; text-align:left;">Date</th>
                    <th style="padding:12px; text-align:right;">Orders</th>
                    <th style="padding:12px; text-align:right;">Revenue (RM)</th>
                </tr>
            </thead>
            <tbody id="dailySalesBody">
                <tr><td colspan="3" style="padding:12px; text-align:center;">Loading...</td></tr>
            </tbody>
        </table>
    </div>

</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script src="admin.js"></script>

</body>
</html>
