<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-with, Content-type, Authorization');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS');

$serverName = "localhost";
$dbName = "cont1";
$userName = "root";
$password = "";

$conn = mysqli_connect($serverName, $userName, $password, $dbName);

if (!$conn) {
    die("Error: " . mysqli_connect_error());
}
?>
