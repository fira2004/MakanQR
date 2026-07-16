<?php
include "db.php";
include "config_stripe.php";
include "stripe_helper.php";

$sessionId = $_GET['session_id'] ?? '';
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

$message = '';
$success = false;
$orderNumber = '';

if ($sessionId && $orderId) {
    // Verify directly with Stripe rather than trusting the redirect alone —
    // anyone could hit this URL with a fake order_id otherwise.
    $session = stripe_request("checkout/sessions/$sessionId", [], 'GET');

    if (isset($session['payment_status']) && $session['payment_status'] === 'paid') {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET payment_status = 'paid' WHERE id = ? AND stripe_session_id = ?");
        mysqli_stmt_bind_param($stmt, "is", $orderId, $sessionId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $orderResult = mysqli_query($conn, "SELECT order_number FROM orders WHERE id = $orderId");
        $orderRow = mysqli_fetch_assoc($orderResult);
        $orderNumber = $orderRow['order_number'] ?? '';

        $success = true;
        $message = "Payment successful! Your order is now in the kitchen queue.";
    } else {
        $message = "Payment was not completed. If you were charged, please contact the vendor with your order details.";
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
            // Register this order locally so the status page knows to poll for it.
            // Live details (items, total, status, wait time) are fetched fresh from the DB.
            const orders = JSON.parse(localStorage.getItem("orders")) || [];
            if (!orders.some(o => String(o.id) === String(<?php echo json_encode($orderId); ?>))) {
                orders.push({
                    id: <?php echo json_encode($orderId); ?>,
                    order_number: <?php echo json_encode($orderNumber); ?>,
                    status: "Pending",
                    time: new Date().toLocaleString(),
                    items: [],
                    paymentMethod: "card"
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
