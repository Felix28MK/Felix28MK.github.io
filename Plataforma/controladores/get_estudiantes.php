<?php
// Incluir el archivo de conexión
require_once 'conexion.php';

try {
    // Verificar si el parámetro id_curso está presente en la solicitud GET
    if (isset($_GET['id_curso'])) {
        $idCurso = $_GET['id_curso'];

        // Consulta para obtener los estudiantes inscritos en el curso específico sin duplicados
        $sql = "
            SELECT DISTINCT alumnos.id, alumnos.nombre_completo
            FROM alumnos
            JOIN inscripciones ON inscripciones.id_alumno = alumnos.id
            WHERE inscripciones.id_curso = :id_curso
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_curso', $idCurso, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener los resultados
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay estudiantes y devolverlos como JSON
        if ($estudiantes) {
            echo json_encode($estudiantes);
        } else {
            echo json_encode([]);
        }
    } else {
        echo json_encode(['error' => 'No se ha especificado un curso.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener los estudiantes: ' . $e->getMessage()]);
}
?>