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

    // Obtener la lista de exámenes
    $sqlExamenes = "SELECT * FROM examenes";
    $stmtExamenes = $conn->query($sqlExamenes);

    echo "<h2>Lista de Exámenes</h2>";
    while ($examen = $stmtExamenes->fetch(PDO::FETCH_ASSOC)) {
        // Obtener las preguntas del examen actual
        $sqlPreguntas = "SELECT * FROM preguntas WHERE id_examen = :id_examen";
        $stmtPreguntas = $conn->prepare($sqlPreguntas);
        $stmtPreguntas->bindParam(':id_examen', $examen['id_examen'], PDO::PARAM_STR);
        $stmtPreguntas->execute();

        while ($pregunta = $stmtPreguntas->fetch(PDO::FETCH_ASSOC)) {
            

            // Obtener las opciones para la pregunta actual
            $sqlOpciones = "SELECT * FROM opciones WHERE id_pregunta = :id_pregunta";
            $stmtOpciones = $conn->prepare($sqlOpciones);
            $stmtOpciones->bindParam(':id_pregunta', $pregunta['id_pregunta'], PDO::PARAM_STR);
            $stmtOpciones->execute();

            while ($opcion = $stmtOpciones->fetch(PDO::FETCH_ASSOC)) {
                
            }

            // Obtener las respuestas relacionadas con la pregunta actual
            $sqlRespuestas = "SELECT * FROM respuestas WHERE id_pregunta = :id_pregunta";
            $stmtRespuestas = $conn->prepare($sqlRespuestas);
            $stmtRespuestas->bindParam(':id_pregunta', $pregunta['id_pregunta'], PDO::PARAM_STR);
            $stmtRespuestas->execute();

            while ($respuesta = $stmtRespuestas->fetch(PDO::FETCH_ASSOC)) {
                
            }
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
                    <a href="#">Crear Examen</a>
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
    <h1>Crear o Modificar Examen y Preguntas</h1>
    <table> 
        <tr>
            <td>
                <label for="nombreExamen">Nombre del Examen:</label>
                <input type="text" id="nombreExamen" placeholder="Introduce el nombre del examen" required>
            </td>
            <td>
            <label for="materiasSelect">Selecciona la materia:</label>
            <select id="materiasSelect" required>
                <?php
                // Generar las opciones del select con las materias
                foreach ($materias as $materias) {
                    echo "<option value='{$materias['id']}'>{$materias['nombre']}</option>";
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
    <table width="1200px">
        <tr>
            <td> 
                <button type="button" id="crearExamenBtn">Crear Nuevo Examen</button>
            </td>
            <td>
                <button type="button" id="editarExamenBtn" class="hidden">Editar Examen</button>
            </td>
            <td>
                <button type="button" id="eliminarExamenBtn" class="hidden">Eliminar Examen</button>
            </td>
        </tr>
    </table>


    <h2>Seleccionar Examen para Modificar</h2>
    <select id="examenesSelect">
        <option value="">Selecciona un examen</option>
    </select>
    <button type="button" id="cargarExamenBtn">Cargar Examen</button>

    <h2 id="nombreExamenSeleccionado"></h2>

    <div id="examenContainer" class="hidden">
        <h2>Agregar Preguntas</h2>
        <form id="question-form">
            <input type="text" id="pregunta" placeholder="Pregunta" required>
            <input type="text" id="opcionA" placeholder="Opción A" required>
            <input type="text" id="opcionB" placeholder="Opción B" required>
            <input type="text" id="opcionC" placeholder="Opción C">
            <input type="text" id="opcionD" placeholder="Opción D">
            <input type="text" id="opcionE" placeholder="Opción E">
            
            <table width = "800">
                <tr>
                    <td>
                        <label class="custom-radio">
                            <input type="radio" name="solucion" value="A" required>
                            <div class="radio-circle"></div>
                            <span>Opción A</span>
                        </label>
                    </td>
                    <td>
                        <label class="custom-radio">
                            <input type="radio" name="solucion" value="B" required>
                            <div class="radio-circle"></div>
                            <span>Opción B</span>
                        </label>
                    </td>
                    <td>
                        <label class="custom-radio">
                            <input type="radio" name="solucion" value="C">
                            <div class="radio-circle"></div>
                            <span>Opción C</span>
                        </label>
                    </td>
                    <td>
                        <label class="custom-radio">
                            <input type="radio" name="solucion" value="D">
                            <div class="radio-circle"></div>
                            <span>Opción D</span>
                        </label>
                    </td>
                    <td>
                        <label class="custom-radio">
                            <input type="radio" name="solucion" value="E">
                            <div class="radio-circle"></div>
                            <span>Opción E</span>
                        </label>
                    </td>
                </tr>
            </table>
            
            <button type="submit" id="agregarPreguntaBtn">Agregar Pregunta</button>
        </form>
    </div>

    <h2>Preguntas Creadas</h2>
    <table id="preguntas-tabla">
        <thead>
            <tr>
                <th>No.</th>
                <th>Pregunta</th>
                <th>Opción A</th>
                <th>Opción B</th>
                <th>Opción C</th>
                <th>Opción D</th>
                <th>Opción E</th>
                <th>Solución</th>
                <th> </th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

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

        // Crear examen
        document.getElementById('crearExamenBtn').addEventListener('click', function() {
            const nombre = document.getElementById('nombreExamen').value;
            const idCurso = document.getElementById('cursosSelect').value;

            // Crear el objeto con los datos para enviar
            const requestData = {
                nombre_examen: nombre,
                id_curso: idCurso
            };

            fetch('controladores/create_examen.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json' // Asegurarse de que el servidor interprete los datos como JSON
                },
                body: JSON.stringify(requestData) // Convertir los datos a JSON
            })
            .then(response => response.json()) // Respuesta en formato JSON
            .then(data => {
                if (data.status === 'success') {
                    alert('Examen creado con éxito. ID: ' + data.id_examen);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Cargar los exámenes cuando se seleccione el curso
        document.getElementById('cursosSelect').addEventListener('change', function() {
            const cursoId = this.value;

            if (cursoId) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'controladores/get_examenes.php?id_curso=' + cursoId, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const examenes = JSON.parse(xhr.responseText);

                        const examenesSelect = document.getElementById('examenesSelect');
                        examenesSelect.innerHTML = '<option value="">Seleccione un examen</option>';

                        if (examenes.status === "error") {
                            console.error('Error desde el servidor:', examenes.message);
                            alert(`Error: ${examenes.message}`);
                            return;
                        }

                        if (examenes.length === 0) {
                            examenesSelect.innerHTML = '<option value="">No hay exámenes disponibles</option>';
                        } else {
                            examenes.forEach(examen => {
                                const option = document.createElement('option');
                                option.value = examen.id_examen;
                                option.textContent = examen.nombre;
                                examenesSelect.appendChild(option);
                            });
                        }
                    } else {
                        console.error('Error en la solicitud:', xhr.status);
                    }
                };
                xhr.onerror = function() {
                    console.error('Error al realizar la solicitud.');
                };
                xhr.send();
            } else {
                document.getElementById('examenesSelect').innerHTML = '<option value="">Seleccione un examen</option>';
            }
        });


        document.getElementById('question-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Recopilar los datos del formulario
            const formData = {
                id_examen: document.getElementById('examenesSelect').value,
                pregunta: document.getElementById('pregunta').value,
                solucion: document.querySelector('input[name="solucion"]:checked')?.value,
                opciones: [
                    { opcion: 'A', descripcion: document.getElementById('opcionA').value },
                    { opcion: 'B', descripcion: document.getElementById('opcionB').value },
                    { opcion: 'C', descripcion: document.getElementById('opcionC').value },
                    { opcion: 'D', descripcion: document.getElementById('opcionD').value },
                    { opcion: 'E', descripcion: document.getElementById('opcionE').value },
                ].filter(opcion => opcion.descripcion.trim() !== "") // Filtrar opciones vacías
            };

            // Validación básica antes de enviar
            if (!formData.id_examen || !formData.pregunta || !formData.solucion || formData.opciones.length === 0) {
                alert('Por favor, completa todos los campos requeridos.');
                return;
            }

            try {
                // Enviar datos al servidor
                const response = await fetch('controladores/agregar_pregunta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                // Manejar la respuesta
                const result = await response.json();

                if (result.status === 'success') {
                    alert(result.message);
                    // Limpiar el formulario
                    document.getElementById('question-form').reset();
                    document.getElementById('cargarExamenBtn').click();
                } else {
                    throw new Error(result.message || 'Error desconocido.');
                }
            } catch (error) {
                console.error('Error al enviar los datos:', error);
                alert('Hubo un problema al guardar la pregunta. Por favor, inténtalo de nuevo.');
            }
        });

        document.getElementById('cargarExamenBtn').addEventListener('click', function () {
            const idExamen = document.getElementById('examenesSelect').value;

            // Asegurarse de que haya un examen seleccionado
            if (!idExamen) return;

            fetch('controladores/obtener_preguntas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ id_examen: idExamen }),
            })
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#preguntas-tabla tbody');
                    tbody.innerHTML = '';

                    // Recorrer las preguntas y agregarlas a la tabla
                    data.forEach((pregunta, index) => {
                        const row = `
                            <tr>
                                <td>${pregunta.numero_pregunta}</td>
                                <td>${pregunta.pregunta}</td>
                                <td>${pregunta.opcionA || ''}</td>
                                <td>${pregunta.opcionB || ''}</td>
                                <td>${pregunta.opcionC || ''}</td>
                                <td>${pregunta.opcionD || ''}</td>
                                <td>${pregunta.opcionE || ''}</td>
                                <td>${pregunta.solucion || ''}</td>
                                <td>
                                    <button class="subir" data-id="${pregunta.id_pregunta}">↑</button>
                                    <button class="bajar" data-id="${pregunta.id_pregunta}">↓</button>
                                </td>
                                <td>
                                    <button class="editar" data-id="${pregunta.id_pregunta}">Editar</button>
                                    <button class="eliminar" data-id="${pregunta.id_pregunta}">Eliminar</button>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                })
                .catch(error => console.error('Error:', error));
        });

        document.querySelector('#preguntas-tabla').addEventListener('click', function (e) {
            if (e.target.classList.contains('eliminar')) {
                const idPregunta = e.target.getAttribute('data-id');

                if (confirm('¿Estás seguro de que deseas eliminar esta pregunta?')) {
                    fetch('controladores/eliminar_pregunta.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({ id_pregunta: idPregunta }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Pregunta eliminada exitosamente.');
                                document.getElementById('cargarExamenBtn').click(); // Recargar las preguntas
                            } else {
                                alert('Error al eliminar la pregunta: ' + (data.message || 'Desconocido.'));
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('subir') || e.target.classList.contains('bajar')) {
                const idPregunta = e.target.dataset.id;
                const accion = e.target.classList.contains('subir') ? 'subir' : 'bajar';

                fetch('controladores/mover_pregunta.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id_pregunta: idPregunta, accion: accion }),
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('Orden actualizado.');
                            // Recargar la lista de preguntas
                            document.getElementById('cargarExamenBtn').click();
                        } else {
                            alert(`Error: ${result.message}`);
                        }
                    })
                    .catch(error => console.error('Error al mover la pregunta:', error));
            }
        });

        document.querySelector('#preguntas-tabla').addEventListener('click', function (e) {
            if (e.target.classList.contains('editar')) {

                if (!confirm('¿Estás seguro de editar la pregunta?, no podrás cambiar el número de opciones')) {
                    return;
                }

                const btnEditar = e.target;
                const row = btnEditar.closest('tr');
                const idPregunta = btnEditar.getAttribute('data-id');
                
                // Desactivar el botón de editar mientras está en edición
                btnEditar.disabled = true;

                // Obtener los datos actuales de la fila
                const cells = row.querySelectorAll('td');
                const numeroPregunta = cells[0].textContent.trim();
                const pregunta = cells[1].textContent.trim();
                const opciones = Array.from(cells).slice(2, 7).map(cell => cell.textContent.trim());
                const solucion = cells[7].textContent.trim();

                // Reemplazar las celdas con inputs para edición
                cells[0].innerHTML = `<input type="number" value="${numeroPregunta}" min="1" class="numero-pregunta">`;
                cells[1].innerHTML = `<input type="text" value="${pregunta}" class="pregunta">`;
                opciones.forEach((opcion, index) => {
                    cells[2 + index].innerHTML = `<input type="text" value="${opcion}" class="opcion opcion-${'ABCDE'[index]}">`;
                });
                cells[7].innerHTML = `<input type="text" value="${solucion}" class="solucion">`;

                // Reemplazar botones con Guardar y Cancelar
                const actionCell = row.querySelector('td:last-child');
                actionCell.innerHTML = `
                    <button class="guardar" data-id="${idPregunta}">Guardar</button>
                    <button class="cancelar">Cancelar</button>
                `;
            }

            if (e.target.classList.contains('guardar')) {
                const btnGuardar = e.target;
                const row = btnGuardar.closest('tr');
                const idPregunta = btnGuardar.getAttribute('data-id');

                // Obtener valores editados
                const numeroPregunta = row.querySelector('.numero-pregunta').value;
                const pregunta = row.querySelector('.pregunta').value;
                const opciones = Array.from(row.querySelectorAll('.opcion')).map(input => input.value);
                const solucion = row.querySelector('.solucion').value;

                // Enviar datos al servidor
                fetch('controladores/editar_pregunta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_pregunta: idPregunta,
                        numero_pregunta: numeroPregunta,
                        pregunta,
                        opciones: {
                            A: opciones[0],
                            B: opciones[1],
                            C: opciones[2],
                            D: opciones[3],
                            E: opciones[4],
                        },
                        solucion,
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Actualizar los valores en la tabla
                            const cells = row.querySelectorAll('td');
                            cells[0].textContent = numeroPregunta;
                            cells[1].textContent = pregunta;
                            opciones.forEach((opcion, index) => {
                                cells[2 + index].textContent = opcion;
                            });
                            cells[7].textContent = solucion;

                            // Restaurar los botones
                            const actionCell = row.querySelector('td:last-child');
                            actionCell.innerHTML = `
                                <button class="editar" data-id="${idPregunta}">Editar</button>
                                <button class="eliminar" data-id="${idPregunta}">Eliminar</button>
                            `;
                        } else {
                            alert('Error al guardar los cambios.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al guardar los cambios.');
                    });
            }

            if (e.target.classList.contains('cancelar')) {
                // Recargar las preguntas del examen para descartar cambios
                document.getElementById('cargarExamenBtn').click();
            }
        });
    </script>
</body>
</html>