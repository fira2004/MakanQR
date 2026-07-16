<?php
include "db.php";

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId) {
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id = $orderId");
    mysqli_query($conn, "DELETE FROM orders WHERE id = $orderId AND payment_status = 'unpaid' AND payment_method = 'tng'");
}

header("Location: cart.php");
exit;
