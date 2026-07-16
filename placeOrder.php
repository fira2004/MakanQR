<?php
include "db.php";
header('Content-Type: application/json');

// Check if database connection exists
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

// Get JSON input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (empty($data) || !isset($data['items']) || count($data['items']) === 0) {
    echo json_encode(['success' => false, 'message' => 'No order data received']);
    exit;
}

$items = $data['items'];
$total = $data['total'] ?? 0;
$tableNumber = $data['tableNumber'] ?? '';
$remarks = $data['remarks'] ?? '';

// Generate a unique order number
$orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

// ---- Waiting time calculation ----
// 1. Work out this order's own total prep time from each item's prep_time * quantity.
$foodIds = array_map(fn($i) => (int)($i['id'] ?? 0), $items);
$foodIds = array_filter($foodIds);
$prepTimes = []; // food_id => prep_time

if (!empty($foodIds)) {
    $idsSql = implode(',', $foodIds);
    $prepResult = mysqli_query($conn, "SELECT id, prep_time FROM foods WHERE id IN ($idsSql)");
    while ($row = mysqli_fetch_assoc($prepResult)) {
        $prepTimes[$row['id']] = (int)$row['prep_time'];
    }
}

$prepTimeTotal = 0;
foreach ($items as $item) {
    $foodId = (int)($item['id'] ?? 0);
    $quantity = (int)($item['quantity'] ?? 1);
    $itemPrepTime = $prepTimes[$foodId] ?? 5; // fallback if food not found
    $prepTimeTotal += $itemPrepTime * $quantity;
}

// 2. Sum the prep time of everything currently Pending/Preparing (the queue ahead of this order).
$queueResult = mysqli_query($conn, "
    SELECT SUM(prep_time_total) AS queue_time, COUNT(*) AS queue_length
    FROM orders
    WHERE status IN ('Pending', 'Preparing')
");
$queueRow = mysqli_fetch_assoc($queueResult);
$queueTime = (int)($queueRow['queue_time'] ?? 0);
$queueLength = (int)($queueRow['queue_length'] ?? 0);

// 3. This order's estimated wait = everything ahead of it + its own prep time.
$estimatedWaitingTime = $queueTime + $prepTimeTotal;

// 4. Snapshot the time context, for later ML training data.
$orderHour = (int)date('G');
$dayOfWeek = (int)date('N'); // 1 (Monday) - 7 (Sunday)

// Insert order
$orderSql = "INSERT INTO orders
             (order_number, total, status, table_number, remarks,
              payment_method, payment_status,
              prep_time_total, estimated_waiting_time, queue_length_at_order,
              order_hour, day_of_week, created_at)
             VALUES (?, ?, 'Pending', ?, ?, 'cash', 'unpaid', ?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $orderSql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL Error: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param(
    $stmt, "sdssiiiii",
    $orderNumber, $total, $tableNumber, $remarks,
    $prepTimeTotal, $estimatedWaitingTime, $queueLength,
    $orderHour, $dayOfWeek
);
mysqli_stmt_execute($stmt);

$orderId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

// Insert order items
$itemSql = "INSERT INTO order_items (order_id, food_id, food_name, price, quantity) 
            VALUES (?, ?, ?, ?, ?)";

$itemStmt = mysqli_prepare($conn, $itemSql);

if (!$itemStmt) {
    echo json_encode(['success' => false, 'message' => 'SQL Error on items: ' . mysqli_error($conn)]);
    exit;
}

foreach ($items as $item) {
    $foodId = $item['id'] ?? 0;
    $foodName = $item['name'] ?? '';
    $price = $item['price'] ?? 0;
    $quantity = $item['quantity'] ?? 1;

    mysqli_stmt_bind_param($itemStmt, "iisdi", $orderId, $foodId, $foodName, $price, $quantity);
    mysqli_stmt_execute($itemStmt);
}

mysqli_stmt_close($itemStmt);
mysqli_close($conn);

echo json_encode([
    'success' => true, 
    'order_id' => $orderId,
    'order_number' => $orderNumber,
    'estimated_waiting_time' => $estimatedWaitingTime,
    'message' => 'Order placed successfully'
]);
?>
