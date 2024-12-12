<?php
include 'db.php'; // Asegúrate de que este archivo contiene la conexión correcta a la base de datos
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guest'])) {
        // Acceso como invitado
        $_SESSION['user_id'] = 0; // ID de invitado
        $_SESSION['username'] = 'Invitado';
        header('Location: inicio.php');
        exit();
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        try {
            $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $query->execute([$email]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Credenciales correctas
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: index.php');
                exit();
            } else {
                // Credenciales incorrectas
                $error = "Correo o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            // Error de conexión o consulta
            $error = "Error en el inicio de sesión: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('images/login.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
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

        .container {
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

        h1 {
            color: #4caf50;
            margin-bottom: 20px;
            font-size: 28px;
            text-transform: uppercase;
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

        form input {
            width: 100%;
            padding: 15px;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 18px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        form button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #007BFF, #0056b3);
            color: white;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        }

        form button:hover {
            background: linear-gradient(135deg, #0056b3, #003f7f);
            transform: translateY(-3px);
        }

        .guest-button {
            background: linear-gradient(135deg, #4caf50, #388e3c);
        }

        .guest-button:hover {
            background: linear-gradient(135deg, #388e3c, #2e7d32);
        }

        p {
            margin-top: 15px;
            font-size: 16px;
        }

        p a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease-in-out;
        }

        p a:hover {
            color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="overlay"></div>

    <div class="container">
        <h1>Iniciar Sesión</h1>
        <?php if (isset($error)) echo "<p class='message'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <form method="POST" action="">
            <button type="submit" name="guest" class="guest-button">Ingresar como Invitado</button>
        </form>
        <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
        <a href="/Proyecto/index.php">Volver al Inicio</a>
    </div>
</body>

</html>
