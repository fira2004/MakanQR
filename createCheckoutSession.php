<?php
include "db.php";
include "config_stripe.php";
include "stripe_helper.php";
header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (empty($data) || !isset($data['items']) || count($data['items']) === 0) {
    echo json_encode(['success' => false, 'message' => 'No order data received']);
    exit;
}

$items = $data['items'];
$total = $data['total'] ?? 0;
$tableNumber = $data['tableNumber'] ?? '';
$phoneNumber = $data['phoneNumber'] ?? '';
$remarks = $data['remarks'] ?? '';

$orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

// ---- Same waiting-time calculation as placeOrder.php ----
$foodIds = array_filter(array_map(fn($i) => (int)($i['id'] ?? 0), $items));
$prepTimes = [];
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
    $prepTimeTotal += ($prepTimes[$foodId] ?? 5) * $quantity;
}

$queueResult = mysqli_query($conn, "
    SELECT SUM(prep_time_total) AS queue_time, COUNT(*) AS queue_length
    FROM orders
    WHERE status IN ('Pending', 'Preparing')
");
$queueRow = mysqli_fetch_assoc($queueResult);
$queueTime = (int)($queueRow['queue_time'] ?? 0);
$queueLength = (int)($queueRow['queue_length'] ?? 0);
$estimatedWaitingTime = $queueTime + $prepTimeTotal;

$orderHour = (int)date('G');
$dayOfWeek = (int)date('N');

// ---- Insert the order as Pending / unpaid / card. It only becomes visible
// to the vendor once payment is confirmed (see getOrders.php's filter). ----
$orderSql = "INSERT INTO orders
             (order_number, total, status, table_number, phone_number, remarks,
              payment_method, payment_status,
              prep_time_total, estimated_waiting_time, queue_length_at_order,
              order_hour, day_of_week, created_at)
             VALUES (?, ?, 'Pending', ?, ?, ?, 'card', 'unpaid', ?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $orderSql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL Error: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param(
    $stmt, "sdsssiiiii",
    $orderNumber, $total, $tableNumber, $phoneNumber, $remarks,
    $prepTimeTotal, $estimatedWaitingTime, $queueLength,
    $orderHour, $dayOfWeek
);
mysqli_stmt_execute($stmt);
$orderId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

// Insert order items
$itemStmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, food_id, food_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
foreach ($items as $item) {
    $foodId = $item['id'] ?? 0;
    $foodName = $item['name'] ?? '';
    $price = $item['price'] ?? 0;
    $quantity = $item['quantity'] ?? 1;
    mysqli_stmt_bind_param($itemStmt, "iisdi", $orderId, $foodId, $foodName, $price, $quantity);
    mysqli_stmt_execute($itemStmt);
}
mysqli_stmt_close($itemStmt);

// ---- Build the Stripe Checkout Session ----
$lineItems = [];
foreach ($items as $item) {
    $lineItems[] = [
        'price_data' => [
            'currency' => 'myr',
            'product_data' => ['name' => $item['name'] ?? 'Item'],
            'unit_amount' => (int)round(($item['price'] ?? 0) * 100), // sen
        ],
        'quantity' => (int)($item['quantity'] ?? 1),
    ];
}

$subtotal = array_sum(array_map(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1), $items));
$serviceCharge = round($subtotal * 0.05, 2);
if ($serviceCharge > 0) {
    $lineItems[] = [
        'price_data' => [
            'currency' => 'myr',
            'product_data' => ['name' => 'Service Charge (5%)'],
            'unit_amount' => (int)round($serviceCharge * 100),
        ],
        'quantity' => 1,
    ];
}

$stripeParams = [
    'mode' => 'payment',
    'payment_method_types' => ['card'],
    'line_items' => $lineItems,
    'success_url' => SITE_BASE_URL . '/paymentSuccess.php?session_id={CHECKOUT_SESSION_ID}&order_id=' . $orderId,
    'cancel_url' => SITE_BASE_URL . '/paymentCancel.php?order_id=' . $orderId,
    'metadata' => ['order_id' => $orderId, 'order_number' => $orderNumber],
];

$session = stripe_request('checkout/sessions', $stripeParams, 'POST');

if (isset($session['error'])) {
    echo json_encode(['success' => false, 'message' => 'Stripe error: ' . ($session['error']['message'] ?? 'Unknown error')]);
    exit;
}

// Save the session id so we can verify payment on return
$sessionId = $session['id'];
$updateStmt = mysqli_prepare($conn, "UPDATE orders SET stripe_session_id = ? WHERE id = ?");
mysqli_stmt_bind_param($updateStmt, "si", $sessionId, $orderId);
mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

echo json_encode([
    'success' => true,
    'checkout_url' => $session['url'],
    'order_id' => $orderId,
    'order_number' => $orderNumber
]);
