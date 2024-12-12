<?php
require 'controladores/conexion.php'; // Archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = uniqid(); // Generar un ID único
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $userType = $_POST['userType'];

    // Validaciones
    if (empty($username) || empty($password) || empty($confirm_password) || empty($userType)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (!in_array($userType, ['alumno', 'maestro', 'coordinador'])) {
        $error = "El tipo de usuario no es válido.";
    } else {
        // Encriptar la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar el usuario en la base de datos
        try {
            $stmt = $conn->prepare("INSERT INTO usuarios (id, username, password, userType) VALUES (:id, :username, :password, :userType)");
            $stmt->execute([
                'id' => $id,
                'username' => $username,
                'password' => $hashed_password,
                'userType' => $userType
            ]);

            $success = "Usuario registrado exitosamente. <a href='login.php'>Iniciar sesión</a>";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error = "El nombre de usuario ya está en uso.";
            } else {
                $error = "Error al registrar el usuario: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
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
        .register-container {
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
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Registrar Usuario</h1>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Nombre de usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required>
            <select name="userType" required>
                <option value="">Seleccionar tipo de usuario</option>
                <option value="alumno">Alumno</option>
                <option value="maestro">Maestro</option>
                <option value="coordinador">Coordinador</option>
            </select>
            <button type="submit">Registrar</button>
        </form>
        <a class="back-link" href="login.php">Volver al inicio de sesión</a>
    </div>
</body>
</html>