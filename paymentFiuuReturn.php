<?php
include "db.php";
include "config_fiuu.php";

// Fiuu sends these back as GET or POST depending on integration settings —
// merge both so either works.
$params = array_merge($_GET, $_POST);

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$tranID = $params['tranID'] ?? '';
$orderid = $params['orderid'] ?? '';
$status = $params['status'] ?? '';
$domain = $params['domain'] ?? FIUU_MERCHANT_ID;
$amount = $params['amount'] ?? '';
$currency = $params['currency'] ?? 'MYR';
$receivedSkey = $params['skey'] ?? '';

$message = '';
$success = false;
$orderNumber = '';

if ($orderId && $tranID && $receivedSkey) {
    // ⚠️ CONFIRM THIS: this is the classic Fiuu/MOLPay v13.x Hosted Payment
    // Page "skey" formula. Cross-check the exact field order against the
    // "Payment Response Parameter" section of the API Spec PDF Fiuu sends
    // you — payment gateways occasionally revise this.
    $expectedSkey = md5(md5($tranID . $orderid . $status . $domain . $amount . $currency) . FIUU_SECRET_KEY);

    if ($expectedSkey === $receivedSkey && $status === '00') {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET payment_status = 'paid' WHERE id = ? AND order_number = ?");
        mysqli_stmt_bind_param($stmt, "is", $orderId, $orderid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $orderNumber = $orderid;
        $success = true;
        $message = "Payment successful! Your order is now in the kitchen queue.";
    } elseif ($expectedSkey !== $receivedSkey) {
        $message = "Could not verify this payment's authenticity. If you were charged, please contact the vendor with your order number.";
    } else {
        $message = "Payment was not completed (status: " . htmlspecialchars($status) . "). If you were charged, please contact the vendor.";
    }
} else {
    $message = "Missing payment confirmation details.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Result | MakanQR</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="images/logo.png" alt="MakanQR" class="logo-image"></a>
    </div>
</header>

<section class="dashboard" style="text-align:center;">
    <h1><?php echo $success ? '✅ Payment Successful' : '⚠️ Payment Status'; ?></h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <?php if ($success): ?>
        <p><strong>Order <?php echo htmlspecialchars($orderNumber); ?></strong></p>
        <p>
            <a href="receipt.php?order_id=<?php echo (int)$orderId; ?>" class="btn-primary">View Receipt</a>
            <a href="status.php" class="btn-primary">View Order Status</a>
        </p>

        <script>
            const orders = JSON.parse(localStorage.getItem("orders")) || [];
            if (!orders.some(o => String(o.id) === String(<?php echo json_encode($orderId); ?>))) {
                orders.push({
                    id: <?php echo json_encode($orderId); ?>,
                    order_number: <?php echo json_encode($orderNumber); ?>,
                    status: "Pending",
                    time: new Date().toLocaleString(),
                    items: [],
                    paymentMethod: "tng"
                });
                localStorage.setItem("orders", JSON.stringify(orders));
            }
            localStorage.removeItem("cart");
        </script>
    <?php else: ?>
        <p><a href="cart.php" class="btn-primary">Back to Cart</a></p>
    <?php endif; ?>
</section>

</body>
</html>
