<?php
include 'conexion.php';
session_start();

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifica si se accede mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['usuario']) || !isset($input['password'])) {
        echo json_encode(['error' => 'Datos de inicio de sesión no válidos']);
        exit();
    }

    $usuario = $input['usuario'];
    $password = $input['password']; // Aquí recibimos la contraseña en texto plano

    try {
        // Preparar la consulta para buscar el usuario
        $query = $conexion->prepare(
            "SELECT id, nombre, password FROM usuarios WHERE correo = ? OR dni = ?"
        );
        $query->bind_param('ss', $usuario, $usuario);
        $query->execute();
        $result = $query->get_result();
        $usuarioEncontrado = $result->fetch_assoc();

        if ($usuarioEncontrado) {
            // Verifica la contraseña
            if (password_verify($password, $usuarioEncontrado['password'])) { // Verifica directamente la contraseña
                $_SESSION['usuario_id'] = $usuarioEncontrado['id'];
                $_SESSION['usuario_nombre'] = $usuarioEncontrado['nombre'];
                echo json_encode(['message' => 'Login exitoso', 'nombre' => $usuarioEncontrado['nombre']]);
            } else {
                echo json_encode(['error' => 'Contraseña incorrecta']);
            }
        } else {
            echo json_encode(['error' => 'Usuario no encontrado']);
        }

        // Cerrar la consulta
        $query->close();
    } catch (Exception $e) {
        echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
    }

    // Cerrar la conexión
    $conexion->close();
    exit(); // Asegúrate de salir después de procesar la solicitud
}

// Si accedes directamente, no haces nada
http_response_code(405); // Método no permitido
echo json_encode(['error' => 'Método no permitido.']);
?>
