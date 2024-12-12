<?php
session_start();

// Verificar si el usuario es coordinador
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'coordinador') {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación Confirmada</title>
    <link rel="stylesheet" href="alumno.css">
    <link rel="stylesheet" href="lista.css">
    <style>
        /* Estilos básicos */
        body { font-family: Arial, sans-serif; background-color: #f4f9f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; color: #4CAF50; }
        p { text-align: center; font-size: 18px; color: #555; }
        button { background-color: #4CAF50; color: #fff; padding: 10px; border: none; border-radius: 5px; cursor: pointer; display: block; width: 100%; margin-top: 20px; }
        button:hover { background-color: #388E3C; }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        <div class="navbar-brand">Panel del Coordinador</div>
        <ul class="navbar-menu">
            <li><a href="#">Registrar Cursos</a></li>
            <li><a href="cursos.php">Ver Cursos</a></li>
            <li><a href="asignar_materias.php">Asignar materias</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>Asignación de Materias Confirmada</h1>
        <p>Las materias han sido asignadas correctamente al alumno.</p>
        <button onclick="window.location.href='asignar_materias.php'">Volver a Asignar Materias</button>
        <button onclick="window.location.href='cursos.php'">Ver Cursos</button>
    </div>
</body>
</html>
