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

// Consultar información del maestro
$stmt = $conn->prepare("SELECT * FROM maestros WHERE id = :id");
$stmt->execute(['id' => $maestro_id]);
$maestro = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encontraron datos del maestro, se insertan datos de ejemplo
if (!$maestro) {
    // Datos predeterminados de ejemplo
    $maestro = [
        'id' => $maestro_id,
        'rfc' => 'RFC123456ABC',
        'nombre_completo' => 'Nombre',
        'direccion' => 'Direccion',
        'edad' => 30,
        'telefono' => '1234567890',
        'correo' => 'correo@ejemplo.com'
    ];
    
    // Insertar los datos de ejemplo en la base de datos
    $insert_stmt = $conn->prepare("INSERT INTO maestros (id, rfc, nombre_completo, direccion, edad, telefono, correo) 
                                  VALUES (:id, :rfc, :nombre_completo, :direccion, :edad, :telefono, :correo)");
    $insert_stmt->execute([
        'id' => $maestro_id,
        'rfc' => $maestro['rfc'],
        'nombre_completo' => $maestro['nombre_completo'],
        'direccion' => $maestro['direccion'],
        'edad' => $maestro['edad'],
        'telefono' => $maestro['telefono'],
        'correo' => $maestro['correo']
    ]);
}

// Si el formulario fue enviado para actualizar datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rfc = $_POST['rfc'];
    $nombre_completo = $_POST['nombre_completo'];
    $direccion = $_POST['direccion'];
    $edad = $_POST['edad'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    // Actualizar los datos en la base de datos
    $update_stmt = $conn->prepare("UPDATE maestros SET rfc = :rfc, nombre_completo = :nombre_completo, direccion = :direccion, edad = :edad, telefono = :telefono, correo = :correo WHERE id = :id");
    $update_stmt->execute([
        'rfc' => $rfc,
        'nombre_completo' => $nombre_completo,
        'direccion' => $direccion,
        'edad' => $edad,
        'telefono' => $telefono,
        'correo' => $correo,
        'id' => $maestro_id
    ]);

    // Redirigir después de la actualización
    header("Location: maestro_dashboard.php"); // Redirigir al dashboard después de guardar los datos
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del Maestro</title>
    <link rel="stylesheet" href="maestro.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="lista.css">
    <style>
        /* Estilos generales */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f9f4; /* Fondo suave verde claro */
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #4CAF50; /* Barra de navegación en verde */
            padding: 10px 20px;
            color: white;
            text-align: center;
        }

        .navbar-menu {
            list-style-type: none;
            padding: 0;
        }

        .navbar-menu li {
            display: inline;
            margin-right: 20px;
        }

        .navbar-menu a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .navbar-menu a:hover {
            text-decoration: underline;
        }

        /* Estilos del contenedor principal */
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff; /* Fondo blanco para el formulario */
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #4CAF50; /* Borde verde */
        }

        h1 {
            text-align: center;
            color: #388E3C; /* Título verde oscuro */
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            color: #388E3C; /* Etiquetas en verde oscuro */
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #4CAF50; /* Borde verde */
            border-radius: 4px;
            background-color: #f1fdf1; /* Fondo verde claro */
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #2C6B2F; /* Borde verde más oscuro al hacer foco */
            outline: none;
        }

        .form-group button {
            background-color: #4CAF50; /* Botón verde */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-group button:hover {
            background-color: #388E3C; /* Botón verde oscuro al pasar el mouse */
        }

        .form-group button:active {
            background-color: #2C6B2F; /* Botón verde más oscuro al hacer clic */
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar">
        <div class="navbar-brand">Panel de Maestro</div>
        <ul class="navbar-menu">
            <li><a href="maestro_dashboard.php">Inicio</a></li>
            <li><a href="#">Datos</a></li>
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
    <h1> </h1>
    <div class="container">
        <!-- Formulario para editar los datos del maestro -->
        <form method="POST">
        <h1>Datos del Maestro</h1>
            <div class="form-group">
                <label for="rfc">RFC</label>
                <input type="text" id="rfc" name="rfc" value="<?php echo htmlspecialchars($maestro['rfc']); ?>" required>
            </div>

            <div class="form-group">
                <label for="nombre_completo">Nombre Completo</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($maestro['nombre_completo']); ?>" required>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <textarea id="direccion" name="direccion"><?php echo htmlspecialchars($maestro['direccion']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad" value="<?php echo htmlspecialchars($maestro['edad']); ?>" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($maestro['telefono']); ?>" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($maestro['correo']); ?>" required>
            </div>

            <div class="form-group">
                <button type="submit">Guardar Datos</button>
            </div>
        </form>
    </div>
</body>
</html>