<?php
// Incluir el archivo de conexión
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener los datos del examen y los estudiantes seleccionados
        $examenId = $_POST['examen_id'];
        $estudiantes = explode(',', $_POST['estudiantes']);  // Convertir los IDs de estudiantes en un array
        $unidad = $_POST['unidad']; 

        // Validar que se haya recibido el examen y los estudiantes
        if (!empty($examenId) && !empty($estudiantes) && !empty($unidad)) {
            // Insertar el examen para cada estudiante seleccionado
            $sqlInsertar = "
                INSERT INTO evaluacion (id_alumno, id_examen, unidad)
                VALUES (:id_alumno, :id_examen, :unidad)";  // Aquí asumo que la unidad es 1, pero puedes ajustarlo si es necesario

            $stmtInsertar = $conn->prepare($sqlInsertar);
            
            // Iterar sobre cada estudiante y asignar el examen
            foreach ($estudiantes as $estudiante) {
                $stmtInsertar->bindParam(':id_alumno', $estudiante);
                $stmtInsertar->bindParam(':id_examen', $examenId);
                $stmtInsertar->bindParam(':unidad', $unidad);
                $stmtInsertar->execute();
            }

            // Si la inserción fue exitosa
            echo 'Examen asignado correctamente a los estudiantes seleccionados.';
        } else {
            echo 'Por favor, selecciona un examen y al menos un estudiante.';
        }
    } catch (PDOException $e) {
        // Manejo de errores
        echo 'Error al asignar el examen: ' . $e->getMessage();
    }
} else {
    echo 'Método no permitido.';
}
?>
