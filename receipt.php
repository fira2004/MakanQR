<?php
include "db.php";

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

$order = null;
$items = [];

if ($orderId && $conn) {
    $orderResult = mysqli_query($conn, "SELECT * FROM orders WHERE id = $orderId");
    $order = mysqli_fetch_assoc($orderResult);

    if ($order) {
        $itemsResult = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $orderId");
        while ($row = mysqli_fetch_assoc($itemsResult)) {
            $items[] = $row;
        }
    }
}

$paymentLabels = [
    'cash' => '💵 Cash on Pickup',
    'card' => '💳 Card (Online)',
    'tng'  => "📱 Touch 'n Go eWallet",
];

if ($order) {
    $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
    $serviceCharge = round($subtotal * 0.05, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt <?php echo $order ? htmlspecialchars($order['order_number']) : ''; ?> | MakanQR</title>
<link rel="stylesheet" href="style.css">
<style>
    .receipt-wrap {
        max-width: 420px;
        margin: 40px auto;
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        font-family: 'Courier New', monospace;
    }
    .receipt-wrap h2 {
        text-align: center;
        margin-bottom: 4px;
        font-family: inherit;
    }
    .receipt-sub {
        text-align: center;
        color: #6b7280;
        font-size: 0.85rem;
        margin-bottom: 20px;
    }
    .receipt-divider {
        border-top: 1px dashed #cbd5e1;
        margin: 14px 0;
    }
    .receipt-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.95rem;
        margin: 4px 0;
    }
    .receipt-row.total {
        font-weight: bold;
        font-size: 1.1rem;
    }
    .receipt-meta {
        font-size: 0.85rem;
        color: #4b5563;
        margin-bottom: 4px;
    }
    .receipt-actions {
        text-align: center;
        margin-top: 24px;
    }
    .receipt-actions a, .receipt-actions button {
        display: inline-block;
        margin: 6px;
        padding: 10px 20px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        font-weight: 600;
        background: #0F172A;
        color: white;
        font-family: 'Poppins', sans-serif;
    }
    .receipt-actions a:hover, .receipt-actions button:hover {
        background: #FBBF24;
        color: #0F172A;
    }
    @media print {
        header, nav, .receipt-actions { display: none !important; }
        body { background: white !important; }
        .receipt-wrap { box-shadow: none; margin: 0; }
    }
</style>
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="images/logo.png" alt="MakanQR" class="logo-image"></a>
    </div>
</header>

<div class="receipt-wrap">
<?php if (!$order): ?>
    <h2>Receipt not found</h2>
    <p class="receipt-sub">We couldn't find an order matching that ID.</p>
<?php else: ?>
    <h2>MakanQR</h2>
    <p class="receipt-sub">Official Receipt</p>

    <div class="receipt-meta">Order: <?php echo htmlspecialchars($order['order_number']); ?></div>
    <div class="receipt-meta">Date: <?php echo htmlspecialchars($order['created_at']); ?></div>
    <?php if (!empty($order['table_number'])): ?>
        <div class="receipt-meta">Table: <?php echo htmlspecialchars($order['table_number']); ?></div>
    <?php endif; ?>
    <?php if (!empty($order['phone_number'])): ?>
        <div class="receipt-meta">Phone: <?php echo htmlspecialchars($order['phone_number']); ?></div>
    <?php endif; ?>
    <div class="receipt-meta">
        Payment: <?php echo $paymentLabels[$order['payment_method'] ?? 'cash'] ?? htmlspecialchars($order['payment_method'] ?? 'cash'); ?>
        (<?php echo ($order['payment_status'] ?? 'unpaid') === 'paid' ? 'Paid ✅' : (($order['payment_method'] ?? 'cash') === 'cash' ? 'Pay at counter' : 'Unpaid'); ?>)
    </div>

    <div class="receipt-divider"></div>

    <?php foreach ($items as $item): ?>
        <div class="receipt-row">
            <span><?php echo htmlspecialchars($item['food_name']); ?> ×<?php echo $item['quantity']; ?></span>
            <span>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
        </div>
    <?php endforeach; ?>

    <div class="receipt-divider"></div>

    <div class="receipt-row">
        <span>Subtotal</span>
        <span>RM <?php echo number_format($subtotal, 2); ?></span>
    </div>
    <div class="receipt-row">
        <span>Service Charge (5%)</span>
        <span>RM <?php echo number_format($serviceCharge, 2); ?></span>
    </div>

    <div class="receipt-divider"></div>

    <div class="receipt-row total">
        <span>Total</span>
        <span>RM <?php echo number_format($order['total'], 2); ?></span>
    </div>

    <?php if (!empty($order['remarks'])): ?>
        <div class="receipt-divider"></div>
        <div class="receipt-meta">Remarks: <?php echo htmlspecialchars($order['remarks']); ?></div>
    <?php endif; ?>

    <div class="receipt-divider"></div>
    <p class="receipt-sub">Thank you for ordering with MakanQR!</p>
<?php endif; ?>
</div>

<div class="receipt-actions">
    <?php if ($order): ?>
        <button onclick="window.print()">🖨️ Print / Save as PDF</button>
        <a href="status.php">View Order Status</a>
    <?php endif; ?>
    <a href="menu.php">Back to Menu</a>
</div>

</body>
</html>
