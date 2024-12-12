<?php
// Incluir archivo de conexión
require_once 'conexion.php';

header('Content-Type: application/json');

try {
    // Leer el JSON enviado al script
    $input = json_decode(file_get_contents("php://input"), true);

    // Validar datos obligatorios
    if (!isset($input['id_examen'], $input['pregunta'], $input['solucion'], $input['opciones'])) {
        throw new Exception("Faltan datos obligatorios.");
    }

    $idExamen = $input['id_examen'];
    $pregunta = $input['pregunta'];
    $solucion = $input['solucion'];
    $opciones = $input['opciones'];

    // Iniciar una transacción
    $conn->beginTransaction();

    // Calcular el número de la pregunta dentro del examen
    $queryNumero = "SELECT IFNULL(MAX(numero_pregunta), 0) + 1 AS nuevo_numero FROM preguntas WHERE id_examen = :id_examen";
    $stmt = $conn->prepare($queryNumero);
    $stmt->bindParam(':id_examen', $idExamen);
    $stmt->execute();
    $nuevoNumero = $stmt->fetchColumn();

    // Generar un ID único para la pregunta
    $idPregunta = uniqid("q_");

    // Insertar la pregunta
    $queryPregunta = "INSERT INTO preguntas (id_pregunta, id_examen, numero_pregunta, pregunta, solucion) 
                      VALUES (:id_pregunta, :id_examen, :numero_pregunta, :pregunta, :solucion)";
    $stmt = $conn->prepare($queryPregunta);
    $stmt->bindParam(':id_pregunta', $idPregunta);
    $stmt->bindParam(':id_examen', $idExamen);
    $stmt->bindParam(':numero_pregunta', $nuevoNumero);
    $stmt->bindParam(':pregunta', $pregunta);
    $stmt->bindParam(':solucion', $solucion);
    $stmt->execute();

    // Insertar las opciones asociadas a la pregunta
    $queryOpcion = "INSERT INTO opciones (id_opcion, id_pregunta, opcion, descripcion) 
                    VALUES (:id_opcion, :id_pregunta, :opcion, :descripcion)";
    $stmt = $conn->prepare($queryOpcion);

    foreach ($opciones as $opcion) {
        if (!isset($opcion['opcion'], $opcion['descripcion'])) {
            throw new Exception("Faltan datos en una de las opciones.");
        }

        $idOpcion = uniqid("o_");
        $stmt->bindParam(':id_opcion', $idOpcion);
        $stmt->bindParam(':id_pregunta', $idPregunta);
        $stmt->bindParam(':opcion', $opcion['opcion']);
        $stmt->bindParam(':descripcion', $opcion['descripcion']);
        $stmt->execute();
    }

    // Confirmar la transacción
    $conn->commit();

    echo json_encode(["status" => "success", "message" => "Pregunta y opciones agregadas correctamente."]);

} catch (Exception $e) {
    // Revertir la transacción si hay un error
    $conn->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
