<?php
header('Content-Type: application/json');
session_start(); // Iniciar la sesión

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'cato_chan';
$username = 'root';
$password = 'admin';

// Conectar a la base de datos usando PDO
try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $usuario = $input['usuario'];
    $passwordCifrada = $input['password'];

    // Obtener clave privada y descifrar contraseña
    $clavePrivada = openssl_pkey_get_private(file_get_contents('private_key.pem'));
    if (!$clavePrivada) {
        echo json_encode(['error' => 'No se pudo cargar la clave privada']);
        exit;
    }

    $descifradoExitoso = openssl_private_decrypt(
        base64_decode($_POST['password']),
        $passwordDescifrada,
        $clavePrivada
    );
    if (!$descifradoExitoso) {
        die('Error al descifrar la contraseña');
    }

    // Consulta para validar usuario por correo o DNI
    $query = $conexion->prepare(
        "SELECT id, nombre, password FROM usuarios WHERE correo = :usuario OR dni = :usuario"
    );
    $query->bindParam(':usuario', $usuario);
    $query->execute();
    $usuarioEncontrado = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuarioEncontrado) {
        // Verificar contraseña
        if (password_verify($passwordDescifrada, $usuarioEncontrado['password'])) {
            $_SESSION['usuario_id'] = $usuarioEncontrado['id'];
            $_SESSION['usuario_nombre'] = $usuarioEncontrado['nombre'];

            echo json_encode(['message' => 'Login exitoso', 'nombre' => $usuarioEncontrado['nombre']]);
        } else {
            echo json_encode(['error' => 'Contraseña incorrecta']);
        }
    } else {
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
}
?>
