// cart.js - FIXED VERSION

// Load cart from localStorage
function loadCart() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const container = document.getElementById("cartItems");
    const totalPriceEl = document.getElementById("totalPrice");
    const cartCountEl = document.getElementById("cartCount");

    if (!container) return;

    let total = 0;
    let count = 0;

    container.innerHTML = "";

    if (cart.length === 0) {
        container.innerHTML = "<p>Your cart is empty.</p>";
        if (totalPriceEl) totalPriceEl.innerText = "0.00";
        if (cartCountEl) cartCountEl.innerText = "0";
        return;
    }

    cart.forEach((item, index) => {
        total += item.price * item.quantity;
        count += item.quantity;

        container.innerHTML += `
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
        `;
    });

    if (totalPriceEl) totalPriceEl.innerText = total.toFixed(2);
    if (cartCountEl) cartCountEl.innerText = count;
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

// Load cart on page load
document.addEventListener("DOMContentLoaded", function() {
    loadCart();
});