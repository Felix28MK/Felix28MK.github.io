<?php
session_start();

// Verificar si el usuario está autenticado y es un maestro
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'maestro') {
    header("Location: login.php"); // Redirigir al login si no está autenticado o no es maestro
    exit;
}

// Incluir el archivo de conexión
require_once 'controladores/conexion.php';

// Obtener el ID del maestro desde la sesión
$maestro_id = $_SESSION['user_id'];

try {
    // Obtener la lista de materias
    $sqlMaterias = "SELECT * FROM materias";
    $stmtMaterias = $conn->query($sqlMaterias);
    $materias = $stmtMaterias->fetchAll(PDO::FETCH_ASSOC);

    // Obtener la lista de cursos cuando se seleccione una materia
    if (isset($_GET['id_materia'])) {
        $idMateria = $_GET['id_materia'];

        try {
            // Consultar los cursos de la materia seleccionada
            $sqlCursos = "SELECT * FROM cursos WHERE id_materia = :id_materia";
            $stmtCursos = $conn->prepare($sqlCursos);
            $stmtCursos->bindParam(':id_materia', $idMateria);
            $stmtCursos->execute();
            $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($cursos);
        } catch (PDOException $e) {
            echo "Error al obtener cursos: " . $e->getMessage();
        }
    }

} catch (PDOException $e) {
    echo "Error al realizar la consulta: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="modal.css">
    <style>
        h3 {
            margin-bottom: 15px;
        }
        .ficha-estudiante {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            margin: 10px;
            width: 200px;
            text-align: center;
            background-color: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .fichas-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }
        label {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Panel de Maestro</div>
        <ul class="navbar-menu">
            <li><a href="maestro_dashboard.php">Inicio</a></li>
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
    <input type="hidden" id="maestroId" value="<?php echo $maestro_id; ?>">
    <h1>Calificaciones</h1>
    <table>
        <tr>
            <td>
                <label for="materiasSelect">Selecciona la materia:</label>
                <select id="materiasSelect" required>
                    <?php
                    foreach ($materias as $materia) {
                        echo "<option value='{$materia['id']}'>{$materia['nombre']}</option>";
                    }
                    ?>
                </select>
            </td>
            <td>
                <label for="cursosSelect">Selecciona el curso:</label>
                <select id="cursosSelect" required>
                    <option value="">Seleccione un curso</option>
                </select>
            </td>
        </tr>
    </table>

    <div class="fichas-container" id="estudiantesContainer"></div>

    <script>
        // Cargar los cursos cuando se seleccione la materia
        document.getElementById('materiasSelect').addEventListener('change', function() {
            const materiaId = this.value;
            const maestroId = document.getElementById('maestroId').value;  // Asegúrate de tener un campo con este id en el HTML

            if (materiaId && maestroId) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'controladores/get_cursos_maestro.php?id_materia=' + materiaId + '&id_maestro=' + maestroId, true);
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        const cursos = JSON.parse(xhr.responseText);

                        const cursosSelect = document.getElementById('cursosSelect');
                        cursosSelect.innerHTML = '<option value="">Seleccione un curso</option>';

                        cursos.forEach(curso => {
                            const option = document.createElement('option');
                            option.value = curso.id;
                            option.textContent = curso.id;
                            cursosSelect.appendChild(option);
                        });
                    }
                };
                xhr.send();
            } else {
                document.getElementById('cursosSelect').innerHTML = '<option value="">Seleccione un curso</option>';
            }
        });

        // Mostrar estudiantes cuando se seleccione el curso
        document.getElementById('cursosSelect').addEventListener('change', function () {
            const cursoId = this.value; // Obtener el curso seleccionado
            
            if (cursoId) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'controladores/get_calificaciones.php?id_curso=' + cursoId, true); // Consultar estudiantes por curso
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const estudiantes = JSON.parse(xhr.responseText);
                        const estudiantesContainer = document.getElementById('estudiantesContainer');
                        
                        estudiantesContainer.innerHTML = ''; // Limpiar las fichas anteriores

                        // Verificar si hay estudiantes
                        if (estudiantes.length > 0) {
                            estudiantes.forEach(estudiante => {
                                const ficha = document.createElement('div');
                                ficha.className = 'ficha-estudiante';
                                ficha.innerHTML = `
                                    <h3>${estudiante.nombre_completo}</h3>
                                    <p>Unidad 1: ${estudiante.unidad_1 !== null ? estudiante.unidad_1 : 'N/A'}</p>
                                    <p>Unidad 2: ${estudiante.unidad_2 !== null ? estudiante.unidad_2 : 'N/A'}</p>
                                    <p>Unidad 3: ${estudiante.unidad_3 !== null ? estudiante.unidad_3 : 'N/A'}</p>
                                    <p>Unidad 4: ${estudiante.unidad_4 !== null ? estudiante.unidad_4 : 'N/A'}</p>
                                    <p>Unidad 5: ${estudiante.unidad_5 !== null ? estudiante.unidad_5 : 'N/A'}</p>
                                    <p><strong>Calificación Final: ${estudiante.calificacion_final !== null ? estudiante.calificacion_final : 'N/A'}</strong></p>
                                `;
                                estudiantesContainer.appendChild(ficha);
                            });
                        } else {
                            estudiantesContainer.innerHTML = '<p>No hay estudiantes inscritos en este curso.</p>';
                        }
                    } else {
                        console.error('Error al cargar estudiantes:', xhr.statusText);
                    }
                };
                xhr.send();
            } else {
                // Si no hay curso seleccionado, limpiar la vista
                document.getElementById('estudiantesContainer').innerHTML = '<p>Seleccione un curso.</p>';
            }
        });
    </script>
</body>
</html>