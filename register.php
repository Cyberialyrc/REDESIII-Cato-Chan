<?php
include 'conexion.php'; // Incluye la conexión correctamente

// Inicializa variables para almacenar mensajes
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $dni = trim($_POST['dni']);
    $password = $_POST['password'];

    // Validación de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $message = 'Correo no válido';
        $messageType = 'danger'; // Tipo de mensaje: error
    } else {
        // Verificar si el correo ya existe
        $checkQuery = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = ?");
        $checkQuery->bind_param("s", $correo);
        $checkQuery->execute();
        $checkQuery->bind_result($count);
        $checkQuery->fetch();
        $checkQuery->close();

        if ($count > 0) {
            $message = 'El correo ya está registrado. Por favor, utiliza otro.';
            $messageType = 'danger';
        } else {
            // Hash de la contraseña
            $passwordHasheada = password_hash($password, PASSWORD_BCRYPT);

            // Preparación de la consulta para insertar el nuevo usuario
            $query = $conexion->prepare(
                "INSERT INTO usuarios (nombre, apellido, correo, dni, password) VALUES (?, ?, ?, ?, ?)"
            );

            if ($query) {
                $query->bind_param("sssss", $nombre, $apellido, $correo, $dni, $passwordHasheada);
                if ($query->execute()) {
                    $message = 'Usuario registrado exitosamente';
                    $messageType = 'success'; // Tipo de mensaje: éxito
                } else {
                    $message = 'Error al registrar usuario';
                    $messageType = 'danger';
                }
                $query->close();
            } else {
                $message = 'Error en la preparación de la consulta';
                $messageType = 'danger';
            }
        }
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATO CHAN</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Bienvenido a CATO CHAN</h1>
        <img src="logo.jpeg" alt="Logo de CATO CHAN" class="logo">
        <div class="buttons">
            <button id="btn-ingresar">Iniciar sesión</button>
            <button id="btn-registrar">Registrarse</button>
        </div>
    </header>

    <main>
        <div class="container">
            <?php if ($message): ?>
                <div class="alert <?= $messageType; ?>" role="alert">
                    <?= htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <a href="index.html" class="btn btn-primary">Volver a inicio</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 - CATO CHAN. Todos los derechos reservados.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>
