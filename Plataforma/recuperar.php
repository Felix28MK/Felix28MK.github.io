<?php
require 'controladores/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];

    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        // Generar una clave de validación y fecha de expiración
        $validation_key = bin2hex(random_bytes(16));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Guardar en la tabla password_reset
        $stmt = $conn->prepare("DELETE FROM password_reset WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $stmt = $conn->prepare("INSERT INTO password_reset (username, validation_key, expiration) VALUES (:username, :validation_key, :expiration)");
        $stmt->execute([
            'username' => $username,
            'validation_key' => $validation_key,
            'expiration' => $expiration
        ]);

        // Enlace simulado de recuperación
        $reset_link = "http://localhost/restaurar.php?key=$validation_key";
        $message = "Se ha enviado un enlace de recuperación a tu correo (simulación): <a href='$reset_link'>$reset_link</a>";
    } else {
        $error = "El usuario no existe.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
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
        input, select {
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
        <h1>Recuperar Contraseña</h1>
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" id="username" placeholder="Nombre de Usuario" required>
            <button type="submit">Enviar</button>
        </form>
        <a class="back-link" href="restaurar.php">Restaurar contraseña</a>
        <a class="back-link" href="login.php">Volver al Inicio</a>
    </div>
</body>
</html>