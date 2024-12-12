<?php
session_start();
require 'controladores/conexion.php';

// Verificar si el usuario es coordinador
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'coordinador') {
    header("Location: login.php");
    exit;
}

$coordinador_id = $_SESSION['user_id'];

// Definir los grados y grupos disponibles
$grados = [1, 2, 3];
$grupos = ['A', 'B', 'C', 'D'];

// Obtener todas las materias disponibles
$materias_stmt = $conn->prepare("SELECT * FROM materias");
$materias_stmt->execute();
$materias = $materias_stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejar los filtros por grado y grupo
$grado_filtrado = $_POST['grado'] ?? '';
$grupo_filtrado = $_POST['grupo'] ?? '';
$where_clause = 'WHERE 1=1';

if ($grado_filtrado) $where_clause .= " AND grado = :grado";
if ($grupo_filtrado) $where_clause .= " AND grupo = :grupo";

$alumnos_stmt = $conn->prepare("SELECT id, nombre_completo FROM alumnos $where_clause");
$params = [];
if ($grado_filtrado) $params['grado'] = $grado_filtrado;
if ($grupo_filtrado) $params['grupo'] = $grupo_filtrado;
$alumnos_stmt->execute($params);
$alumnos = $alumnos_stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener materias inscritas para cada alumno
$materias_inscritas_por_alumno = [];
foreach ($alumnos as $alumno) {
    $inscripciones_stmt = $conn->prepare("SELECT id_curso, materia FROM inscripciones WHERE id_alumno = :id_alumno");
    $inscripciones_stmt->execute(['id_alumno' => $alumno['id']]);
    $materias_inscritas_por_alumno[$alumno['id']] = $inscripciones_stmt->fetchAll(PDO::FETCH_COLUMN, 1); // Solo obtenemos los nombres de las materias
}

