<?php
require 'controladores/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $validation_key = $_POST['validation_key'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar que las contraseñas coinciden
    if ($new_password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si la combinación de username y validation_key es válida
        $stmt = $conn->prepare("SELECT * FROM password_reset WHERE username = :username AND validation_key = :validation_key AND expiration > NOW()");
        $stmt->execute(['username' => $username, 'validation_key' => $validation_key]);
        $reset_request = $stmt->fetch();

        if ($reset_request) {
            // Actualizar la contraseña del usuario
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET password = :password WHERE username = :username");
            $stmt->execute(['password' => $hashed_password, 'username' => $username]);

            // Eliminar la solicitud de recuperación
            $stmt = $conn->prepare("DELETE FROM password_reset WHERE validation_key = :validation_key");
            $stmt->execute(['validation_key' => $validation_key]);

            $message = "Contraseña restablecida con éxito. <a href='login.php'>Ir al inicio de sesión</a>";
        } else {
            $error = "El enlace de recuperación no es válido, el usuario no coincide o ha expirado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restaurar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #2b6a2d;
        }
        .recuperar-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #006709;
            color: #ffffff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            width: 95%;
            cursor: pointer;
        }
        button:hover {
            background-color: #003b09;
        }
        .back-link {
            display: block;
            margin-top: 10px;
            color: #006709;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .message {
            color: #006709;
            margin-top: 10px;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="recuperar-container">
        <h1>Restaurar Contraseña</h1>
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (!isset($message)): ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Nombre de Usuario" required>
            <input type="text" name="validation_key" placeholder="Clave de Validación" required>
            <input type="password" name="new_password" placeholder="Nueva Contraseña" required>
            <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required>
            <button type="submit">Restaurar Contraseña</button>
        </form>
        <?php endif; ?>
        <a class="back-link" href="login.php">Volver al Inicio</a>
    </div>
</body>
</html>
