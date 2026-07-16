<?php
include "auth_check.php";
require_vendor_login(true);
// Real analytics computed from the orders/order_items tables, replacing
// the localStorage-only numbers the admin dashboard used to show.
include "db.php";
header("Content-Type: application/json");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

$totalOrdersResult = mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders");
$totalOrders = (int)(mysqli_fetch_assoc($totalOrdersResult)['c'] ?? 0);

$revenueResult = mysqli_query($conn, "SELECT SUM(total) AS r FROM orders");
$totalRevenue = (float)(mysqli_fetch_assoc($revenueResult)['r'] ?? 0);

$popularResult = mysqli_query($conn, "
    SELECT food_name, SUM(quantity) AS qty
    FROM order_items
    GROUP BY food_name
    ORDER BY qty DESC
    LIMIT 1
");
$popularRow = mysqli_fetch_assoc($popularResult);
$popularFood = $popularRow ? $popularRow['food_name'] : '-';

echo json_encode([
    'totalOrders' => $totalOrders,
    'totalRevenue' => $totalRevenue,
    'popularFood' => $popularFood
]);
