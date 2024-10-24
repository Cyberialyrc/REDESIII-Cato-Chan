<?php
$host = "localhost";
$user = "root";
$password = "admin";
$dbname = "cato_chan";

$conexion = new mysqli($host, $user, $password, $dbname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Clave de encriptación simétrica (guardarla en un entorno seguro).
define('ENCRYPTION_KEY', 'TuClaveSecreta123456'); // Usa una clave de 16, 24 o 32 bytes.
?>
