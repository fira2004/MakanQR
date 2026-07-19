<?php
session_start();
include "db.php";
?>
<?php
$table = $_GET['table'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | MakanQR</title>

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
        <a href="index.php">Home</a>
        <a href="menu.php" class="active">Menu</a>
        <a href="cart.php">Cart</a>
        <a href="recommendation.php">AI Picks</a>
        <div class="cart-btn" onclick="toggleCart()">
    🛒 <span id="cartCount">0</span>
</div>
    </nav>
</header>

<section class="menu-page">

    <div class="menu-title">
        <h1>Our Menu</h1>
        <p>Browse our delicious food and beverages.</p>
    </div>

    <div class="filter-buttons">
        <button onclick="filterMenu('all')">All</button>
        <button onclick="filterMenu('food')">Food</button>
        <button onclick="filterMenu('drink')">Drinks</button>
    </div>

    <div class="menu-layout">

    <div class="menu-section">
       <div class="menu-layout">

    <div class="menu-section">
        <div id="menu" class="menu-grid"></div>
    </div>

    <div class="cart-sidebar">

        <h2>Your Cart</h2>

        <div id="cartSidebarItems"></div>

        <div class="cart-sidebar-footer">

            <h3>
                Total: RM
                <span id="sidebarTotal">
                    0.00
                </span>
            </h3>

            <button onclick="goCheckout()">
                Checkout
            </button>
            <script>
const tableNumber = "<?php echo $table; ?>";

if(tableNumber){
    localStorage.setItem("tableNumber", tableNumber);
}
</script>

        </div>

    </div>

</div>
    </div>

</section>

<script src="food.js"></script>
<script src="cart.js"></script>

<!-- Single Cart Drawer - Remove any others -->
<div id="cartDrawer" class="cart-drawer">
    <div class="cart-header">
        <h2>Your Cart</h2>
        <button onclick="closeCart()">✖</button>
    </div>
    <div id="cartItems"></div>
    <div class="cart-footer">
        <h3>Total: RM <span id="cartTotal">0.00</span></h3>
        <button onclick="goCheckout()">Checkout</button>
    </div>
</div>

<!-- Remove any duplicate script tags -->
</body>
</html>