<?php
header('Content-Type: application/json');
require 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id_examen'], $data['id_alumno'])) {
    $id_examen = $data['id_examen'];
    $id_alumno = $data['id_alumno'];

    try {
        $stmt = $conn->prepare("SELECT calificacion FROM evaluacion WHERE id_examen = :id_examen AND id_alumno = :id_alumno");
        $stmt->execute([
            'id_examen' => $id_examen,
            'id_alumno' => $id_alumno
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode([
                'contestado' => true,
                'calificacion' => $result['calificacion']
            ]);
        } else {
            echo json_encode(['contestado' => false]);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al verificar el estado del examen']);
    }
} else {
    echo json_encode(['error' => 'Datos incompletos']);
}
?>
