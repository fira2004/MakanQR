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

// Only count orders that are actually real: cash orders, or card orders
// that Stripe has confirmed as paid. Excludes abandoned/unpaid card checkouts.
$visibilityFilter = "(payment_method = 'cash' OR payment_status = 'paid')";

$totalOrdersResult = mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders WHERE $visibilityFilter");
$totalOrders = (int)(mysqli_fetch_assoc($totalOrdersResult)['c'] ?? 0);

$revenueResult = mysqli_query($conn, "SELECT SUM(total) AS r FROM orders WHERE $visibilityFilter");
$totalRevenue = (float)(mysqli_fetch_assoc($revenueResult)['r'] ?? 0);

$popularResult = mysqli_query($conn, "
    SELECT oi.food_name, SUM(oi.quantity) AS qty
    FROM order_items oi
    JOIN orders o ON o.id = oi.order_id
    WHERE $visibilityFilter
    GROUP BY oi.food_name
    ORDER BY qty DESC
    LIMIT 1
");
$popularRow = mysqli_fetch_assoc($popularResult);
$popularFood = $popularRow ? $popularRow['food_name'] : '-';

// Daily breakdown: orders and revenue grouped by calendar day, most recent
// 30 days first. DATE(created_at) groups everything placed on the same day
// together regardless of time.
$dailyResult = mysqli_query($conn, "
    SELECT DATE(created_at) AS day, COUNT(*) AS orders, SUM(total) AS revenue
    FROM orders
    WHERE $visibilityFilter
    GROUP BY DATE(created_at)
    ORDER BY day DESC
    LIMIT 30
");

$dailySales = [];
while ($row = mysqli_fetch_assoc($dailyResult)) {
    $dailySales[] = [
        'date' => $row['day'],
        'orders' => (int)$row['orders'],
        'revenue' => (float)$row['revenue']
    ];
}

echo json_encode([
    'totalOrders' => $totalOrders,
    'totalRevenue' => $totalRevenue,
    'popularFood' => $popularFood,
    'dailySales' => $dailySales
]);
