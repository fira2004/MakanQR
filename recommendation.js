const orderHistory =
    JSON.parse(localStorage.getItem("orders")) || [];

const recommendationContainer =
    document.getElementById(
        "recommendationContainer"
    );

function generateRecommendations(){

    const scores = {};

    foods.forEach(food => {
        scores[food.id] = 0;
    });

    const allOrderedItems = [];

    orderHistory.forEach(order => {

        order.items.forEach(item => {

            allOrderedItems.push(item);

        });

    });

    /* ----------------------------
       1. FREQUENCY SCORE
    ----------------------------- */

    allOrderedItems.forEach(item => {

        scores[item.id] += 40;

    });

    /* ----------------------------
       2. CATEGORY PREFERENCE
    ----------------------------- */

    const categoryCount = {};

    allOrderedItems.forEach(item => {

        categoryCount[item.category] =
            (categoryCount[item.category] || 0) + 1;

    });

    foods.forEach(food => {

        if(categoryCount[food.category]){

            scores[food.id] +=
                categoryCount[food.category] * 10;

        }

    });

    /* ----------------------------
       3. RECENT ORDER SCORE
    ----------------------------- */

    const latestOrder =
        orderHistory[orderHistory.length - 1];

    if(latestOrder){

        latestOrder.items.forEach(item => {

            foods.forEach(food => {

                if(
                    food.category === item.category
                ){
                    scores[food.id] += 25;
                }

            });

        });

    }

    /* ----------------------------
       4. COMBO MATCHING
    ----------------------------- */

    const combos = {

        "Nestum Chicken Rice":
            "Teh Ais",

        "Ayam Penyet Set Rice":
            "Milo Ais",

        "Lemon Chicken Set Rice":
            "Teh Ais",

        "Tomyam Fried Rice":
            "Milo Ais"
    };

    allOrderedItems.forEach(item => {

        const comboDrink =
            combos[item.name];

        if(comboDrink){

            foods.forEach(food => {

                if(food.name === comboDrink){

                    scores[food.id] += 20;

                }

            });

        }

    });

    /* ----------------------------
       SORT BY SCORE
    ----------------------------- */

    const rankedFoods =
        [...foods]
        .sort(
            (a,b)=>
            scores[b.id]-scores[a.id]
        )
        .slice(0,4);

    displayRecommendations(
        rankedFoods,
        scores
    );
}

function displayRecommendations(
    recommendedFoods,
    scores
){

    recommendationContainer.innerHTML = "";

    recommendedFoods.forEach(food => {

        recommendationContainer.innerHTML += `
        <div class="feature-card">

            ${
                food.foodImage
                ?
                `<img
                src="${food.foodImage}"
                class="foodImage">`
                :
                `<div class="food-icon">
                    ${food.icon}
                </div>`
            }

            <h3>${food.name}</h3>

            <p>${food.desc}</p>

            <h4>
                RM ${food.price.toFixed(2)}
            </h4>

            <p>
                AI Score:
                ${scores[food.id]}
            </p>

        </div>
        `;
    });

}

generateRecommendations();