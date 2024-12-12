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

// Consultar información del alumno
$stmt = $conn->prepare("SELECT * FROM alumnos WHERE id = :id");
$stmt->execute(['id' => $alumno_id]);
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encontraron datos del alumno, se insertan datos de ejemplo
if (!$alumno) {
    // Datos predeterminados de ejemplo
    $alumno = [
        'id' => $alumno_id,
        'numero_control' => '12345678',
        'nombre_completo' => 'Alumno Ejemplo',
        'grado' => 1,
        'grupo' => 'A',
        'correo' => 'alumno@ejemplo.com',
        'direccion' => 'Dirección predeterminada',
        'edad' => 18,
        'fecha_nacimiento' => '2005-01-01',
        'telefono' => '1234567890'
    ];
    
    // Insertar los datos de ejemplo en la base de datos
    $insert_stmt = $conn->prepare("INSERT INTO alumnos (id, numero_control, nombre_completo, grado, grupo, correo, direccion, edad, fecha_nacimiento, telefono) 
                                  VALUES (:id, :numero_control, :nombre_completo, :grado, :grupo, :correo, :direccion, :edad, :fecha_nacimiento, :telefono)");
    $insert_stmt->execute($alumno);
}

// Si el formulario fue enviado para actualizar datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numero_control = $_POST['numero_control'];
    $nombre_completo = $_POST['nombre_completo'];
    $grado = $_POST['grado'];
    $grupo = $_POST['grupo'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $edad = $_POST['edad'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $telefono = $_POST['telefono'];

    // Actualizar los datos en la base de datos
    $update_stmt = $conn->prepare("UPDATE alumnos SET 
        numero_control = :numero_control, 
        nombre_completo = :nombre_completo, 
        grado = :grado, 
        grupo = :grupo, 
        correo = :correo, 
        direccion = :direccion, 
        edad = :edad, 
        fecha_nacimiento = :fecha_nacimiento, 
        telefono = :telefono 
        WHERE id = :id");
    $update_stmt->execute([
        'numero_control' => $numero_control,
        'nombre_completo' => $nombre_completo,
        'grado' => $grado,
        'grupo' => $grupo,
        'correo' => $correo,
        'direccion' => $direccion,
        'edad' => $edad,
        'fecha_nacimiento' => $fecha_nacimiento,
        'telefono' => $telefono,
        'id' => $alumno_id
    ]);

    // Redirigir después de la actualización
    header("Location: alumno_dashboard.php"); // Redirigir al dashboard después de guardar los datos
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del Alumno</title>
    <link rel="stylesheet" href="alumno.css">
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
        <div class="navbar-brand">Panel de Alumnos</div>
        <ul class="navbar-menu">
            <li><a href="alumno_dashboard.php">Inicio</a></li>
            <li><a href="#">Datos Personales</a></li>
            <li><a href="materias.php">Materias</a></li>
            <li><a href="examenes_alumno.php">Exámenes</a></li>
            <li><a href="logout.php" id="logout">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <!-- Contenedor principal -->
    <div class="container">
        <h1>Datos del Alumno</h1>
        <form method="POST">
            <div class="form-group">
                <label for="numero_control">Número de Control</label>
                <input type="text" id="numero_control" name="numero_control" value="<?php echo htmlspecialchars($alumno['numero_control']); ?>" required>
            </div>

            <div class="form-group">
                <label for="nombre_completo">Nombre Completo</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($alumno['nombre_completo']); ?>" required>
            </div>

            <div class="form-group">
                <label for="grado">Grado</label>
                <input type="number" id="grado" name="grado" value="<?php echo htmlspecialchars($alumno['grado']); ?>" required>
            </div>

            <div class="form-group">
                <label for="grupo">Grupo A, B, C, D</label>
                <input type="text" id="grupo" name="grupo" value="<?php echo htmlspecialchars($alumno['grupo']); ?>" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($alumno['correo']); ?>">
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($alumno['direccion']); ?>">
            </div>

            <div class="form-group">
                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad" value="<?php echo htmlspecialchars($alumno['edad']); ?>">
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($alumno['fecha_nacimiento']); ?>">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($alumno['telefono']); ?>">
            </div>

            <div class="form-group">
                <button type="submit">Guardar Datos</button>
            </div>
        </form>
    </div>
</body>
</html>
