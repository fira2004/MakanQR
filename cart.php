<?php
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart | SmartCanteen AI</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="images/logo.png" alt="MakanQR" class="logo-image"></a>  
    </div>
    <nav>
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="cart.php" class="active">Cart</a>
        <a href="recommendation.php">AI Picks</a>
    </nav>
</header>

<section class="cart-page">
    <h1>Your Cart</h1>
    
    <!-- SINGLE cart container -->
    <div id="cartItems"></div>
    
    <div class="cart-summary">
        <h2>Total: RM <span id="totalPrice">0.00</span></h2>
        <button onclick="goCheckout()">Checkout</button>
    </div>
</section>

<script src="cart.js"></script>
</body>
</html>