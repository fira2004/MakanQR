let dailyChart = null;

async function loadAnalytics() {
    try {
        const res = await fetch("getAnalytics.php");
        const data = await res.json();

        document.getElementById("totalOrders").innerText = data.totalOrders ?? 0;
        document.getElementById("totalRevenue").innerText = "RM " + (data.totalRevenue ?? 0).toFixed(2);
        document.getElementById("popularFood").innerText = data.popularFood || "-";

        renderDailySales(data.dailySales || []);
    } catch (error) {
        console.error("Failed to load analytics:", error);
    }
}

function renderDailySales(dailySales) {
    // API returns most-recent-first; charts/tables read better oldest-first (left to right)
    const chronological = [...dailySales].reverse();

    const tbody = document.getElementById("dailySalesBody");
    if (tbody) {
        if (dailySales.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" style="padding:12px; text-align:center;">No sales yet.</td></tr>`;
        } else {
            // Table stays most-recent-first, which reads better for "what happened lately"
            tbody.innerHTML = dailySales.map(day => `
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td style="padding:12px;">${formatDate(day.date)}</td>
                    <td style="padding:12px; text-align:right;">${day.orders}</td>
                    <td style="padding:12px; text-align:right;">RM ${day.revenue.toFixed(2)}</td>
                </tr>
            `).join('');
        }
    }

    const canvas = document.getElementById("dailySalesChart");
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = chronological.map(d => formatDate(d.date));
    const revenueData = chronological.map(d => d.revenue);
    const orderData = chronological.map(d => d.orders);

    if (dailyChart) {
        dailyChart.destroy();
    }

    dailyChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue (RM)',
                    data: revenueData,
                    backgroundColor: '#FBBF24',
                    yAxisID: 'y'
                },
                {
                    label: 'Orders',
                    data: orderData,
                    type: 'line',
                    borderColor: '#0F172A',
                    backgroundColor: '#0F172A',
                    yAxisID: 'y1',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: 'Revenue (RM)' },
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    title: { display: true, text: 'Orders' },
                    beginAtZero: true,
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
}

function formatDate(dateStr) {
    const d = new Date(dateStr + "T00:00:00");
    return d.toLocaleDateString('en-MY', { day: 'numeric', month: 'short' });
}

loadAnalytics();
setInterval(loadAnalytics, 30000);
