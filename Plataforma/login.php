<?php
session_start();
require 'controladores/conexion.php'; // Archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para obtener el usuario
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Autenticación exitosa, guardar datos en la sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['userType'];

        // Redirigir según el tipo de usuario
        if ($user['userType'] == 'alumno') {
            header("Location: alumno_dashboard.php"); // Redirigir a la página de alumno
        } elseif ($user['userType'] == 'maestro') {
            header("Location: maestro_dashboard.php"); // Redirigir a la página de maestro
        } elseif ($user['userType'] == 'coordinador') {
            header("Location: coordinador_dashboard.php"); // Redirigir a la página de coordinador
        }
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
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
        .login-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        input[type="text"], input[type="password"] {
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
            margin-bottom: 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #003b09;
        }
        .secondary-buttons a {
            display: block;
            margin: 5px 0;
            color: #006709;
            text-decoration: none;
        }
        .secondary-buttons a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <div class="secondary-buttons">
            <a href="registro.php">Registrar Usuario</a>
            <a href="recuperar.php">Recuperar Contraseña</a>
            <a href="/Proyecto/index.php">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>
