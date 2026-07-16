let foods = [];
const menu = document.getElementById("menu");

async function loadFoods() {
    try {
        console.log("Loading foods...");
        
        const response = await fetch("getFoods.php");
        console.log("Response status:", response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log("Data received:", data);
        
        // Check if we got an error message
        if (data.error) {
            console.error("Server error:", data.error);
            menu.innerHTML = `<p style="color:red; text-align:center; padding:40px;">Error: ${data.error}</p>`;
            return;
        }
        
        // Check if we got a message (no foods)
        if (data.message) {
            menu.innerHTML = `<p style="text-align:center; padding:40px;">${data.message}</p>`;
            return;
        }
        
        // Check if data is an array and has items
        if (!Array.isArray(data) || data.length === 0) {
            menu.innerHTML = `<p style="text-align:center; padding:40px;">No menu items available.</p>`;
            return;
        }
        
        foods = data;
        displayMenu(foods);
        if (typeof loadCartDrawer === 'function') loadCartDrawer();
        if (typeof loadCart === 'function') loadCart();
        
    } catch (error) {
        console.error("Error loading foods:", error);
        menu.innerHTML = `<p style="color:red; text-align:center; padding:40px;">Failed to load menu: ${error.message}</p>`;
    }
}

function displayMenu(items) {
    if (!menu) return;
    
    if (!items || items.length === 0) {
        menu.innerHTML = "<p style='text-align:center; padding:40px;'>No items to display.</p>";
        return;
    }
    
    const htmlString = items.map(food => `
        <div class="feature-card">
            <div class="image-wrapper">
                <img src="${food.foodImage || 'images/default.png'}" 
                     alt="${food.name}" 
                     class="foodImage"
                     onerror="this.src='images/default.png'">
            </div>
            <h3>${food.name}</h3>
            <p>${food.desc || ''}</p>
            <h4>RM ${parseFloat(food.price || 0).toFixed(2)}</h4>
            <button onclick="addToCart('${food.id}')">
                Add To Cart
            </button>
        </div>
    `).join("");

    menu.innerHTML = htmlString;
}

function addToCart(foodId) {
    const food = foods.find(f => f.id == foodId);
    if (!food) {
        alert("Food not found!");
        return;
    }

    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    
    const existingItem = cart.find(item => item.id == foodId);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: food.id,
            name: food.name,
            price: parseFloat(food.price),
            quantity: 1,
            image: food.foodImage || ""
        });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    
    // Reload cart displays
    if (typeof loadCartDrawer === 'function') loadCartDrawer();
    if (typeof loadCart === 'function') loadCart();
    
    alert(`${food.name} added to cart!`);
}

// Load on page load
document.addEventListener("DOMContentLoaded", function() {
    loadFoods();
});

// Filter function
function filterMenu(category) {
    if (category === "all") {
        displayMenu(foods);
        return;
    }

    const filteredItems = foods.filter(item => {
        if (category === "food") {
            return item.category === "rice-set" || item.category === "noodles";
        }
        if (category === "drink") {
            return item.category === "drinks";
        }
        return false;
    });

    displayMenu(filteredItems);
}