const recommendationContainer = document.getElementById("recommendationContainer");

async function generateRecommendations() {
    // The phone number, if this browser has one saved (from scanning a
    // table QR / a previous checkout), lets us pull this customer's real
    // order history from the database - not just what's in localStorage.
    const phoneNumber = localStorage.getItem("phoneNumber") || "";

    if (!recommendationContainer) return;
    recommendationContainer.innerHTML = "<p>Finding your picks...</p>";

    try {
        const res = await fetch("getRecommendations.php?phone=" + encodeURIComponent(phoneNumber));
        const data = await res.json();
        displayRecommendations(data.recommendations || [], data.personalized);
    } catch (error) {
        console.error("Failed to load recommendations:", error);
        recommendationContainer.innerHTML = "<p>Couldn't load recommendations right now.</p>";
    }
}

function displayRecommendations(recommendedFoods, personalized) {
    if (!recommendationContainer) return;

    if (recommendedFoods.length === 0) {
        recommendationContainer.innerHTML = "<p>No recommendations available yet.</p>";
        return;
    }

    const heading = personalized
        ? "<p class='ai-picks-note'>Based on your order history</p>"
        : "<p class='ai-picks-note'>Popular picks right now</p>";

    recommendationContainer.innerHTML = heading + recommendedFoods.map(food => `
        <div class="feature-card">
            ${food.image
                ? `<img src="${food.image}" class="foodImage" onerror="this.src='images/default.png'">`
                : `<div class="food-icon">🍽️</div>`
            }
            <h3>${food.name}</h3>
            <p>${food.description || ''}</p>
            <h4>RM ${parseFloat(food.price).toFixed(2)}</h4>
            <button onclick="addRecommendedToCart(${food.id}, '${food.name.replace(/'/g, "\\'")}', ${food.price}, '${(food.image || '').replace(/'/g, "\\'")}')">
                Add To Cart
            </button>
        </div>
    `).join('');
}

function addRecommendedToCart(id, name, price, image) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    const existingItem = cart.find(item => item.id == id);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ id, name, price: parseFloat(price), quantity: 1, image });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    if (typeof loadCart === 'function') loadCart();
    alert(`${name} added to cart!`);
}

document.addEventListener("DOMContentLoaded", generateRecommendations);
