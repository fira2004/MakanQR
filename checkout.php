<?php
session_start();    
include "db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | MakanQR</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php">
            <img src="images/logo.png" alt="MakanQR Logo" class="logo-image">
        </a>
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="cart.php">Cart</a>
    </nav>
</header>
 <section class="checkout-page">

    <h1>Checkout</h1>

    <div class="checkout-layout">

        <div class="checkout-left">

            <table class="checkout-table">

                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Line Total</th>
                    </tr>
                </thead>

                <tbody id="checkout-items">
                </tbody>

            </table>

        </div>
        <div id="checkoutSummary"></div>
        

        <div class="checkout-right">

            <div class="checkout-card">

              <label for="tableNumber">Table Number</label>
<input
    type="text"
    id="tableNumber"
    readonly>
                <label for="phoneNumber">Phone Number</label>
                <input
                    type="tel"
                    id="phoneNumber"
                    placeholder="e.g. 0123456789">
                <label>Remarks</label>
                <textarea
                    id="remarks"
                    rows="4"></textarea>

                <div class="price-row">
                    <span>Subtotal</span>
                    <span id="subtotal">
                        RM 0.00
                    </span>
                </div>

                <div class="price-row">
                    <span>Service Charge (5%)</span>
                    <span id="serviceCharge">
                        RM 0.00
                    </span>
                </div>

                <div class="price-row total">
                    <span>Total</span>
                    <span id="grandTotal">
                        RM 0.00
                    </span>
                </div>

                <h3 style="margin-top:20px; margin-bottom:10px;">Payment Method</h3>

                <label class="payment-option">
                    <input type="radio" name="paymentMethod" value="cash" checked>
                    💵 Cash on Pickup
                </label>

                <label class="payment-option">
                    <input type="radio" name="paymentMethod" value="card">
                    💳 Pay Online (Card)
                </label>

                <label class="payment-option">
                    <input type="radio" name="paymentMethod" value="tng">
                    📱 Touch 'n Go eWallet
                </label>

                <div id="paymentError" style="color:red;"></div>

                <button
                    class="place-order-btn"
                    onclick="placeOrder()">
                    Place Order
                </button>

            </div>

        </div>

    </div>

</section>

<script src="checkout.js"></script>

</body>
</html>