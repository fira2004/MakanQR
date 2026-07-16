let adminFoods = [];

async function loadFoodList() {
    const container = document.getElementById("foodList");
    if (!container) return;

    try {
        const res = await fetch("getFoodsAdmin.php");
        adminFoods = await res.json();

        if (!Array.isArray(adminFoods) || adminFoods.length === 0) {
            container.innerHTML = "<p>No food items yet.</p>";
            return;
        }

        container.innerHTML = adminFoods.map(food => `
            <div class="order-card" id="food-${food.id}">
                <h3>${food.name}</h3>
                <p>${food.description || ''}</p>
                <p>RM ${parseFloat(food.price).toFixed(2)} — ${food.category || 'uncategorized'}</p>
                <p>Status: <strong>${food.availability}</strong></p>

                <button onclick="toggleAvailability(${food.id}, '${food.availability}')">
                    Mark ${food.availability === 'Available' ? 'Not Available' : 'Available'}
                </button>
                <button onclick="showEditForm(${food.id})">Edit</button>
                <button onclick="deleteFood(${food.id})">Delete</button>

                <div id="editForm-${food.id}" style="display:none; margin-top:10px;"></div>
            </div>
        `).join('');

    } catch (error) {
        console.error("Failed to load food list:", error);
        container.innerHTML = "<p>Failed to load food list.</p>";
    }
}

document.getElementById("addFoodForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const errorEl = document.getElementById("addFoodError");
    errorEl.innerText = "";

    const payload = {
        name: document.getElementById("foodName").value,
        desc: document.getElementById("foodDesc").value,
        price: document.getElementById("foodPrice").value,
        category: document.getElementById("foodCategory").value,
        image: document.getElementById("foodImage").value,
        availability: document.getElementById("foodAvailability").value
    };

    try {
        const res = await fetch("addFood.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.success) {
            document.getElementById("addFoodForm").reset();
            loadFoodList();
        } else {
            errorEl.innerText = result.message || "Failed to add food.";
        }
    } catch (error) {
        console.error("Add food error:", error);
        errorEl.innerText = "Something went wrong. Please try again.";
    }
});

function showEditForm(id) {
    const food = adminFoods.find(f => f.id == id);
    if (!food) return;

    const box = document.getElementById(`editForm-${id}`);
    box.style.display = "block";
    box.innerHTML = `
        <p><label>Name<br><input type="text" id="editName-${id}" value="${food.name}"></label></p>
        <p><label>Description<br><textarea id="editDesc-${id}" rows="2">${food.description || ''}</textarea></label></p>
        <p><label>Price (RM)<br><input type="number" step="0.01" min="0" id="editPrice-${id}" value="${food.price}"></label></p>
        <p><label>Image URL/path<br><input type="text" id="editImage-${id}" value="${food.image || ''}"></label></p>
        <button onclick="saveEdit(${id})">Save</button>
        <button onclick="document.getElementById('editForm-${id}').style.display='none'">Cancel</button>
    `;
}

async function saveEdit(id) {
    const food = adminFoods.find(f => f.id == id);
    if (!food) return;

    const payload = {
        id: id,
        name: document.getElementById(`editName-${id}`).value,
        desc: document.getElementById(`editDesc-${id}`).value,
        price: document.getElementById(`editPrice-${id}`).value,
        category: food.category,
        image: document.getElementById(`editImage-${id}`).value,
        availability: food.availability
    };

    try {
        const res = await fetch("updateFood.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.success) {
            loadFoodList();
        } else {
            alert(result.message || "Failed to update food.");
        }
    } catch (error) {
        console.error("Update food error:", error);
        alert("Something went wrong. Please try again.");
    }
}

async function toggleAvailability(id, currentStatus) {
    const food = adminFoods.find(f => f.id == id);
    if (!food) return;

    const newStatus = currentStatus === 'Available' ? 'Not Available' : 'Available';

    try {
        const res = await fetch("updateFood.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id: id,
                name: food.name,
                desc: food.description,
                price: food.price,
                category: food.category,
                image: food.image,
                availability: newStatus
            })
        });
        const result = await res.json();

        if (result.success) {
            loadFoodList();
        } else {
            alert(result.message || "Failed to update availability.");
        }
    } catch (error) {
        console.error("Toggle availability error:", error);
        alert("Something went wrong. Please try again.");
    }
}

async function deleteFood(id) {
    if (!confirm("Delete this food item? This cannot be undone.")) return;

    try {
        const res = await fetch("deleteFood.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });
        const result = await res.json();

        if (result.success) {
            loadFoodList();
        } else {
            alert(result.message || "Failed to delete food. It may be referenced by existing orders.");
        }
    } catch (error) {
        console.error("Delete food error:", error);
        alert("Something went wrong. Please try again.");
    }
}

loadFoodList();
