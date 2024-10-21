<?php
header('Content-Type: application/json');
$conexion = new mysqli("localhost", "root", "admin", "cato_chan");

if ($conexion->connect_error) {
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $usuario = $input['usuario'];
    $contenido = $input['contenido'];

    $query = $conexion->prepare("INSERT INTO posts (usuario, contenido) VALUES (?, ?)");
    $query->bind_param("ss", $usuario, $contenido);

    if ($query->execute()) {
        echo json_encode(['message' => 'Post enviado exitosamente']);
    } else {
        echo json_encode(['error' => 'Error al enviar el post']);
    }

    $conexion->close();
}
?>
