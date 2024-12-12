<?php
session_start();

// Verificar si el usuario está autenticado y es un alumno
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'alumno') {
    header("Location: login.php"); // Redirigir al login si no está autenticado o no es alumno
    exit;
}

require 'controladores/conexion.php'; // Incluir la conexión a la base de datos

// Obtener el ID del alumno desde la sesión
$alumno_id = $_SESSION['user_id'];

// Consultar información del alumno desde la tabla `alumnos`
$stmt = $conn->prepare("SELECT * FROM alumnos WHERE id = :id");
$stmt->execute(['id' => $alumno_id]);
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no hay datos del alumno, redirigir a la página de "Datos del Alumno"
if (!$alumno || empty(trim($alumno['nombre_completo']))) {
    header("Location: datos_alumno.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Alumno</title>
    <link rel="stylesheet" href="alumno.css">
    <link rel="stylesheet" href="lista.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar">
        <div class="navbar-brand">Panel de Alumnos</div>
        <ul class="navbar-menu">
            <li><a href="#">Inicio</a></li>
            <li><a href="datos_alumno.php">Datos Personales</a></li>
            <li><a href="materias.php">Materias</a></li>
            <li><a href="examenes_alumno.php">Exámenes</a></li>
            <li><a href="logout.php" id="logout">Cerrar Sesión</a></li>
        </ul>
    </nav>
>
    <!-- Contenido principal -->
    <div class="main-content">
        <h1>Bienvenido, <?php echo htmlspecialchars($alumno['nombre_completo']); ?></h1>
        <p>En este panel puedes gestionar tus datos, revisar las materias y acceder a tus exámenes.</p>

        <div class="dashboard-sections">
            <section>
                <h2>Datos Personales</h2>
                <p>Consulta y actualiza tu información personal.</p>
                <button onclick="location.href='datos_alumno.php'">Ver Datos</button>
            </section>

            <section>
                <h2>Materias</h2>
                <p>Revisa las materias en las que estás inscrito.</p>
                <button onclick="location.href='materias.php'">Ver Materias</button>
            </section>

            <section>
                <h2>Exámenes</h2>
                <p>Accede a tus exámenes y consulta resultados anteriores.</p>
                <button onclick="location.href='examenes_alumno.php'">Ver Exámenes</button>
            </section>
        </div>
    </div>
</body>
</html>
