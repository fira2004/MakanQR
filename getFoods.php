<?php
include "db.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check connection
if (!$conn) {
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

$sql = "SELECT * FROM foods WHERE availability = 'Available'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die(json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]));
}

$foods = [];

while($row = mysqli_fetch_assoc($result)) {
    $foods[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "desc" => $row["description"],
        "price" => $row["price"],
        "foodImage" => $row["image"],
        "category" => $row["category"],
        "availability" => $row["availability"]
    ];
}

// If no foods found, return a message
if (empty($foods)) {
    echo json_encode(['message' => 'No foods found in the database']);
} else {
    header("Content-Type: application/json");
    echo json_encode($foods);
}
?>