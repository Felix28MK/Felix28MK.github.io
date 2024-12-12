<?php
// Incluir la conexión PDO
include('controladores/conexion.php'); // Asegúrate de tener este archivo correctamente configurado

// Verificar si se ha enviado una solicitud para eliminar un curso
if (isset($_GET['eliminar'])) {
    $id_curso = $_GET['eliminar'];
    try {
        // Eliminar el curso de la base de datos
        $query = "DELETE FROM cursos WHERE id = :id_curso";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_curso', $id_curso);
        $stmt->execute();
        $message = "Curso eliminado correctamente.";
    } catch (PDOException $e) {
        $message = "Error al eliminar el curso: " . $e->getMessage();
    }
}

// Verificar si se está editando un curso
if (isset($_POST['editar'])) {
    // Recibir los datos del formulario de edición
    $id_curso = $_POST['id_curso'];
    $id_materia = $_POST['id_materia'];
    $id_maestro = $_POST['id_maestro'];
    $grado = $_POST['grado'];
    $grupo = $_POST['grupo'];
    $horario = $_POST['horario'];
    $salon = $_POST['salon'];

    try {
        // Actualizar los datos del curso en la base de datos
        $query = "UPDATE cursos 
                  SET id_materia = :id_materia, id_maestro = :id_maestro, grado = :grado, grupo = :grupo, horario = :horario, salon = :salon 
                  WHERE id = :id_curso";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_materia', $id_materia);
        $stmt->bindParam(':id_maestro', $id_maestro);
        $stmt->bindParam(':grado', $grado);
        $stmt->bindParam(':grupo', $grupo);
        $stmt->bindParam(':horario', $horario);
        $stmt->bindParam(':salon', $salon);
        $stmt->bindParam(':id_curso', $id_curso);
        $stmt->execute();
        $message = "Curso editado correctamente.";
    } catch (PDOException $e) {
        $message = "Error al editar el curso: " . $e->getMessage();
    }
}

// Consultar los cursos para mostrarlos
try {
    // Consultar los cursos asignados
    $cursos = $conn->query("SELECT c.id, m.nombre AS materia, ma.nombre_completo AS maestro, c.grado, c.grupo, c.horario, c.salon
                            FROM cursos c
                            JOIN materias m ON c.id_materia = m.id
                            JOIN maestros ma ON c.id_maestro = ma.id");
    
    // Consultar los maestros y materias para formularios de edición
    $maestros = $conn->query("SELECT id, nombre_completo FROM maestros");
    $materias = $conn->query("SELECT id, nombre FROM materias");
} catch (PDOException $e) {
    $message = "Error al obtener datos: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
    <!-- Incluir las hojas de estilo -->
    <link rel="stylesheet" href="maestros.css">
    <link rel="stylesheet" href="lista.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        button:disabled {
            background-color: #cccccc;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        <div class="navbar-brand">Panel del Coordinador</div>
        <ul class="navbar-menu">
            <li><a href="coordinador_dashboard.php">Inicio</a></li>
            <li><a href="registrar_cursos.php">Registrar Cursos</a></li>
            <li><a href="#">Ver Cursos</a></li>
            <li><a href="asignar_materias.php">Asignar materias</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="container2">
        <h1>Gestión de Cursos</h1>

        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Mostrar los cursos en una tabla -->
        <h2>Cursos Asignados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Curso</th>
                    <th>Materia</th>
                    <th>Maestro</th>
                    <th>Grado</th>
                    <th>Grupo</th>
                    <th>Horario</th>
                    <th>Salón</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($curso = $cursos->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $curso['id']; ?></td>
                        <td><?php echo $curso['materia']; ?></td>
                        <td><?php echo $curso['maestro']; ?></td>
                        <td><?php echo $curso['grado']; ?></td>
                        <td><?php echo $curso['grupo']; ?></td>
                        <td><?php echo $curso['horario']; ?></td>
                        <td><?php echo $curso['salon']; ?></td>
                        <td>
                            <!-- Botón para editar -->
                            <a href="Cursos.php?editar=<?php echo $curso['id']; ?>" class="button">Editar</a>
                            <!-- Botón para eliminar -->
                            <a href="Cursos.php?eliminar=<?php echo $curso['id']; ?>" class="button" onclick="return confirm('¿Estás seguro de eliminar este curso?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Formulario de edición (si se ha seleccionado un curso para editar) -->
        <?php if (isset($_GET['editar'])):
            $id_curso = $_GET['editar'];
            $stmt = $conn->prepare("SELECT * FROM cursos WHERE id = :id_curso");
            $stmt->bindParam(':id_curso', $id_curso);
            $stmt->execute();
            $curso_editar = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
            <h2>Editar Curso</h2>
            <form method="POST" action="">
                <input type="hidden" name="id_curso" value="<?php echo $curso_editar['id']; ?>">

                <div class="form-section">
                    <label for="id_materia">Materia:</label>
                    <select name="id_materia" required>
                        <?php while ($materia = $materias->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $materia['id']; ?>" <?php echo ($curso_editar['id_materia'] == $materia['id']) ? 'selected' : ''; ?>>
                                <?php echo $materia['nombre']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-section">
                    <label for="id_maestro">Maestro:</label>
                    <select name="id_maestro" required>
                        <?php while ($maestro = $maestros->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $maestro['id']; ?>" <?php echo ($curso_editar['id_maestro'] == $maestro['id']) ? 'selected' : ''; ?>>
                                <?php echo $maestro['nombre_completo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-section">
                    <label for="grado">Grado:</label>
                    <select name="grado" required>
                        <option value="1" <?php echo ($curso_editar['grado'] == 1) ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo ($curso_editar['grado'] == 2) ? 'selected' : ''; ?>>2</option>
                        <option value="3" <?php echo ($curso_editar['grado'] == 3) ? 'selected' : ''; ?>>3</option>
                    </select>
                </div>

                <div class="form-section">
                    <label for="grupo">Grupo:</label>
                    <select name="grupo" required>
                        <option value="A" <?php echo ($curso_editar['grupo'] == 'A') ? 'selected' : ''; ?>>A</option>
                        <option value="B" <?php echo ($curso_editar['grupo'] == 'B') ? 'selected' : ''; ?>>B</option>
                        <option value="C" <?php echo ($curso_editar['grupo'] == 'C') ? 'selected' : ''; ?>>C</option>
                        <option value="D" <?php echo ($curso_editar['grupo'] == 'D') ? 'selected' : ''; ?>>D</option>
                    </select>
                </div>

                <div class="form-section">
                    <label for="horario">Horario:</label>
                    <input type="text" name="horario" value="<?php echo $curso_editar['horario']; ?>" required>
                </div>

                <div class="form-section">
                    <label for="salon">Salón:</label>
                    <input type="text" name="salon" value="<?php echo $curso_editar['salon']; ?>" required>
                </div>

                <div class="form-section">
                    <button type="submit" name="editar">Actualizar Curso</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>