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

// Consultar información del alumno desde la tabla `alumnos`
$stmt = $conn->prepare("SELECT * FROM alumnos WHERE id = :id");
$stmt->execute(['id' => $alumno_id]);
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no hay datos del alumno, redirigir a la página de "Datos del Alumno"
if (!$alumno || empty(trim($alumno['id']))) {
    header("Location: datos_alumno.php");
    exit;
}

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
    <title>Materias Inscritas</title>
    <link rel="stylesheet" href="alumno.css">
    <link rel="stylesheet" href="lista.css">
    <link rel="stylesheet" href="styles.css">
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
        <div class="navbar-brand">Panel de Alumnos</div>
        <ul class="navbar-menu">
            <li><a href="alumno_dashboard.php">Inicio</a></li>
            <li><a href="datos_alumno.php">Datos Personales</a></li>
            <li><a href="materias.php">Materias</a></li>
            <li><a href="examenes_alumno.php">Exámenes</a></li>
            <li><a href="logout.php" id="logout">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <h1>Ver Examen</h1>
        <input type="hidden" id="alumnoId" value="<?php echo $_SESSION['user_id']; ?>">
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
        <div id="examen">
            <div id="examenContainer">
            </div>
            <button type="button" id="enviarRespuestasBtn">Enviar Respuestas</button>
        </div>
        
    <script>
        // Cargar los cursos cuando se seleccione la materia
        document.getElementById('materiasSelect').addEventListener('change', function() {
            const materiaId = this.value;

            if (materiaId) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'controladores/get_cursos.php?id_materia=' + materiaId, true);
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

        //script de examenes alumno.php que muestra el examen
        document.getElementById('cargarExamenBtn').addEventListener('click', function () {
            const examenId = document.getElementById('examenesSelect').value;
            const alumnoId = document.getElementById('alumnoId').value;

            if (examenId && alumnoId) {
                fetch(`controladores/get_preguntas_alumno.php?id_examen=${examenId}&id_alumno=${alumnoId}`)
                    .then(response => response.json())
                    .then(data => {
                        const examenContainer = document.getElementById('examenContainer');
                        examenContainer.innerHTML = '';

                        if (data.error) {
                            examenContainer.innerHTML = `<p>${data.error}</p>`;
                        } else {
                            data.forEach((pregunta, index) => {
                                const preguntaDiv = document.createElement('div');
                                preguntaDiv.classList.add('pregunta');

                                const preguntaTitulo = document.createElement('h3');
                                preguntaTitulo.textContent = `${index + 1}. ${pregunta.pregunta}`;
                                preguntaDiv.appendChild(preguntaTitulo);

                                pregunta.opciones.forEach((opcion, idx) => {
                                    const opcionLabel = document.createElement('label');
                                    const opcionInput = document.createElement('input');

                                    opcionInput.type = 'radio';
                                    opcionInput.id = `${pregunta.id_pregunta}`;
                                    opcionInput.name = `pregunta_${pregunta.numero_pregunta}`;
                                    opcionInput.value = String.fromCharCode(65 + idx); // Convertimos índice (0-4) a letra (A-E)

                                    opcionLabel.appendChild(opcionInput);
                                    opcionLabel.appendChild(document.createTextNode(`${String.fromCharCode(65 + idx)}. ${opcion.descripcion}`));
                                    preguntaDiv.appendChild(opcionLabel);
                                    preguntaDiv.appendChild(document.createElement('br'));
                                });

                                examenContainer.appendChild(preguntaDiv);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar las preguntas:', error);
                    });
            } else {
                alert('Por favor, selecciona un examen y asegúrate de que el ID de alumno esté correcto.');
            }
        });

        document.getElementById('enviarRespuestasBtn').addEventListener('click', function () {
            const examenId = document.getElementById('examenesSelect').value;
            const alumnoId = document.getElementById('alumnoId').value;

            // Función para verificar si el examen ya está contestado
            const verificarExamen = async () => {
                try {
                    const response = await fetch('controladores/verificar_examen.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_examen: examenId, id_alumno: alumnoId })
                    });
                    
                    if (!response.ok) {
                        const error = await response.json();
                        throw new Error(error.error || 'Error al verificar el estado del examen');
                    }

                    return await response.json();
                } catch (error) {
                    console.error('Error al verificar el examen:', error);
                    throw error;
                }
            };

            // Obtener y procesar respuestas seleccionadas
            const obtenerRespuestas = () => {
                const respuestas = [];
                const preguntaDivs = document.querySelectorAll('.pregunta');
                preguntaDivs.forEach(preguntaDiv => {
                    const preguntaId = preguntaDiv.querySelector('h3').textContent.split('.')[0];
                    const seleccionada = preguntaDiv.querySelector('input[type="radio"]:checked');

                    if (seleccionada) {
                        respuestas.push({
                            id_pregunta: seleccionada.id,
                            numero_pregunta: preguntaId,
                            respuesta: seleccionada.value
                        });
                    }
                });

                return respuestas;
            };

            // Enviar las respuestas al servidor
            const enviarRespuestas = async (respuestas) => {
                try {
                    const datos = {
                        id_examen: examenId,
                        id_alumno: alumnoId,
                        respuestas: respuestas
                    };

                    const response = await fetch('controladores/guardar_respuestas.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(datos)
                    });

                    if (!response.ok) {
                        const error = await response.json();
                        throw new Error(error.error || 'Error desconocido al guardar las respuestas');
                    }

                    const data = await response.json();
                    if (data.success) {
                        alert(`Respuestas enviadas correctamente. Tu calificación es: ${data.calificacion}`);
                    } else {
                        alert('Error al enviar respuestas: ' + data.error);
                    }
                } catch (error) {
                    console.error('Error al enviar las respuestas:', error);
                    alert('Error: ' + error.message);
                }
            };

            // Manejar la lógica principal
            (async () => {
                try {
                    // Verificar si el examen ya ha sido contestado
                    const resultadoVerificacion = await verificarExamen();

                    if (resultadoVerificacion.contestado) {
                        alert(`Este examen ya ha sido contestado. Tu calificación es: ${resultadoVerificacion.calificacion}`);
                    } else {
                        const respuestas = obtenerRespuestas();
                        if (respuestas.length > 0) {
                            await enviarRespuestas(respuestas);
                        } else {
                            alert('Por favor, selecciona una respuesta para cada pregunta.');
                        }
                    }
                } catch (error) {
                    alert('Error al procesar la solicitud: ' + error.message);
                }
            })();
        });
    </script>
</body>
</html>