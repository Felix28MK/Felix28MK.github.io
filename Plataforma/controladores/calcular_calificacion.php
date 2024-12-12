<?php
require 'conexion.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$alumnoId = $data['alumnoId'];
$examenId = $data['examenId'];

if (!$alumnoId || !$examenId) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

try {
    // Calcular respuestas correctas
    $stmt = $conn->prepare("SELECT COUNT(*) AS correctas
                            FROM respuestas r
                            JOIN preguntas p ON r.id_pregunta = p.id_pregunta
                            WHERE r.id_alumno = :alumnoId 
                              AND p.id_examen = :examenId 
                              AND r.respuesta = p.solucion");
    $stmt->execute(['alumnoId' => $alumnoId, 'examenId' => $examenId]);
    $correctas = $stmt->fetchColumn();

    // Contar total de preguntas del examen
    $stmt = $conn->prepare("SELECT COUNT(*) FROM preguntas WHERE id_examen = :examenId");
    $stmt->execute(['examenId' => $examenId]);
    $totalPreguntas = $stmt->fetchColumn();

    // Calcular calificación
    $calificacion = ($correctas / $totalPreguntas) * 100;

    // Guardar en tabla evaluación
    $stmt = $conn->prepare("INSERT INTO evaluacion (id_alumno, id_examen, unidad, calificacion)
                            VALUES (:alumnoId, :examenId, :unidad, :calificacion)
                            ON DUPLICATE KEY UPDATE calificacion = :calificacion");
    $stmt->execute([
        'alumnoId' => $alumnoId,
        'examenId' => $examenId,
        'unidad' => 1, // Cambia la unidad según corresponda
        'calificacion' => $calificacion
    ]);

    echo json_encode(['success' => true, 'calificacion' => $calificacion]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al calcular la calificación: ' . $e->getMessage()]);
}
?>
