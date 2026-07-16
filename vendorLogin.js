document.getElementById("loginForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const errorEl = document.getElementById("loginError");
    errorEl.innerText = "";

    try {
        const res = await fetch("vendorLogin.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ username, password })
        });
        const result = await res.json();

        if (result.success) {
            window.location.href = "vendor.php";
        } else {
            errorEl.innerText = result.message || "Login failed.";
        }
    } catch (error) {
        console.error("Login error:", error);
        errorEl.innerText = "Something went wrong. Please try again.";
    }
});
