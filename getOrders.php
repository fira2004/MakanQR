<?php
include "auth_check.php";
require_vendor_login(true);
// Returns all orders that aren't Completed yet, oldest first (queue order),
// with their line items joined in. Used by the vendor dashboard.
include "db.php";
header("Content-Type: application/json");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM orders WHERE status != 'Completed' ORDER BY created_at ASC");

$orders = [];
while ($order = mysqli_fetch_assoc($result)) {
    $orderId = (int)$order['id'];
    $itemsResult = mysqli_query($conn, "SELECT food_name, price, quantity FROM order_items WHERE order_id = $orderId");
    $items = [];
    while ($item = mysqli_fetch_assoc($itemsResult)) {
        $items[] = $item;
    }
    $order['items'] = $items;
    $orders[] = $order;
}

echo json_encode(['orders' => $orders]);
