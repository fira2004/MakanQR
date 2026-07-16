<?php
// This is Fiuu's server-to-server notification, separate from the customer's
// browser redirect (paymentFiuuReturn.php). It's more reliable because it
// fires even if the customer closes their browser right after paying.
//
// NOTE: this can only be tested once your site is on a real public HTTPS
// domain — Fiuu's servers cannot reach http://localhost. Register this URL
// in the Fiuu portal under Transactions > Settings > Callback URL once deployed.
include "db.php";
include "config_fiuu.php";

$params = array_merge($_GET, $_POST);

$tranID = $params['tranID'] ?? '';
$orderid = $params['orderid'] ?? '';
$status = $params['status'] ?? '';
$domain = $params['domain'] ?? FIUU_MERCHANT_ID;
$amount = $params['amount'] ?? '';
$currency = $params['currency'] ?? 'MYR';
$receivedSkey = $params['skey'] ?? '';

if ($tranID && $orderid && $receivedSkey) {
    $expectedSkey = md5(md5($tranID . $orderid . $status . $domain . $amount . $currency) . FIUU_SECRET_KEY);

    if ($expectedSkey === $receivedSkey && $status === '00') {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET payment_status = 'paid' WHERE order_number = ?");
        mysqli_stmt_bind_param($stmt, "s", $orderid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Fiuu expects a plain acknowledgement response
echo "OK";
