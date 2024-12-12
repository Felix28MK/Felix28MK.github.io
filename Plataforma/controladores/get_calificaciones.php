<?php
require_once 'conexion.php';

if (isset($_GET['id_curso'])) {
    $idCurso = $_GET['id_curso'];

    try {
        // Consulta para filtrar estudiantes por curso
        $query = "
            SELECT 
                i.id_alumno,
                CONCAT(a.nombre_completo) AS nombre_completo,
                i.unidad_1,
                i.unidad_2,
                i.unidad_3,
                i.unidad_4,
                i.unidad_5,
                i.calificacion_final
            FROM inscripciones i
            INNER JOIN alumnos a ON i.id_alumno = a.id
            WHERE i.id_curso = :id_curso
            GROUP BY i.id_alumno, i.id_curso
            ORDER BY a.nombre_completo ASC
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_curso', $idCurso, PDO::PARAM_STR);
        $stmt->execute();
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($estudiantes); // Devolver resultados en JSON
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al obtener estudiantes: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'ID de curso no proporcionado.']);
}
?>