// Manejar la asignación de materias
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alumno_id'])) {
    $alumno_id = $_POST['alumno_id'];
    $materias_seleccionadas = $_POST['materias'] ?? [];

    // Obtener los datos del alumno
    $alumno_stmt = $conn->prepare("SELECT grado, grupo FROM alumnos WHERE id = :id");
    $alumno_stmt->execute(['id' => $alumno_id]);
    $alumno = $alumno_stmt->fetch(PDO::FETCH_ASSOC);

    if ($alumno) {
        $grado_alumno = $alumno['grado'];
        $grupo_alumno = $alumno['grupo'];

        $no_curso_mensaje = '';

        foreach ($materias_seleccionadas as $materia_id) {
            // Obtener los cursos correspondientes a la materia, grado y grupo seleccionados
            $cursos_stmt = $conn->prepare("SELECT * FROM cursos WHERE id_materia = :id_materia AND grado = :grado AND grupo = :grupo");
            $cursos_stmt->execute([
                'id_materia' => $materia_id,
                'grado' => $grado_alumno,
                'grupo' => $grupo_alumno
            ]);
            $cursos = $cursos_stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($cursos)) {
                // Mensaje si no hay cursos disponibles para esa materia
                $no_curso_mensaje .= "No hay curso disponible para la materia seleccionada en el grado $grado_alumno / grupo $grupo_alumno.<br>";
            } else {
                // Obtener el nombre de la materia
                $materia_stmt = $conn->prepare("SELECT nombre FROM materias WHERE id = :id_materia");
                $materia_stmt->execute(['id_materia' => $materia_id]);
                $materia = $materia_stmt->fetch(PDO::FETCH_ASSOC);
                $materia_nombre = $materia['nombre'];

                foreach ($cursos as $curso) {
                    // Verificar si ya existe una inscripción para el alumno en este curso
                    $inscripcion_check_stmt = $conn->prepare("SELECT 1 FROM inscripciones WHERE id_alumno = :id_alumno AND id_curso = :id_curso");
                    $inscripcion_check_stmt->execute([
                        'id_alumno' => $alumno_id,
                        'id_curso' => $curso['id']
                    ]);
                    $inscripcion_existe = $inscripcion_check_stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$inscripcion_existe) {
                        // Insertar inscripción solo si no existe
                        $insert_stmt = $conn->prepare("INSERT INTO inscripciones (id_alumno, id_curso, materia, horario) VALUES (:id_alumno, :id_curso, :materia, :horario)");
                        $insert_stmt->execute([
                            'id_alumno' => $alumno_id,
                            'id_curso' => $curso['id'],
                            'materia' => $curso['id_materia'],
                            'horario' => $curso['horario']
                        ]);
                    }
                }
            }
        }

        // Mostrar mensaje si no hay cursos disponibles, o redirigir si todo fue exitoso
        if ($no_curso_mensaje) {
            echo "<div class='mensaje'>$no_curso_mensaje</div>";
        } else {
            header("Location: asignacion_confirmada.php");
            exit;
        }
    } else {
        echo "Alumno no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Materias</title>
    <link rel="stylesheet" href="maestros.css">
    <link rel="stylesheet" href="lista.css">
    <style>
        /* Estilos básicos */
        body { font-family: Arial, sans-serif; background-color: #f4f9f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; color: #4CAF50; }
        form { display: flex; flex-direction: column; }
        .materia { margin-bottom: 15px; }
        button { background-color: #4CAF50; color: #fff; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #388E3C; }
        .mensaje { color: red; font-size: 14px; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        <div class="navbar-brand">Panel del Coordinador</div>
        <ul class="navbar-menu">
            <li><a href="coordinador_dashboard.php">Inicio</a></li>
            <li><a href="registrar_cursos.php">Registrar Cursos</a></li>
            <li><a href="cursos.php">Ver Cursos</a></li>
            <li><a href="#">Asignar materias</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>Asignar Materias a un Alumno</h1>
        
        <!-- Filtro por grado y grupo -->
        <form method="POST">
            <table>
                <tr>
                    <td>
                        <label for="grado">Seleccionar Grado:</label>
                        <select name="grado" id="grado">
                            <option value="">Todos los grados</option>
                            <?php foreach ($grados as $grado): ?>
                                <option value="<?php echo $grado; ?>" <?php echo ($grado == $grado_filtrado) ? 'selected' : ''; ?>>
                                    <?php echo $grado; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <label for="grupo">Seleccionar Grupo:</label>
                        <select name="grupo" id="grupo">
                            <option value="">Todos los grupos</option>
                            <?php foreach ($grupos as $grupo): ?>
                                <option value="<?php echo $grupo; ?>" <?php echo ($grupo == $grupo_filtrado) ? 'selected' : ''; ?>>
                                    <?php echo $grupo; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <button type="submit">Filtrar</button>
                    </td>
                </tr>
            </table>
        </form>

        <!-- Seleccionar alumno y materias -->
        <form method="POST">
            <label for="alumno_id">Seleccionar Alumno:</label>
            <select name="alumno_id" id="alumno_id" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($alumnos as $alumno): ?>
                    <option value="<?php echo $alumno['id']; ?>" data-inscritas='<?php echo json_encode($materias_inscritas_por_alumno[$alumno['id']] ?? []); ?>'>
                        <?php echo htmlspecialchars($alumno['nombre_completo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Seleccionar Materias:</label>
            <?php foreach ($materias as $materia): ?>
                <div class="materia">
                    <input type="checkbox" name="materias[]" value="<?php echo $materia['id']; ?>" id="materia_<?php echo $materia['id']; ?>">
                    <label for="materia_<?php echo $materia['id']; ?>">
                        <?php echo htmlspecialchars($materia['nombre']); ?>
                    </label>
                </div>
            <?php endforeach; ?>

            <button type="submit">Asignar Materias</button>
        </form>

        <!-- Mostrar mensaje si no hay curso disponible -->
        <?php if (isset($no_curso_mensaje)): ?>
            <div class="mensaje"><?php echo $no_curso_mensaje; ?></div>
        <?php endif; ?>
    </div>

    <script>
        const alumnoSelect = document.getElementById('alumno_id');
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');

        alumnoSelect.addEventListener('change', () => {
            const selectedOption = alumnoSelect.options[alumnoSelect.selectedIndex];
            const inscritas = JSON.parse(selectedOption.getAttribute('data-inscritas') || '[]');

            checkboxes.forEach(checkbox => {
                checkbox.checked = inscritas.includes(checkbox.id.replace('materia_', ''));
            });
        });

        window.addEventListener('DOMContentLoaded', () => {
            alumnoSelect.dispatchEvent(new Event('change'));
        });
    </script>
</body>
</html>
