<?php
$table = $_GET['table'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome | MakanQR</title>
<link rel="stylesheet" href="style.css">
<style>
    .scan-wrap {
        max-width: 400px;
        margin: 60px auto;
        background: white;
        border-radius: 16px;
        padding: 36px 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        text-align: center;
    }
    .scan-wrap .logo-image { width: 90px; margin-bottom: 10px; }
    .scan-table-badge {
        display: inline-block;
        background: #fde68a;
        color: #78350f;
        font-weight: 700;
        padding: 6px 16px;
        border-radius: 999px;
        margin: 12px 0 24px;
    }
    .scan-wrap label {
        display: block;
        text-align: left;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .scan-wrap input {
        width: 100%;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 6px;
        font-size: 1rem;
    }
    .scan-error {
        color: #dc2626;
        font-size: 0.85rem;
        text-align: left;
        min-height: 18px;
        margin-bottom: 10px;
    }
    .scan-wrap button {
        width: 100%;
        padding: 14px;
        background: #0F172A;
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
    }
    .scan-wrap button:hover { background: #FBBF24; color: #0F172A; }
    .scan-note {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 14px;
    }
</style>
</head>
<body>

<div class="scan-wrap">
    <img src="images/logo.png" alt="MakanQR" class="logo-image">
    <h2>Welcome to MakanQR</h2>

    <?php if ($table !== ''): ?>
        <div class="scan-table-badge">Table <?php echo htmlspecialchars($table); ?></div>
    <?php else: ?>
        <p style="color:#6b7280;">No table detected — you can still order for pickup.</p>
    <?php endif; ?>

    <form id="phoneForm">
        <label for="phoneNumber">Your Phone Number</label>
        <input type="tel" id="phoneNumber" placeholder="e.g. 0123456789" inputmode="numeric" required>
        <div class="scan-error" id="phoneError"></div>
        <button type="submit">Continue to Menu</button>
    </form>

    <p class="scan-note">We'll use this to reach you if there's an issue with your order.</p>
</div>

<script>
const tableNumber = <?php echo json_encode($table); ?>;

document.getElementById("phoneForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const phoneInput = document.getElementById("phoneNumber");
    const errorEl = document.getElementById("phoneError");
    const phone = phoneInput.value.trim();

    // Basic Malaysian mobile number sanity check - digits only, 9-11 long
    const digitsOnly = phone.replace(/[\s-]/g, "");
    if (!/^0\d{8,10}$/.test(digitsOnly)) {
        errorEl.innerText = "Please enter a valid phone number (e.g. 0123456789).";
        return;
    }

    localStorage.setItem("phoneNumber", digitsOnly);
    if (tableNumber) {
        localStorage.setItem("tableNumber", tableNumber);
    }

    window.location.href = "menu.php" + (tableNumber ? ("?table=" + encodeURIComponent(tableNumber)) : "");
});
</script>

</body>
</html>
