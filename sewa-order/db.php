<?php
// Database connection file
$host = "localhost";
$user = "root";
$password = "";
$database = "sewa_luna";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed");
}
?>
