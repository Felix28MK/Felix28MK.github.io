<?php
// Incluir el archivo de conexión
require_once 'conexion.php';

if (isset($_GET['id_examen'])) {
    $idExamen = $_GET['id_examen'];

    try {
        // Consultar calificaciones y alumnos relacionados con el examen
        $sqlResultados = "
            SELECT 
                a.id AS id_alumno,
                a.nombre_completo,
                e.calificacion,
                p.id_pregunta,
                p.pregunta,
                r.respuesta,
                p.solucion
            FROM evaluacion e
            JOIN alumnos a ON e.id_alumno = a.id
            LEFT JOIN respuestas r ON e.id_alumno = r.id_alumno
            LEFT JOIN preguntas p ON r.id_pregunta = p.id_pregunta
            WHERE e.id_examen = :id_examen
            ORDER BY a.nombre_completo, p.numero_pregunta
        ";
        $stmtResultados = $conn->prepare($sqlResultados);
        $stmtResultados->bindParam(':id_examen', $idExamen);
        $stmtResultados->execute();
        $resultados = $stmtResultados->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($resultados);
    } catch (PDOException $e) {
        echo "Error al obtener resultados: " . $e->getMessage();
    }
    exit;
}
?>
