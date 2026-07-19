async function loadVendorOrders() {
    const container = document.getElementById("vendorOrders");
    if (!container) return;

    try {
        const res = await fetch("getOrders.php");
        const data = await res.json();
        const orders = data.orders || [];

        if (orders.length === 0) {
            container.innerHTML = "<h2>No orders available.</h2>";
            return;
        }

        container.innerHTML = orders.map(order => `
            <div class="order-card">
                <h3>Order ${order.order_number || ('#' + order.id)}</h3>
                ${order.table_number ? `<p>Table: ${order.table_number}</p>` : ''}
                ${order.phone_number ? `<p>📞 ${order.phone_number}</p>` : ''}
                ${order.remarks ? `<p>📝 ${order.remarks}</p>` : ''}

                <div class="order-items">
                    ${(order.items || []).map(item => `
                        <div>${item.food_name} × ${item.quantity}</div>
                    `).join('')}
                </div>

                <p>Total: RM ${parseFloat(order.total).toFixed(2)}</p>
                <p>Status: <span class="status-badge ${order.status.toLowerCase()}">${order.status}</span></p>
                <p>${order.payment_method === 'card' ? '💳 Paid Online' : order.payment_method === 'tng' ? '📱 Paid via TnG eWallet' : '💵 Cash on Pickup'}</p>
                ${order.estimated_waiting_time != null ? `<p>⏱️ Estimated wait: ${order.estimated_waiting_time} min</p>` : ''}

                <button
                    class="${order.status === 'Preparing' ? 'status-btn-active preparing' : ''}"
                    ${order.status === 'Preparing' ? 'disabled' : ''}
                    onclick="updateStatus(${order.id}, 'Preparing')">
                    ${order.status === 'Preparing' ? '✓ Preparing' : 'Preparing'}
                </button>
                <button
                    class="${order.status === 'Ready' ? 'status-btn-active ready' : ''}"
                    ${order.status === 'Ready' ? 'disabled' : ''}
                    onclick="updateStatus(${order.id}, 'Ready')">
                    ${order.status === 'Ready' ? '✓ Ready' : 'Ready'}
                </button>
                <button
                    class="${order.status === 'Completed' ? 'status-btn-active completed' : ''}"
                    ${order.status === 'Completed' ? 'disabled' : ''}
                    onclick="updateStatus(${order.id}, 'Completed')">
                    ${order.status === 'Completed' ? '✓ Completed' : 'Completed'}
                </button>
            </div>
        `).join('');

    } catch (error) {
        console.error("Failed to load vendor orders:", error);
        container.innerHTML = "<h2>Failed to load orders.</h2>";
    }
}

async function updateStatus(id, newStatus) {
    try {
        const res = await fetch("updateOrderStatus.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ order_id: id, status: newStatus })
        });
        const result = await res.json();

        if (result.success) {
            loadVendorOrders();
        } else {
            alert(result.message || "Failed to update order.");
        }
    } catch (error) {
        console.error("Failed to update order:", error);
        alert("Failed to update order.");
    }
}

loadVendorOrders();
// Poll every 10s so new orders appear without a manual refresh
setInterval(loadVendorOrders, 10000);
