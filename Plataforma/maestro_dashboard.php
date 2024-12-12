<?php
session_start();

// Verificar si el usuario está autenticado y es un maestro
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'maestro') {
    header("Location: login.php"); // Redirigir al login si no está autenticado o no es maestro
    exit;
}

require 'controladores/conexion.php'; // Incluir la conexión a la base de datos

// Obtener el ID del maestro desde la sesión
$maestro_id = $_SESSION['user_id'];

// Consultar información del maestro desde la tabla `maestros`
$stmt = $conn->prepare("SELECT * FROM maestros WHERE id = :id");
$stmt->execute(['id' => $maestro_id]);
$maestro = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no hay datos del maestro, redirigir a la página de "Datos del Maestro"
if (!$maestro || empty(trim($maestro['nombre_completo']))) {
    header("Location: datos_maestro.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Maestro</title>
    <link rel="stylesheet" href="maestros.css">
    <link rel="stylesheet" href="lista.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar">
        <div class="navbar-brand">Panel de Maestro</div>
        <ul class="navbar-menu">
            <li><a href="#">Inicio</a></li>
            <li><a href="datos_maestro.php">Datos</a></li>
            <li class="dropdown">
                <a href="ver_examenes.php">Ver Exámenes</a>
                <div class="dropdown-content">
                    <a href="crear_examen.php">Crear Examen</a>
                    <a href="ver_examenes.php">Exámenes</a>
                    <a href="imprimir_examen.php">Imprimir Examen</a>
                    <a href="calificaciones.php">Calificaciones</a>
                    <a href="resultados_examenes.php">Resultados de Exámenes</a>
                </div>
            </li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <!-- Contenido principal -->
    <div class="main-content">
        <h1>Bienvenido, <?php echo htmlspecialchars($maestro['nombre_completo']); ?></h1>
        <p>En este panel puedes gestionar tus datos, crear exámenes y visualizar el rendimiento de tus alumnos.</p>

        <div class="dashboard-sections">
            <section>
                <h2>Datos del Maestro</h2>
                <p>Ver y editar tu perfil y datos personales.</p>
                <button class="button" onclick="location.href='datos_maestro.php'">Ir a Datos</button>
            </section>

            <section>
                <h2>Crear Examen</h2>
                <p>Crea un nuevo examen para tus alumnos con preguntas personalizadas.</p>
                <button class="button" onclick="location.href='crear_examen.php'">Crear Examen</button>
            </section>

            <section>
                <h2>Ver Exámenes</h2>
                <p>Visualiza y administra los exámenes ya creados.</p>
                <button class="button" onclick="location.href='ver_examenes.php'">Ver Exámenes</button>
            </section>

            <section>
                <h2>Imprimir Examen</h2>
                <p>Imprime los exámenes que has creado.</p>
                <button class="button" onclick="location.href='imprimir_examen.php'">Imprimir Examen</button>
            </section>

            <section>
                <h2>Calificaciones</h2>
                <p>Ve las calificaciones de tus alumnos.</p>
                <button class="button" onclick="location.href='calificaciones.php'">Calificaciones</button>
            </section>

            <section>
                <h2>Resultados de los Exámenes</h2>
                <p>Visualiza los resultados de los exámenes realizados.</p>
                <button class="button" onclick="location.href='resultados_examenes.php'">Ver Resultados</button>
            </section>
        </div>
    </div>

    <script>
        // Función para alternar la visibilidad del contenido desplegable
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdownContent = event.target.nextElementSibling;
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
