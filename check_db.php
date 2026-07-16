<?php
// TEMPORARY DIAGNOSTIC — delete this file once the issue is resolved.
// Uses the exact same connection as the rest of the app (db.php) to show
// what database/server it's actually talking to, and whether it can see
// the new columns.
include "db.php";

header("Content-Type: text/plain");

if (!$conn) {
    die("Connection FAILED: " . mysqli_connect_error());
}

echo "=== Connection info ===\n";
$info = mysqli_query($conn, "SELECT DATABASE() AS db, @@hostname AS host, @@port AS port, VERSION() AS version");
$row = mysqli_fetch_assoc($info);
print_r($row);

echo "\n=== Columns in `orders` (as seen by this exact connection) ===\n";
$cols = mysqli_query($conn, "SHOW COLUMNS FROM orders");
if (!$cols) {
    echo "SHOW COLUMNS failed: " . mysqli_error($conn) . "\n";
} else {
    while ($c = mysqli_fetch_assoc($cols)) {
        echo $c['Field'] . " (" . $c['Type'] . ")\n";
    }
}

echo "\n=== db.php connection settings ===\n";
echo "host: $host\n";
echo "user: $user\n";
echo "database: $database\n";
