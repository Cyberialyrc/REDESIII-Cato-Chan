<?php
$host = "localhost";
$user = "root";
$password = "admin";
$dbname = "cato_chan";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>