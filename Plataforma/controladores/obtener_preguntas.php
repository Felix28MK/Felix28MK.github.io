<?php
// Incluir el archivo de conexión
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_examen = $_POST['id_examen'];

    try {
        // Consulta para obtener preguntas y sus opciones
        $sqlPreguntas = "
            SELECT p.id_pregunta, p.numero_pregunta, p.pregunta, p.solucion,
                   MAX(CASE WHEN o.opcion = 'A' THEN o.descripcion ELSE NULL END) AS opcionA,
                   MAX(CASE WHEN o.opcion = 'B' THEN o.descripcion ELSE NULL END) AS opcionB,
                   MAX(CASE WHEN o.opcion = 'C' THEN o.descripcion ELSE NULL END) AS opcionC,
                   MAX(CASE WHEN o.opcion = 'D' THEN o.descripcion ELSE NULL END) AS opcionD,
                   MAX(CASE WHEN o.opcion = 'E' THEN o.descripcion ELSE NULL END) AS opcionE
            FROM preguntas p
            LEFT JOIN opciones o ON p.id_pregunta = o.id_pregunta
            WHERE p.id_examen = :id_examen
            GROUP BY p.id_pregunta, p.pregunta, p.solucion
            ORDER BY p.numero_pregunta ASC
        ";

        $stmt = $conn->prepare($sqlPreguntas);
        $stmt->bindParam(':id_examen', $id_examen, PDO::PARAM_STR);
        $stmt->execute();

        $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($preguntas);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al obtener preguntas: ' . $e->getMessage()]);
    }
}
?>
