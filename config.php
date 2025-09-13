<?php
// Database connection file
$host = "localhost";
$user = "root";      
$pass = "";          
$db   = "furshield";
// $host = "sql213.ezyro.com";
// $user = "ezyro_39932746";      
// $pass = "9fbfce0f20";          
// $db   = "ezyro_39932746_furshield";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
