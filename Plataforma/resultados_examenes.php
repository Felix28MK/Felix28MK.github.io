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

        #estudiantesContainer {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .ficha-estudiante {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .ficha-estudiante:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .ficha-estudiante h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #333;
            text-align: center;
        }

        .ficha-estudiante p {
            font-size: 1em;
            margin-bottom: 15px;
            color: #555;
            text-align: center;
        }

        .ficha-estudiante ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .ficha-estudiante ul li {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 0.9em;
            color: #333;
        }

        .ficha-estudiante ul li strong {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
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
    <h1>Resupuestas de los Examenes</h1>
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

        // Cargar los resultados cuando se seleccione el examen
        document.getElementById('examenesSelect').addEventListener('change', function() {
            const examenId = this.value;

            if (examenId) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'controladores/get_resultados.php?id_examen=' + examenId, true);
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        const resultados = JSON.parse(xhr.responseText);
                        const estudiantesContainer = document.getElementById('estudiantesContainer');
                        estudiantesContainer.innerHTML = '';

                        // Agrupar resultados por alumno
                        const alumnos = {};
                        resultados.forEach(row => {
                            if (!alumnos[row.id_alumno]) {
                                alumnos[row.id_alumno] = {
                                    nombre: row.nombre_completo,
                                    calificacion: row.calificacion,
                                    respuestas: []
                                };
                            }
                            alumnos[row.id_alumno].respuestas.push({
                                pregunta: row.pregunta,
                                respuesta: row.respuesta,
                                solucion: row.solucion
                            });
                        });

                        // Crear fichas para cada alumno
                        for (const id in alumnos) {
                            const alumno = alumnos[id];

                            const ficha = document.createElement('div');
                            ficha.className = 'ficha-estudiante';
                            ficha.innerHTML = `
                                <h3>${alumno.nombre}</h3>
                                <p>Calificación: ${alumno.calificacion}</p>
                                <ul>
                                    ${alumno.respuestas.map(r => `
                                        <li>
                                            <strong>${r.pregunta}</strong><br>
                                            Respuesta: ${r.respuesta} - Solución: ${r.solucion}
                                        </li>
                                    `).join('')}
                                </ul>
                            `;
                            estudiantesContainer.appendChild(ficha);
                        }
                    }
                };
                xhr.send();
            } else {
                document.getElementById('estudiantesContainer').innerHTML = '';
            }
        });

    </script>
</body>
</html>