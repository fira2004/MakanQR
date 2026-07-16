document.addEventListener("DOMContentLoaded", function() {
    const tableInput = document.getElementById("tableNumber");
    if (tableInput) {
        tableInput.value = localStorage.getItem("tableNumber") || "";
    }
    const phoneInput = document.getElementById("phoneNumber");
    if (phoneInput) {
        phoneInput.value = localStorage.getItem("phoneNumber") || "";
    }
    loadCheckoutSummary();
});

function loadCheckoutSummary() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const itemsBody = document.getElementById("checkout-items");
    const subtotalEl = document.getElementById("subtotal");
    const serviceChargeEl = document.getElementById("serviceCharge");
    const grandTotalEl = document.getElementById("grandTotal");

    if (!itemsBody) return;

    if (cart.length === 0) {
        itemsBody.innerHTML = `<tr><td colspan="4">Your cart is empty.</td></tr>`;
        if (subtotalEl) subtotalEl.innerText = "RM 0.00";
        if (serviceChargeEl) serviceChargeEl.innerText = "RM 0.00";
        if (grandTotalEl) grandTotalEl.innerText = "RM 0.00";
        return;
    }

    let subtotal = 0;
    itemsBody.innerHTML = cart.map(item => {
        const lineTotal = item.price * item.quantity;
        subtotal += lineTotal;
        return `
            <tr>
                <td>${item.name}</td>
                <td>RM ${item.price.toFixed(2)}</td>
                <td>${item.quantity}</td>
                <td>RM ${lineTotal.toFixed(2)}</td>
            </tr>
        `;
    }).join('');

    const serviceCharge = subtotal * 0.05;
    const grandTotal = subtotal + serviceCharge;

    if (subtotalEl) subtotalEl.innerText = "RM " + subtotal.toFixed(2);
    if (serviceChargeEl) serviceChargeEl.innerText = "RM " + serviceCharge.toFixed(2);
    if (grandTotalEl) grandTotalEl.innerText = "RM " + grandTotal.toFixed(2);
}

async function placeOrder() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const tableNumber = document.getElementById("tableNumber")?.value || "";
    const phoneNumber = document.getElementById("phoneNumber")?.value || "";
    const remarks = document.getElementById("remarks")?.value || "";
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || "cash";
    const errorEl = document.getElementById("paymentError");
    if (errorEl) errorEl.innerText = "";

    if (cart.length === 0) {
        alert("Cart is empty!");
        return;
    }

    // Calculate total (subtotal + 5% service charge, matching what's shown on screen)
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const total = subtotal * 1.05;

    localStorage.setItem("phoneNumber", phoneNumber);

    if (paymentMethod === "card") {
        await placeOrderWithCard(cart, tableNumber, phoneNumber, remarks, total, errorEl);
    } else if (paymentMethod === "tng") {
        await placeOrderWithTng(cart, tableNumber, phoneNumber, remarks, total, errorEl);
    } else {
        await placeOrderWithCash(cart, tableNumber, phoneNumber, remarks, total, errorEl);
    }
}

async function placeOrderWithCash(cart, tableNumber, phoneNumber, remarks, total, errorEl) {
    try {
        const response = await fetch("placeOrder.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ items: cart, total: total, tableNumber: tableNumber, phoneNumber: phoneNumber, remarks: remarks })
        });

        const responseText = await response.text();
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            alert("Server error: " + responseText);
            return;
        }

        if (result.success) {
            const orders = JSON.parse(localStorage.getItem("orders")) || [];
            orders.push({
                id: result.order_id,
                order_number: result.order_number,
                total: total,
                status: "Pending",
                time: new Date().toLocaleString(),
                items: cart,
                tableNumber: tableNumber,
                remarks: remarks,
                estimated_waiting_time: result.estimated_waiting_time,
                paymentMethod: "cash"
            });
            localStorage.setItem("orders", JSON.stringify(orders));
            localStorage.removeItem("cart");

            alert("Order placed successfully! Order #" + result.order_number +
                  "\nPay in cash on pickup." +
                  "\nEstimated waiting time: " + result.estimated_waiting_time + " minutes");
            window.location.href = "receipt.php?order_id=" + result.order_id;
        } else {
            (errorEl || { innerText: '' }).innerText = result.message || "Failed to place order";
        }
    } catch (error) {
        console.error("Order error:", error);
        (errorEl || { innerText: '' }).innerText = "Failed to place order. Please try again.";
    }
}

async function placeOrderWithCard(cart, tableNumber, phoneNumber, remarks, total, errorEl) {
    try {
        const response = await fetch("createCheckoutSession.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ items: cart, total: total, tableNumber: tableNumber, phoneNumber: phoneNumber, remarks: remarks })
        });

        const responseText = await response.text();
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            alert("Server error: " + responseText);
            return;
        }

        if (result.success && result.checkout_url) {
            // Keep the cart intact until payment is actually confirmed on the
            // success page — if the customer backs out, nothing is lost.
            window.location.href = result.checkout_url;
        } else {
            (errorEl || { innerText: '' }).innerText = result.message || "Failed to start payment";
        }
    } catch (error) {
        console.error("Card payment error:", error);
        (errorEl || { innerText: '' }).innerText = "Failed to start payment. Please try again.";
    }
}

async function placeOrderWithTng(cart, tableNumber, phoneNumber, remarks, total, errorEl) {
    try {
        const response = await fetch("placeOrderTngSim.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ items: cart, total: total, tableNumber: tableNumber, phoneNumber: phoneNumber, remarks: remarks })
        });

        const responseText = await response.text();
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            alert("Server error: " + responseText);
            return;
        }

        if (result.success) {
            window.location.href = "tngSimulate.php?order_id=" + result.order_id;
        } else {
            (errorEl || { innerText: '' }).innerText = result.message || "Failed to start Touch 'n Go payment";
        }
    } catch (error) {
        console.error("TnG payment error:", error);
        (errorEl || { innerText: '' }).innerText = "Failed to start payment. Please try again.";
    }
}