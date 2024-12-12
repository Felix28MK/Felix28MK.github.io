<?php
session_start();

// Verificar si el usuario está autenticado y es un alumno
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'alumno') {
    header("Location: login.php"); // Redirigir al login si no está autenticado o no es alumno
    exit;
}

require 'controladores/conexion.php'; // Conexión a la base de datos

// Obtener el ID del alumno desde la sesión
$alumno_id = $_SESSION['user_id'];

// Consultar las materias inscritas, incluyendo maestro, curso y calificaciones
$stmt = $conn->prepare("
    SELECT 
        m.nombre AS materia_nombre,
        c.id AS id_curso,
        c.grado,
        c.grupo,
        c.horario,
        c.salon,
        ma.nombre_completo AS maestro_nombre,
        i.unidad_1,
        i.unidad_2,
        i.unidad_3,
        i.unidad_4,
        i.unidad_5,
        i.calificacion_final
    FROM inscripciones i
    INNER JOIN cursos c ON i.id_curso = c.id
    INNER JOIN materias m ON c.id_materia = m.id
    INNER JOIN maestros ma ON c.id_maestro = ma.id
    WHERE i.id_alumno = :id_alumno
");
$stmt->execute(['id_alumno' => $alumno_id]);
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materias Inscritas</title>
    <link rel="stylesheet" href="alumno.css">
    <link rel="stylesheet" href="lista.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            background-color: #fff;
        }

        .card h2 {
            margin: 0;
            color: #333;
        }

        .card p {
            margin: 5px 0;
            color: #555;
        }

        .calificaciones {
            margin-top: 10px;
        }

        .calificaciones span {
            display: inline-block;
            margin-right: 15px;
        }

        .calificaciones table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            text-align: center;
        }

        .calificaciones th, .calificaciones td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .calificaciones th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Panel de Alumnos</div>
        <ul class="navbar-menu">
            <li><a href="alumno_dashboard.php">Inicio</a></li>
            <li><a href="datos_alumno.php">Datos Personales</a></li>
            <li><a href="#">Materias</a></li>
            <li><a href="examenes_alumno.php">Exámenes</a></li>
            <li><a href="logout.php" id="logout">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php if (count($materias) > 0): ?>
            <?php foreach ($materias as $materia): ?>


                <div class="card">
                    <h2><?php echo htmlspecialchars($materia['materia_nombre']); ?></h2>
                    <p><strong>Curso:</strong> <?php echo htmlspecialchars($materia['id_curso']); ?></p>
                    <p><strong>Maestro:</strong> <?php echo htmlspecialchars($materia['maestro_nombre']); ?></p>
                    <p><strong>Grado:</strong> <?php echo htmlspecialchars($materia['grado']); ?></p>
                    <p><strong>Grupo:</strong> <?php echo htmlspecialchars($materia['grupo']); ?></p>
                    <p><strong>Horario:</strong> <?php echo htmlspecialchars($materia['horario']); ?></p>
                    <p><strong>Salón:</strong> <?php echo htmlspecialchars($materia['salon']); ?></p>
                    <div class="calificaciones">
                        <strong>Calificaciones:</strong>
                        <table>
                            <thead>
                                <tr>
                                    <th>Unidad 1</th>
                                    <th>Unidad 2</th>
                                    <th>Unidad 3</th>
                                    <th>Unidad 4</th>
                                    <th>Unidad 5</th>
                                    <th>Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($materia['unidad_1'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($materia['unidad_2'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($materia['unidad_3'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($materia['unidad_4'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($materia['unidad_5'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($materia['calificacion_final'] ?? 'N/A'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tienes materias inscritas.</p>
        <?php endif; ?>
    </div>
</body>
</html>
