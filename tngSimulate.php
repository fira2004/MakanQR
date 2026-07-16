<?php
include "db.php";

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$order = null;

if ($orderId && $conn) {
    $result = mysqli_query($conn, "SELECT * FROM orders WHERE id = $orderId AND payment_method = 'tng'");
    $order = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Touch 'n Go eWallet Payment | MakanQR</title>
<link rel="stylesheet" href="style.css">
<style>
    .tng-wrap {
        max-width: 400px;
        margin: 40px auto;
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        text-align: center;
    }
    .sim-badge {
        display: inline-block;
        background: #fde68a;
        color: #78350f;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        margin-bottom: 16px;
    }
    .tng-amount {
        font-size: 2rem;
        font-weight: 800;
        color: #0F172A;
        margin: 10px 0;
    }
    .tng-order-id {
        color: #6b7280;
        font-size: 0.9rem;
        margin-bottom: 24px;
    }
    .tng-open-btn {
        display: block;
        width: 100%;
        padding: 14px;
        background: #0066B3;
        color: white;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 12px;
    }
    .tng-open-btn:hover { background: #004f8a; }
    .tng-cancel-link {
        color: #6b7280;
        font-size: 0.9rem;
        text-decoration: underline;
    }

    .tng-modal-btn {
        display: block;
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 10px;
    }
    .tng-confirm-btn { background: #10b981; color: white; }
    .tng-modal-cancel-btn { background: #e5e7eb; color: #0F172A; }
</style>
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="images/logo.png" alt="MakanQR" class="logo-image"></a>
    </div>
</header>

<div class="tng-wrap">
<?php if (!$order): ?>
    <h2>Order not found</h2>
    <p><a href="cart.php">Back to Cart</a></p>
<?php elseif ($order['payment_status'] === 'paid'): ?>
    <h2>✅ Already Paid</h2>
    <p><a href="receipt.php?order_id=<?php echo $orderId; ?>" class="btn-primary">View Receipt</a></p>
<?php else: ?>
    <span class="sim-badge">🧪 No live merchant session — this opens the real TNG app/site, but won't process a real charge</span>
    <h2>Touch 'n Go eWallet</h2>
    <div class="tng-amount">RM <?php echo number_format($order['total'], 2); ?></div>
    <div class="tng-order-id">Order <?php echo htmlspecialchars($order['order_number']); ?></div>

    <a href="https://payment.tngdigital.com.my" onclick="openTNGApp()" target="_blank" rel="noopener" class="tng-open-btn">
        Open TNG eWallet
    </a>

    <p style="font-size:0.85rem; color:#6b7280; margin-bottom:16px;">
        Opens in a new tab/app. Come back here once you're done and confirm below.
    </p>

    <button class="tng-modal-btn tng-confirm-btn" onclick="confirmTngPayment()">I've Completed Payment in TNG</button>
    <a href="tngSimulateCancel.php?order_id=<?php echo $orderId; ?>" class="tng-cancel-link">Cancel Payment</a>
<?php endif; ?>
</div>

<script>
const orderId = <?php echo json_encode($orderId); ?>;

function openTNGApp() {
    // Real navigation to TnG's domain — on a phone with the app installed,
    // the OS (Universal Links / App Links) will typically hand this off to
    // the app itself. There's no live merchant session attached though, so
    // nothing gets charged; this only launches the app/site.
}

async function confirmTngPayment() {
    try {
        const res = await fetch("simulateTngConfirm.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ order_id: orderId })
        });
        const result = await res.json();

        if (result.success) {
            const orders = JSON.parse(localStorage.getItem("orders")) || [];
            if (!orders.some(o => String(o.id) === String(orderId))) {
                orders.push({
                    id: orderId,
                    status: "Pending",
                    time: new Date().toLocaleString(),
                    items: [],
                    paymentMethod: "tng"
                });
                localStorage.setItem("orders", JSON.stringify(orders));
            }
            localStorage.removeItem("cart");
            window.location.href = "receipt.php?order_id=" + orderId;
        } else {
            alert(result.message || "Payment confirmation failed.");
        }
    } catch (error) {
        console.error("TnG confirm error:", error);
        alert("Something went wrong. Please try again.");
    }
}
</script>

</body>
</html>
