<?php
session_start();

// Verificar si el usuario está autenticado y es un alumno
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'coordinador') {
    header("Location: login.php"); // Redirigir al login si no está autenticado o no es alumno
    exit;
}

require 'controladores/conexion.php'; // Incluir la conexión a la base de datos

// Obtener el ID del alumno desde la sesión
$coodinador_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Coordinador</title>
    <link rel="stylesheet" href="maestros.css">
    <link rel="stylesheet" href="lista.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar">
        <div class="navbar-brand">Panel del Coordinador</div>
        <ul class="navbar-menu">
            <li><a href="#">Inicio</a></li>
            <li><a href="registrar_cursos.php">Registrar Cursos</a></li>
            <li><a href="cursos.php">Ver Cursos</a></li>
            <li><a href="asignar_materias">Asignar materias</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
>
    <!-- Contenido principal -->
    <div class="main-content">
        <h1>Bienvenido, Coordinador</h1>
        <p>En este panel puedes gestionar los datos de las materias, los cursos, los maestros y los alumnos.</p>

        <div class="dashboard-sections">
            <section>
                <h2>Registrar Cursos</h2>
                <p>Registra un nuevo curso, asigna salon, grado, grupo, materia y maestro.</p>
                <button onclick="location.href='registrar_cursos.php'">Registrar Cursos</button>
            </section>

            <section>
                <h2>Ver Cursos</h2>
                <p>Revisa los cursos de la escuela.</p>
                <button onclick="location.href='cursos.php'">Ver Cursos</button>
            </section>

            <section>
                <h2>Asignar Materias</h2>
                <p>Asigna materias a los estudiantes.</p>
                <button onclick="location.href='asignar_materias.php'">Asignar Materias</button>
            </section>
        </div>
    </div>
</body>
</html>
