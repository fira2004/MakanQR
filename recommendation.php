<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Recommendations</title>

<link rel="stylesheet" href="style.css">
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
        <a href="recommendation.php" class="active">AI Picks</a>
    </nav>
</header>

<section class="recommendation-page">

    <h1>🤖 AI Recommended For You</h1>

    <div id="recommendationContainer"
         class="menu-grid"></div>

</section>

<script src="recommendation.js"></script>
<script src="cart.js"></script>

</body>
</html>