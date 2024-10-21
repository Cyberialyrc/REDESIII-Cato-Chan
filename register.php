<?php
$conexion = new mysqli("localhost", "root", "admin", "cato_chan");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $dni = $_POST['dni'];

    // Desencriptamos la contraseña recibida usando la clave privada
    $clavePrivada = openssl_pkey_get_private(file_get_contents(__DIR__ . '/keys/private_key.pem'));
    if (!$clavePrivada) {
        die('No se pudo cargar la clave privada');
    }

    openssl_private_decrypt(
        base64_decode($_POST['password']),
        $passwordDescifrada,
        $clavePrivada
    );

    // Hasheamos la contraseña antes de guardarla
    $passwordHasheada = password_hash($passwordDescifrada, PASSWORD_BCRYPT);

    // Insertamos los datos en la base de datos
    $query = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, correo, dni, password) VALUES (?, ?, ?, ?, ?)");
    $query->bind_param("sssss", $nombre, $apellido, $correo, $dni, $passwordHasheada);

    if ($query->execute()) {
        echo json_encode(['message' => 'Usuario registrado exitosamente']);
    } else {
        echo json_encode(['error' => 'Error al registrar usuario']);
    }

    $conexion->close(); // Cerramos la conexión
}
?>