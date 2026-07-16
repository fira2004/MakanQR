<?php
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Status | SmartCanteen AI</title>
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
        <a href="cart.php">Cart</a>
        <a href="recommendation.php">AI Picks</a>
    </nav>
</header>

<section class="status-page">
    <div class="status-header">
        <h1>📦 Order Status</h1>
        <p>Track your orders in real-time</p>
    </div>
    
    <div id="ordersContainer" class="orders-container"></div>
</section>

<footer>
    <p>© 2026 SmartCanteen AI | Final Year Project</p>
</footer>

<script src="status.js"></script>
</body>
</html>