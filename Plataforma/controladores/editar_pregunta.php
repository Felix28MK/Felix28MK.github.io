<?php
header('Content-Type: application/json');

include 'conexion.php'; // Tu archivo de conexión a la base de datos

$data = json_decode(file_get_contents('php://input'), true);

$idPregunta = $data['id_pregunta'];
$numeroPregunta = $data['numero_pregunta'];
$pregunta = $data['pregunta'];
$solucion = $data['solucion'];
$opciones = $data['opciones'];

try {
    // Actualizar la pregunta principal
    $sqlPregunta = "UPDATE preguntas 
                    SET numero_pregunta = :numero_pregunta, 
                        pregunta = :pregunta, 
                        solucion = :solucion 
                    WHERE id_pregunta = :id_pregunta";
    $stmt = $conn->prepare($sqlPregunta);
    $stmt->execute([
        ':numero_pregunta' => $numeroPregunta,
        ':pregunta' => $pregunta,
        ':solucion' => $solucion,
        ':id_pregunta' => $idPregunta,
    ]);

    // Actualizar las opciones
    foreach ($opciones as $opcion => $descripcion) {
        $sqlOpcion = "UPDATE opciones 
                      SET descripcion = :descripcion 
                      WHERE id_pregunta = :id_pregunta AND opcion = :opcion";
        $stmt = $conn->prepare($sqlOpcion);
        $stmt->execute([
            ':descripcion' => $descripcion,
            ':id_pregunta' => $idPregunta,
            ':opcion' => $opcion,
        ]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
