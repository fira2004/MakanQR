<?php
include "db.php";
header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

$phoneNumber = trim($_GET['phone'] ?? '');

// Only count orders that actually happened: cash orders, or card/TnG orders
// Stripe/the flow has confirmed as paid.
$visibilityFilter = "(o.payment_method = 'cash' OR o.payment_status = 'paid')";

$scores = []; // food_id => score
$hasHistory = false;

if ($phoneNumber !== '') {
    // ---- Personalized path: this customer has ordered before (identified
    // by phone number, which persists across devices/sessions unlike
    // localStorage) ----

    // 1. Frequency score: how many times has this customer ordered each item, ever
    $freqStmt = mysqli_prepare($conn, "
        SELECT oi.food_id, SUM(oi.quantity) AS qty
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE o.phone_number = ? AND $visibilityFilter
        GROUP BY oi.food_id
    ");
    mysqli_stmt_bind_param($freqStmt, "s", $phoneNumber);
    mysqli_stmt_execute($freqStmt);
    $freqResult = mysqli_stmt_get_result($freqStmt);

    while ($row = mysqli_fetch_assoc($freqResult)) {
        $scores[$row['food_id']] = ($scores[$row['food_id']] ?? 0) + ((int)$row['qty'] * 40);
        $hasHistory = true;
    }
    mysqli_stmt_close($freqStmt);

    if ($hasHistory) {
        // 2. Category preference: which categories has this customer favored
        $catStmt = mysqli_prepare($conn, "
            SELECT f.category, SUM(oi.quantity) AS qty
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            JOIN foods f ON f.id = oi.food_id
            WHERE o.phone_number = ? AND $visibilityFilter
            GROUP BY f.category
        ");
        mysqli_stmt_bind_param($catStmt, "s", $phoneNumber);
        mysqli_stmt_execute($catStmt);
        $catResult = mysqli_stmt_get_result($catStmt);

        $categoryCount = [];
        while ($row = mysqli_fetch_assoc($catResult)) {
            $categoryCount[$row['category']] = (int)$row['qty'];
        }
        mysqli_stmt_close($catStmt);

        $allFoodsResult = mysqli_query($conn, "SELECT id, category FROM foods WHERE availability = 'Available'");
        while ($food = mysqli_fetch_assoc($allFoodsResult)) {
            if (isset($categoryCount[$food['category']])) {
                $scores[$food['id']] = ($scores[$food['id']] ?? 0) + ($categoryCount[$food['category']] * 10);
            }
        }

        // 3. Recency boost: items from the same category as their most recent order
        $recentStmt = mysqli_prepare($conn, "
            SELECT oi.food_id, f.category
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            JOIN foods f ON f.id = oi.food_id
            WHERE o.phone_number = ? AND $visibilityFilter
            ORDER BY o.created_at DESC
            LIMIT 5
        ");
        mysqli_stmt_bind_param($recentStmt, "s", $phoneNumber);
        mysqli_stmt_execute($recentStmt);
        $recentResult = mysqli_stmt_get_result($recentStmt);

        $recentCategories = [];
        while ($row = mysqli_fetch_assoc($recentResult)) {
            $recentCategories[] = $row['category'];
        }
        mysqli_stmt_close($recentStmt);

        if (!empty($recentCategories)) {
            $allFoodsResult2 = mysqli_query($conn, "SELECT id, category FROM foods WHERE availability = 'Available'");
            while ($food = mysqli_fetch_assoc($allFoodsResult2)) {
                if (in_array($food['category'], $recentCategories, true)) {
                    $scores[$food['id']] = ($scores[$food['id']] ?? 0) + 25;
                }
            }
        }
    }
}

if (!$hasHistory) {
    // ---- Fallback path: first-time visitor, or no phone number given.
    // Show what's actually popular store-wide instead of nothing. ----
    $popularResult = mysqli_query($conn, "
        SELECT oi.food_id, SUM(oi.quantity) AS qty
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE $visibilityFilter
        GROUP BY oi.food_id
        ORDER BY qty DESC
        LIMIT 20
    ");
    while ($row = mysqli_fetch_assoc($popularResult)) {
        $scores[$row['food_id']] = (int)$row['qty'] * 5;
    }
}

// Fetch full details for the top-scoring foods (only available items)
arsort($scores);
$topIds = array_slice(array_keys($scores), 0, 4);

if (empty($topIds)) {
    // Total cold start - no order history exists anywhere yet. Just show
    // whatever's on the menu so the page isn't empty.
    $fallbackResult = mysqli_query($conn, "SELECT * FROM foods WHERE availability = 'Available' LIMIT 4");
    $recommendations = [];
    while ($row = mysqli_fetch_assoc($fallbackResult)) {
        $row['score'] = 0;
        $recommendations[] = $row;
    }
} else {
    $idsSql = implode(',', array_map('intval', $topIds));
    $foodsResult = mysqli_query($conn, "SELECT * FROM foods WHERE id IN ($idsSql) AND availability = 'Available'");
    $foodsById = [];
    while ($row = mysqli_fetch_assoc($foodsResult)) {
        $foodsById[$row['id']] = $row;
    }

    $recommendations = [];
    foreach ($topIds as $id) {
        if (isset($foodsById[$id])) {
            $food = $foodsById[$id];
            $food['score'] = $scores[$id];
            $recommendations[] = $food;
        }
    }
}

echo json_encode([
    'personalized' => $hasHistory,
    'recommendations' => $recommendations
]);
