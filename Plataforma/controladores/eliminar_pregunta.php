<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPregunta = $_POST['id_pregunta'];

    try {
        // Iniciar una transacción
        $conn->beginTransaction();

        // Obtener el ID del examen y el número de pregunta
        $query = $conn->prepare("SELECT id_examen, numero_pregunta FROM preguntas WHERE id_pregunta = :id_pregunta");
        $query->execute([':id_pregunta' => $idPregunta]);
        $pregunta = $query->fetch(PDO::FETCH_ASSOC);

        if ($pregunta) {
            $idExamen = $pregunta['id_examen'];
            $numeroPregunta = $pregunta['numero_pregunta'];

            // Eliminar las opciones relacionadas
            $deleteOpciones = $conn->prepare("DELETE FROM opciones WHERE id_pregunta = :id_pregunta");
            $deleteOpciones->execute([':id_pregunta' => $idPregunta]);

            // Eliminar la pregunta
            $deletePregunta = $conn->prepare("DELETE FROM preguntas WHERE id_pregunta = :id_pregunta");
            $deletePregunta->execute([':id_pregunta' => $idPregunta]);

            // Actualizar el número de las preguntas restantes en el mismo examen
            $updateNumeros = $conn->prepare("
                UPDATE preguntas 
                SET numero_pregunta = numero_pregunta - 1
                WHERE id_examen = :id_examen AND numero_pregunta > :numero_pregunta
            ");
            $updateNumeros->execute([
                ':id_examen' => $idExamen,
                ':numero_pregunta' => $numeroPregunta,
            ]);

            // Confirmar los cambios
            $conn->commit();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Pregunta no encontrada.']);
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la pregunta: ' . $e->getMessage()]);
    }
}
?>
