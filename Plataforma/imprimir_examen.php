<?php
session_start();

// Verificar si el usuario está autenticado y es un maestro
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'maestro') {
    header("Location: login.php"); // Redirigir al login si no está autenticado o no es maestro
    exit;
}

// Incluir el archivo de conexión
require('fpdf/fpdf.php');
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

if (isset($_GET['id_examen'])) {
    $id_examen = $_GET['id_examen'];

    try {
        // Obtener el nombre del examen
        $sqlExamen = "SELECT nombre FROM examenes WHERE id_examen = ?";
        $stmtExamen = $conn->prepare($sqlExamen);
        $stmtExamen->execute([$id_examen]);
        $examen = $stmtExamen->fetch(PDO::FETCH_ASSOC);

        if ($examen) {
            // Obtener las preguntas y opciones del examen
            $sqlPreguntas = "SELECT p.numero_pregunta, p.pregunta, o.opcion, o.descripcion 
                             FROM preguntas p 
                             LEFT JOIN opciones o ON p.id_pregunta = o.id_pregunta 
                             WHERE p.id_examen = ? 
                             ORDER BY p.numero_pregunta, o.opcion";
            $stmtPreguntas = $conn->prepare($sqlPreguntas);
            $stmtPreguntas->execute([$id_examen]);
            $preguntas = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);

            if ($preguntas) {
                // Inicia el PDF
                $pdf = new FPDF();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 16);

                // Título del examen
                $pdf->Cell(0, 10, utf8_decode($examen['nombre']), 0, 1, 'C');
                $pdf->Ln(10);

                // Agrupar preguntas con sus opciones
                $preguntasAgrupadas = [];
                foreach ($preguntas as $pregunta) {
                    $numeroPregunta = $pregunta['numero_pregunta'];
                    if (!isset($preguntasAgrupadas[$numeroPregunta])) {
                        $preguntasAgrupadas[$numeroPregunta] = [
                            'pregunta' => $pregunta['pregunta'],
                            'opciones' => []
                        ];
                    }
                    $preguntasAgrupadas[$numeroPregunta]['opciones'][] = [
                        'opcion' => $pregunta['opcion'],
                        'descripcion' => $pregunta['descripcion']
                    ];
                }

                // Mostrar preguntas y opciones
                foreach ($preguntasAgrupadas as $numero => $datosPregunta) {
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->MultiCell(0, 10, utf8_decode("Pregunta $numero: " . $datosPregunta['pregunta']));
                    $pdf->SetFont('Arial', '', 12);
                    foreach ($datosPregunta['opciones'] as $opcion) {
                        $pdf->Cell(10, 10, $opcion['opcion'] . ') ' . utf8_decode($opcion['descripcion']), 0, 1);
                    }
                    $pdf->Ln(5);
                }

                // Salida del PDF
                $pdf->Output('D', 'examen.pdf'); // Descargar el archivo
            } else {
                echo "No se encontraron preguntas para el examen seleccionado.";
            }
        } else {
            echo "No se encontró el examen con el ID proporcionado.";
        }
    } catch (PDOException $e) {
        echo "Error al generar el PDF: " . $e->getMessage();
    }
} else {
    echo "ID de examen no proporcionado.";
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
            margin-bottom: 10px; /* Espacio entre las opciones */
        }

        label input {
            margin-right: 10px; /* Espacio entre el radio button y el texto */
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
                    <a href="ver_examenes.php">Examenes</a>
                    <a href="#">Imprimir Examen</a>
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

    <button type="button" id="generarPdfBtn">Imprimir Examen (PDF)</button>

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

        document.getElementById('cursosSelect').addEventListener('change', function() {
            const cursoId = this.value;  // Obtener el ID de la curso seleccionada

            // Comprobar si se ha seleccionado una curso
            if (cursoId) {
                // Crear una solicitud AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'controladores/get_examen.php?id_curso=' + cursoId, true); // URL al archivo PHP que gestionará la consulta
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        const examenes = JSON.parse(xhr.responseText);  // Convertir la respuesta JSON en un objeto

                        // Limpiar el select de examenes
                        const examensSelect = document.getElementById('examenesSelect');
                        examensSelect.innerHTML = '<option value="">Seleccione un examen</option>';  // Opcional: colocar un mensaje de "Seleccione un examen"

                        // Llenar el select de examenes con los datos obtenidos
                        examenes.forEach(examen => {
                            const option = document.createElement('option');
                            option.value = examen.id_examen;  // ID del examen
                            option.textContent = examen.id_examen;  // ID del examen
                            examenesSelect.appendChild(option);
                        });
                    }
                };
                xhr.send();
            } else {
                // Si no se selecciona una curso, limpiar el select de examenes
                document.getElementById('examenesSelect').innerHTML = '<option value="">Seleccione un examen</option>';
            }
        });

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

                            // Mostrar la pregunta
                            const preguntaTitulo = document.createElement('h3');
                            preguntaTitulo.textContent = `${index + 1}. ${pregunta.pregunta}`;
                            preguntaDiv.appendChild(preguntaTitulo);

                            // Mostrar las opciones como botones de radio
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

        document.getElementById('generarPdfBtn').addEventListener('click', function() {
            const examenId = document.getElementById('examenesSelect').value;

            if (examenId) {
                // Redirigir a la página que genera el PDF
                window.location.href = `imprimir_examen.php?id_examen=${examenId}`;
            } else {
                alert('Por favor, selecciona un examen.');
            }
        });
    </script>
</body>
</html>