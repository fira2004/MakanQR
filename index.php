<?php
session_start();
include "db.php";   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MakanQR</title>

    <link rel="stylesheet" href="style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="images/logo.png" alt="MakanQR" class="logo-image"></a>  
    </div>

    <nav>
        <a href="index.php" class="active">Home</a>
        <a href="menu.php">Menu</a>
        <a href="cart.php">Cart</a>
        <a href="recommendation.php">AI Picks</a>
    </nav>
</header>
<section class="hero">

    <div class="hero-content">

        <h1>Order Smarter.<br>Eat Faster.</h1>

        <p>
            Skip long queues and enjoy a seamless food ordering experience
            powered by AI recommendations and smart ordering technology.
        </p>

        <div class="hero-buttons">
            <a href="menu.php" class="btn-primary">View Menu</a>
            <a href="recommendation.php" class="btn-secondary">AI Recommendations</a>
        </div>

    </div>

    <div class="hero-image">

        <div class="food-card">
            🍗
        </div>

        <div class="food-card">
            🍜
        </div>

        <div class="food-card">
            🧋
        </div>

    </div>

</section>

<section class="features">

    <h2>Why Choose SmartCanteen AI?</h2>

    <div class="feature-grid">

        <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h3>QR Ordering</h3>
            <p>Scan, order and pay directly from your phone.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🤖</div>
            <h3>AI Recommendation</h3>
            <p>Get personalised food suggestions based on preferences.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>Fast Service</h3>
            <p>Reduce waiting time and improve customer experience.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Analytics</h3>
            <p>Monitor popular menu items and sales performance.</p>
        </div>

    </div>

</section>

<section class="popular-food">

    <h2>Popular Meals</h2>

    <div class="food-grid">

        <div class="food-preview">
            <img src="images/NC2.png" alt="Nestum Chicken">
            <h3>Nestum Chicken Rice</h3>
            <p>RM12.00</p>
        </div>

        <div class="food-preview">
            <img src="images/NAP1.png" alt="Ayam Penyet">
            <h3>Ayam Penyet Set Rice</h3>
            <p>RM11.00</p>
        </div>

        <div class="food-preview">
            <img src="images/LC1.png" alt="Lemon Chicken">
            <h3>Lemon Chicken Rice</h3>
            <p>RM11.00</p>
        </div>

    </div>

</section>

<section class="cta">

    <h2>Ready To Order?</h2>

    <p>
        Browse our menu and let MakanQR recommend your next favourite meal.
    </p>

    <a href="menu.php" class="btn-primary">
        Start Ordering
    </a>

</section>

<footer>

    <p>
        © 2026 SmartCanteen AI | Final Year Project
    </p>

    <p>
        <a href="vendor_login.php" style="color: #FFFF00; font-size: 0.85em;">Vendor Login</a>
    </p>

</footer>

</body>
</html>