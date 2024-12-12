<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de Juegos</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('images/fondo.jpg') no-repeat center center fixed;
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

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 600px;
            position: relative;
            z-index: 2;
        }

        .container h1 {
            margin-bottom: 40px;
            color: #3d85c6;
            font-size: 32px;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        button {
            display: block;
            width: 100%;
            max-width: 280px;
            margin: 15px auto;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            background: linear-gradient(135deg, #6fa8dc, #3d85c6);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        button:hover {
            background: linear-gradient(135deg, #3d85c6, #0b5394);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
        }

        .username {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #ffffff;
            font-weight: bold;
            font-size: 18px;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px 20px;
            border-radius: 15px;
        }

        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .logout a {
            text-decoration: none;
            color: #ffffff;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px 20px;
            border-radius: 15px;
            border: 2px solid #ffffff;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .logout a:hover {
            background-color: #ffffff;
            color: #3d85c6;
        }
    </style>
</head>

<body>
    <!-- Nombre de usuario en la esquina superior izquierda -->
    <?php if (isset($_SESSION['username'])): ?>
        <div class="username">
            Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </div>
    <?php endif; ?>

    <!-- Botón de cerrar sesión -->
    <?php if (isset($_SESSION['username'])): ?>
        <div class="logout">
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    <?php endif; ?>

    <div class="container">
        <h1>Aprende y diviertete</h1>
        <button onclick="location.href='wordsearch.php'">Jugar Sopa de Letras</button>
        <button onclick="location.href='quiz.php'">Juego de Preguntas</button>
        <button onclick="location.href='match_game.php'">Asocia Palabras</button>
        <button onclick="location.href='shooting_game.php'">Juego del Cubo Disparador</button>
    </div>
</body>

</html>
