<?php
// Returns live status + items for a set of order IDs, so the customer's
// status page reflects what the vendor actually did in the database
// (instead of relying only on localStorage).
include "db.php";
header("Content-Type: application/json");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
$idList = array_filter(array_map('intval', explode(',', $ids)));

if (empty($idList)) {
    echo json_encode(['orders' => []]);
    exit;
}

// Safe to inline: every element has passed through intval() above.
$idsSql = implode(',', $idList);

$result = mysqli_query($conn, "SELECT * FROM orders WHERE id IN ($idsSql)");

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
