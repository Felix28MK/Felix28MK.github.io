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

    // Obtener la lista de estudiantes inscritos en un curso específico
    if (isset($_GET['id_curso'])) {
        $idCurso = $_GET['id_curso'];
    
        try {
            // Obtener los estudiantes según el curso
            $sqlEstudiantes = "
                SELECT alumnos.id, alumnos.nombre_completo
                FROM alumnos
                JOIN inscripciones ON inscripciones.id_estudiante = alumnos.id
                WHERE inscripciones.id_curso = :id_curso";
            
            $stmtEstudiantes = $conn->prepare($sqlEstudiantes);
            $stmtEstudiantes->bindParam(':id_curso', $idCurso);
            $stmtEstudiantes->execute();
            $estudiantes = $stmtEstudiantes->fetchAll(PDO::FETCH_ASSOC);
    
            echo json_encode($estudiantes);
        } catch (PDOException $e) {
            echo "Error al obtener estudiantes: " . $e->getMessage();
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
    <title>Generador de Preguntas</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="modal.css">
    <style>
        h3 {
            margin-bottom: 15px;
        }
        label {
            margin-bottom: 10px;
        }
        label input {
            margin-right: 10px;
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
                <a href="#">Ver Exámenes</a>
                <div class="dropdown-content">
                    <a href="crear_examen.php">Crear Examen</a>
                    <a href="#">Exámenes</a>
                    <a href="imprimir_examen.php">Imprimir Examen</a>
                    <a href="calificaciones.php">Calificaciones</a>
                    <a href="resultados_examenes.php">Resultados de Exámenes</a>
                </div>
            </li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
    <input type="hidden" id="maestroId" value="<?php echo $maestro_id; ?>">
    <h1>Ver Examen</h1>
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
            <td>
                <label for="examenesSelect">Selecciona el examen:</label><br>
                <select id="examenesSelect" required>
                    <option value="">Seleccione un examen</option>
                </select>
            </td>
        </tr>
    </table>

    <button type="button" id="cargarExamenBtn">Cargar Examen</button>

    <h2 id="nombreExamenSeleccionado"></h2>
    <div id="examenContainer"></div>


    <form id="asignarExamenForm" method="POST">
        <!-- Unidad -->
        <label for="unidad">Unidad:</label>
        <select id="unidad" name="unidad">
            <option value="1">Unidad 1</option>
            <option value="2">Unidad 2</option>
            <option value="3">Unidad 3</option>
            <option value="4">Unidad 4</option>
            <option value="5">Unidad 5</option>
            <!-- Puedes agregar más unidades según sea necesario -->
        </select><br><br>

        <!-- Estudiantes -->
        <h3>Asignar Examen a Estudiantes</h3>
        <label for="estudiantesSelect">Selecciona estudiantes:</label><br>

        <!-- Aquí se mostrarán los estudiantes según el curso seleccionado -->
        <div id="estudiantesContainer"></div>

        <button type="button" id="asignarExamenBtn">Asignar Examen</button>
    </form>
    

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

        // Cargar los estudiantes cuando se seleccione el curso
        document.getElementById('cursosSelect').addEventListener('change', function() {
            const cursoId = this.value;

            if (cursoId) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'controladores/get_estudiantes.php?id_curso=' + cursoId, true);
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        const estudiantes = JSON.parse(xhr.responseText);
                        const estudiantesContainer = document.getElementById('estudiantesContainer');
                        estudiantesContainer.innerHTML = '';

                        if (estudiantes.length > 0) {
                            estudiantes.forEach(estudiante => {
                                const label = document.createElement('label');
                                label.innerHTML = `<input type="checkbox" name="estudiantes[]" value="${estudiante.id}"> ${estudiante.nombre_completo}`;
                                estudiantesContainer.appendChild(label);
                                estudiantesContainer.appendChild(document.createElement('br'));
                            });
                        } else {
                            estudiantesContainer.innerHTML = 'No hay estudiantes inscritos en este curso.';
                        }
                    }
                };
                xhr.send();
            } else {
                document.getElementById('estudiantesContainer').innerHTML = '';
            }
        });

        // Cargar los examenes cuando se seleccione el curso 
        document.getElementById('cursosSelect').addEventListener('change', function() {
            const cursoId = this.value;

            if (cursoId) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'controladores/get_examen.php?id_curso=' + cursoId, true);
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        const examenes = JSON.parse(xhr.responseText);
                        const examenesSelect = document.getElementById('examenesSelect');
                        examenesSelect.innerHTML = '<option value="">Seleccione un examen</option>';

                        examenes.forEach(examen => {
                            const option = document.createElement('option');
                            option.value = examen.id_examen;
                            option.textContent = examen.nombre;
                            examenesSelect.appendChild(option);
                        });
                    }
                };
                xhr.send();
            } else {
                document.getElementById('examenesSelect').innerHTML = '<option value="">Seleccione un examen</option>';
            }
        });

        // Lógica para cargar las preguntas del examen
        document.getElementById('cargarExamenBtn').addEventListener('click', function() {
            const examenId = document.getElementById('examenesSelect').value;

            if (examenId) {
                fetch(`controladores/get_preguntas.php?id_examen=${examenId}`)
                    .then(response => response.json())
                    .then(data => {
                        const examenContainer = document.getElementById('examenContainer');
                        examenContainer.innerHTML = '';

                        data.forEach((pregunta, index) => {
                            const preguntaDiv = document.createElement('div');
                            preguntaDiv.classList.add('pregunta');

                            const preguntaTitulo = document.createElement('h3');
                            preguntaTitulo.textContent = `${index + 1}. ${pregunta.pregunta}`;
                            preguntaDiv.appendChild(preguntaTitulo);

                            pregunta.opciones.forEach(opcion => {
                                const opcionLabel = document.createElement('label');
                                const opcionInput = document.createElement('input');

                                opcionInput.type = 'radio';
                                opcionInput.name = `pregunta_${pregunta.numero_pregunta}`;
                                opcionInput.value = opcion.id_opcion;

                                opcionLabel.appendChild(opcionInput);
                                opcionLabel.appendChild(document.createTextNode(opcion.descripcion));
                                preguntaDiv.appendChild(opcionLabel);
                                preguntaDiv.appendChild(document.createElement('br'));
                            });

                            examenContainer.appendChild(preguntaDiv);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar las preguntas:', error);
                    });
            } else {
                alert('Por favor, selecciona un examen.');
            }
        });


        // Lógica para asignar el examen a los estudiantes
        document.getElementById('asignarExamenBtn').addEventListener('click', function() {
            const examenId = document.getElementById('examenesSelect').value;
            const estudiantesSeleccionados = Array.from(document.querySelectorAll('input[name="estudiantes[]"]:checked'))
                                                .map(checkbox => checkbox.value);
            const unidad = document.getElementById('unidad').value;  // Obtenemos el valor de la unidad

            if (examenId && estudiantesSeleccionados.length > 0) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'controladores/asignar_examen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        alert('Examen asignado correctamente a los estudiantes seleccionados.');
                    } else {
                        alert('Error al asignar el examen.');
                    }
                };

                // Aquí agregamos la unidad al enviar la solicitud
                xhr.send('examen_id=' + examenId + '&estudiantes=' + estudiantesSeleccionados.join(',') + '&unidad=' + unidad);
            } else {
                alert('Por favor, selecciona un examen y al menos un estudiante.');
            }
        });
    </script>
</body>
</html>