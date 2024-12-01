<?php
require 'config/config.php';
require 'config/database.php';

$db = new Database();
$con = $db->conectar();

// Inicializar variables para errores y mensajes
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($nombre && $email && $password) {
        // Verificar si el email ya está registrado
        $sql = $con->prepare("SELECT id FROM usuarios WHERE email = ?");
        $sql->execute([$email]);
        if ($sql->fetchColumn() > 0) {
            $mensaje = 'El correo ya está registrado.';
        } else {
            // Insertar el usuario en la base de datos
            $password_hash = password_hash($password, PASSWORD_DEFAULT); // Encriptar contraseña
            $sql = $con->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
            $resultado = $sql->execute([$nombre, $email, $password_hash]);

            if ($resultado) {
                $mensaje = 'Registro exitoso. ¡Ahora puedes iniciar sesión!';
            } else {
                $mensaje = 'Ocurrió un error al registrar al usuario.';
            }
        }
    } else {
        $mensaje = 'Por favor, completa todos los campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="my-4">Registro de Usuario</h2>
        <?php if ($mensaje): ?>
            <div class="alert alert-info"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="POST" action="registro.php">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrarse</button>
        </form>
    </div>
</body>
</html>
