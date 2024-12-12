<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pregunta = $_POST['id_pregunta'];
    $accion = $_POST['accion'];

    try {
        // Obtener el número de pregunta actual y el examen asociado
        $stmt = $conn->prepare("SELECT id_examen, numero_pregunta FROM preguntas WHERE id_pregunta = :id_pregunta");
        $stmt->execute(['id_pregunta' => $id_pregunta]);
        $preguntaActual = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$preguntaActual) {
            throw new Exception('Pregunta no encontrada.');
        }

        $id_examen = $preguntaActual['id_examen'];
        $numeroActual = $preguntaActual['numero_pregunta'];
        $numeroNuevo = ($accion === 'subir') ? $numeroActual - 1 : $numeroActual + 1;

        // Verificar que el nuevo número de pregunta exista en el examen
        $stmt = $conn->prepare("SELECT id_pregunta FROM preguntas WHERE id_examen = :id_examen AND numero_pregunta = :numero_nuevo");
        $stmt->execute(['id_examen' => $id_examen, 'numero_nuevo' => $numeroNuevo]);
        $preguntaIntercambio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$preguntaIntercambio) {
            throw new Exception('Movimiento no permitido.');
        }

        $idPreguntaIntercambio = $preguntaIntercambio['id_pregunta'];

        // Intercambiar los números de pregunta
        $conn->beginTransaction();

        $stmt = $conn->prepare("UPDATE preguntas SET numero_pregunta = :numero_nuevo WHERE id_pregunta = :id_pregunta");
        $stmt->execute(['numero_nuevo' => $numeroNuevo, 'id_pregunta' => $id_pregunta]);

        $stmt = $conn->prepare("UPDATE preguntas SET numero_pregunta = :numero_actual WHERE id_pregunta = :id_pregunta");
        $stmt->execute(['numero_actual' => $numeroActual, 'id_pregunta' => $idPreguntaIntercambio]);

        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
