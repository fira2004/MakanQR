<?php

$host = "mysql-138e1494-makanqr-project.j.aivencloud.com";
$user = "avnadmin";
$password = "AVNS_zN5VIWRSxhchFB_Wy4S";
$database = "makanqr"; // or your database name
$port = 22922;

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database,
    $port
);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>