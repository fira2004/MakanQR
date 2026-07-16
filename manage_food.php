<?php include "auth_check.php"; require_vendor_login(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Food</title>

<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="images/logo.png" alt="MakanQR" class="logo-image"></a>
        <h2>Manage Food</h2>
    </div>
    <nav>
        <a href="vendor.php">Orders</a>
        <a href="manage_food.php">Manage Food</a>
        <a href="admin.php">Analytics</a>
        <a href="vendorLogout.php">Logout (<?php echo htmlspecialchars($_SESSION['vendor_username']); ?>)</a>
    </nav>
</header>

<section class="dashboard">

    <h1>Add New Food</h1>

    <div id="addFoodError" style="color:red;"></div>

    <form id="addFoodForm" style="max-width:420px;">
        <p><label>Name<br><input type="text" id="foodName" required></label></p>
        <p><label>Description<br><textarea id="foodDesc" rows="2"></textarea></label></p>
        <p><label>Price (RM)<br><input type="number" id="foodPrice" step="0.01" min="0" required></label></p>
        <p><label>Category<br>
            <select id="foodCategory">
                <option value="rice-set">Rice Set</option>
                <option value="noodles">Noodles</option>
                <option value="drinks">Drinks</option>
            </select>
        </label></p>
        <p><label>Image URL/path<br><input type="text" id="foodImage" placeholder="images/example.png"></label></p>
        <p><label>Availability<br>
            <select id="foodAvailability">
                <option value="Available">Available</option>
                <option value="Not Available">Not Available</option>
            </select>
        </label></p>
        <button type="submit">Add Food</button>
    </form>

    <h1>Current Menu</h1>

    <div id="foodList"></div>

</section>

<script src="manage_food.js"></script>

</body>
</html>
