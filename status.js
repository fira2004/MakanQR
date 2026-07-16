async function loadOrders() {
    // We still use localStorage to remember WHICH orders this customer
    // placed (there's no login system), but the actual status/details
    // are always fetched fresh from the database so vendor updates show up.
    const localOrders = JSON.parse(localStorage.getItem("orders")) || [];
    const container = document.getElementById("ordersContainer");

    if (!container) return;

    if (localOrders.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <h2>No orders found.</h2>
                <p>Place your first order from the menu!</p>
                <a href="menu.php" class="btn-primary">Browse Menu</a>
            </div>
        `;
        return;
    }

    const ids = localOrders.map(o => o.id).filter(Boolean).join(",");
    let liveOrders = [];

    try {
        const res = await fetch(`getOrderStatus.php?ids=${ids}`);
        const data = await res.json();
        liveOrders = data.orders || [];
    } catch (error) {
        console.error("Failed to fetch live order status:", error);
    }

    // Merge: live DB fields (status, items, total) win, but keep the
    // human-friendly local timestamp string.
    const merged = localOrders.map(local => {
        const live = liveOrders.find(o => String(o.id) === String(local.id));
        if (!live) return local;
        return {
            ...local,
            status: live.status,
            order_number: live.order_number || local.order_number,
            total: parseFloat(live.total),
            items: (live.items && live.items.length) ? live.items.map(i => ({
                name: i.food_name,
                price: parseFloat(i.price),
                quantity: parseInt(i.quantity, 10)
            })) : local.items,
            tableNumber: live.table_number || local.tableNumber,
            remarks: live.remarks || local.remarks,
            estimated_waiting_time: live.estimated_waiting_time != null ? live.estimated_waiting_time : local.estimated_waiting_time,
            paymentMethod: live.payment_method || local.paymentMethod,
            paymentStatus: live.payment_status || local.paymentStatus
        };
    }).reverse();

    container.innerHTML = "";

    merged.forEach(order => {
        let badgeClass = "";
        let statusIcon = "";

        if (order.status === "Pending") {
            badgeClass = "pending";
            statusIcon = "⏳";
        } else if (order.status === "Preparing") {
            badgeClass = "preparing";
            statusIcon = "👨‍🍳";
        } else if (order.status === "Ready") {
            badgeClass = "ready";
            statusIcon = "🔔";
        } else if (order.status === "Completed") {
            badgeClass = "completed";
            statusIcon = "✅";
        } else {
            badgeClass = "pending";
            statusIcon = "📦";
        }

        const displayId = order.order_number || `#${order.id || Date.now()}`;

        container.innerHTML += `
        <div class="order-card">
            <div class="order-header">
                <h3>Order ${displayId}</h3>
                <span class="status-badge ${badgeClass}">
                    ${statusIcon} ${order.status || "Pending"}
                </span>
            </div>
            <p class="order-time">📅 ${order.time || new Date().toLocaleString()}</p>

            ${order.items ? `
                <div class="order-items">
                    <strong>Items:</strong>
                    ${order.items.map(item => `
                        <div class="order-item">
                            ${item.name} × ${item.quantity}
                            <span class="item-price">RM ${(item.price * item.quantity).toFixed(2)}</span>
                        </div>
                    `).join('')}
                </div>
            ` : ''}

            <div class="order-footer">
                <strong>Total: RM ${(order.total || 0).toFixed(2)}</strong>
                ${order.tableNumber ? `<span>Table: ${order.tableNumber}</span>` : ''}
                ${order.remarks ? `<span>📝 ${order.remarks}</span>` : ''}
                ${order.paymentMethod === 'card' ? `<span>💳 Paid Online</span>` :
                  order.paymentMethod === 'tng' ? `<span>📱 Paid via TnG eWallet</span>` :
                  (order.paymentMethod ? `<span>💵 Cash on Pickup</span>` : '')}
                ${(order.status !== 'Completed' && order.estimated_waiting_time != null) ?
                    `<span>⏱️ Estimated wait: ${order.estimated_waiting_time} min</span>` : ''}
                ${order.id ? `<span><a href="receipt.php?order_id=${order.id}">🧾 View Receipt</a></span>` : ''}
            </div>
        </div>
        `;
    });
}

document.addEventListener("DOMContentLoaded", function() {
    loadOrders();
    // Poll every 15s so status updates from the vendor show up without a manual refresh
    setInterval(loadOrders, 15000);
});
