<?php
// Incluir la conexión PDO
include('controladores/conexion.php'); // Asegúrate de tener este archivo correctamente configurado

// Mensajes de feedback
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $id_materia = $_POST['id_materia'];
    $id_maestro = $_POST['id_maestro'];
    $grado = $_POST['grado'];
    $grupo = $_POST['grupo'];
    $horario = $_POST['horario'];
    $salon = $_POST['salon'];
    
    // Generar el ID del curso automáticamente
    $id_curso = strtoupper("CUR-{$id_materia}-{$grupo}{$grado}");
    
    // Validar que todos los campos estén completos
    if ($id_materia && $id_maestro && $grado && $grupo && $horario && $salon) {
        try {
            // Insertar el curso en la base de datos
            $query = "INSERT INTO cursos (id, id_materia, id_maestro, grado, grupo, horario, salon) 
                      VALUES (:id_curso, :id_materia, :id_maestro, :grado, :grupo, :horario, :salon)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_curso', $id_curso);
            $stmt->bindParam(':id_materia', $id_materia);
            $stmt->bindParam(':id_maestro', $id_maestro);
            $stmt->bindParam(':grado', $grado);
            $stmt->bindParam(':grupo', $grupo);
            $stmt->bindParam(':horario', $horario);
            $stmt->bindParam(':salon', $salon);
            
            // Ejecutar la consulta
            $stmt->execute();
            $message = "Curso asignado correctamente.";
        } catch (PDOException $e) {
            $message = "Error al asignar el curso: " . $e->getMessage();
        }
    } else {
        $message = "Por favor, completa todos los campos.";
    }
}

// Obtener maestros y materias para el formulario
try {
    // Consultar maestros
    $maestros = $conn->query("SELECT id, nombre_completo FROM maestros");
    
    // Consultar materias
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
    <title>Coordinador Dashboard</title>
    <!-- Incluir las hojas de estilo -->
    <link rel="stylesheet" href="maestros.css">
    <link rel="stylesheet" href="lista.css">
</head>
<body>

    <!-- Barra de navegación -->
    <div class="navbar">
        <div class="navbar-brand">Panel del Coordinador</div>
        <ul class="navbar-menu">
            <li><a href="coordinador_dashboard.php">Inicio</a></li>
            <li><a href="#">Registrar Cursos</a></li>
            <li><a href="cursos.php">Ver Cursos</a></li>
            <li><a href="asignar_materias.php">Asignar materias</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>Asignar Cursos</h1>
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-section">
                <label for="id_materia">Materia:</label>
                <select name="id_materia" id="id_materia" required>
                    <option value="">Selecciona una materia</option>
                    <?php while ($materia = $materias->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $materia['id']; ?>">
                            <?php echo $materia['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-section">
                <label for="id_maestro">Maestro:</label>
                <select name="id_maestro" id="id_maestro" required>
                    <option value="">Selecciona un maestro</option>
                    <?php while ($maestro = $maestros->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $maestro['id']; ?>">
                            <?php echo $maestro['nombre_completo']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-section">
                <label for="grado">Grado:</label>
                <select name="grado" id="grado" required>
                    <option value="">Selecciona un grado</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>

            <div class="form-section">
                <label for="grupo">Grupo:</label>
                <select name="grupo" id="grupo" required>
                    <option value="">Selecciona un grupo</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <div class="form-section">
                <label for="horario">Horario:</label>
                <input type="text" name="horario" id="horario" placeholder="Ejemplo: 08:00-09:00" required>
            </div>

            <div class="form-section">
                <label for="salon">Salón:</label>
                <input type="text" name="salon" id="salon" placeholder="Ejemplo: 101" required>
            </div>

            <button type="submit">Asignar Curso</button>
            <a href="cursos.php">Ver cursos</a>
        </form>
    </div>
</body>
</html>