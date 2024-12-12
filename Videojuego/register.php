<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $query = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $query->execute([$username, $email, $password]);
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        $error = "Error al registrarse: " . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('images/register.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
            overflow: hidden;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 90%;
            max-width: 450px;
            position: relative;
            z-index: 2;
        }

        .register-container h1 {
            color: #4caf50;
            font-size: 28px;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .message {
            color: #ff0000;
            background-color: #ffdddd;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
        }

        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"] {
            width: 100%;
            padding: 15px;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 18px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .register-container button {
            width: 100%;
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            border: none;
            padding: 15px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        }

        .register-container button:hover {
            background: linear-gradient(135deg, #388e3c, #2e7d32);
            transform: translateY(-3px);
        }

        .register-container a {
            color: #4caf50;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: color 0.3s ease-in-out;
        }

        .register-container a:hover {
            color: #388e3c;
        }

        .register-container p {
            font-size: 16px;
            margin-top: 20px;
        }

    </style>
</head>
<body>
    <div class="overlay"></div>

    <div class="register-container">
        <h1>Regístrate</h1>
        <?php if (isset($error)): ?>
            <p class="message"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Nombre de usuario" required>
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>
