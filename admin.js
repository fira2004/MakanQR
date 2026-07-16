async function loadAnalytics() {
    try {
        const res = await fetch("getAnalytics.php");
        const data = await res.json();

        document.getElementById("totalOrders").innerText = data.totalOrders ?? 0;
        document.getElementById("totalRevenue").innerText = "RM " + (data.totalRevenue ?? 0).toFixed(2);
        document.getElementById("popularFood").innerText = data.popularFood || "-";
    } catch (error) {
        console.error("Failed to load analytics:", error);
    }
}

loadAnalytics();
setInterval(loadAnalytics, 30000);
