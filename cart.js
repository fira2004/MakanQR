// Updates every cart UI element present on the current page - the always-
// visible sidebar (menu.php's "Your Cart" panel), the slide-out drawer,
// and the header cart-icon count badge. Whichever of these exist on the
// page get updated; missing ones are just skipped.
function loadCart() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    let total = 0;
    let count = 0;
    cart.forEach(item => {
        total += item.price * item.quantity;
        count += item.quantity;
    });

    renderCartInto("cartItems", "cartTotal", cart, total);
    renderCartInto("cartSidebarItems", "sidebarTotal", cart, total);

    const cartCountEl = document.getElementById("cartCount");
    if (cartCountEl) cartCountEl.innerText = count;

    // Legacy id some pages used
    const totalPriceEl = document.getElementById("totalPrice");
    if (totalPriceEl) totalPriceEl.innerText = total.toFixed(2);
}

function renderCartInto(containerId, totalId, cart, total) {
    const container = document.getElementById(containerId);
    const totalEl = document.getElementById(totalId);
    if (!container) return;

    if (cart.length === 0) {
        container.innerHTML = "<p>Your cart is empty.</p>";
        if (totalEl) totalEl.innerText = "0.00";
        return;
    }

    container.innerHTML = cart.map((item, index) => `
        <div class="cart-item">
            <div>
                <h3>${item.name}</h3>
                <p>RM ${item.price.toFixed(2)}</p>
            </div>
            <div class="cart-controls">
                <button onclick="decrease(${index})">-</button>
                <span>${item.quantity}</span>
                <button onclick="increase(${index})">+</button>
                <button onclick="removeItem(${index})">Remove</button>
            </div>
        </div>
    `).join('');

    if (totalEl) totalEl.innerText = total.toFixed(2);
}

function increase(index) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart[index].quantity++;
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
}

function decrease(index) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
    } else {
        cart.splice(index, 1);
    }
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
}

function removeItem(index) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
}

function goCheckout() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    if (cart.length === 0) {
        alert("Your cart is empty!");
        return;
    }
    window.location.href = "checkout.php";
}

function toggleCart() {
    const drawer = document.getElementById("cartDrawer");
    if (drawer) drawer.classList.toggle("open");
}

function closeCart() {
    const drawer = document.getElementById("cartDrawer");
    if (drawer) drawer.classList.remove("open");
}

// Load cart on page load
document.addEventListener("DOMContentLoaded", function() {
    loadCart();
});
