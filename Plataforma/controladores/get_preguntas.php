<?php
// Incluir el archivo de conexión
require_once 'conexion.php';

// Verificar si se recibió el parámetro 'id_examen'
if (isset($_GET['id_examen'])) {
    $id_examen = $_GET['id_examen'];

    try {
        // Consultar las preguntas del examen
        $sqlPreguntas = "SELECT * FROM preguntas WHERE id_examen = :id_examen";
        $stmtPreguntas = $conn->prepare($sqlPreguntas);
        $stmtPreguntas->bindParam(':id_examen', $id_examen, PDO::PARAM_STR);
        $stmtPreguntas->execute();
        $preguntas = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);

        $examenData = [];

        foreach ($preguntas as $pregunta) {
            // Consultar las opciones de cada pregunta
            $sqlOpciones = "SELECT * FROM opciones WHERE id_pregunta = :id_pregunta";
            $stmtOpciones = $conn->prepare($sqlOpciones);
            $stmtOpciones->bindParam(':id_pregunta', $pregunta['id_pregunta'], PDO::PARAM_STR);
            $stmtOpciones->execute();
            $opciones = $stmtOpciones->fetchAll(PDO::FETCH_ASSOC);

            // Construir el array para cada pregunta
            $examenData[] = [
                'pregunta' => $pregunta['pregunta'],
                'numero_pregunta' => $pregunta['numero_pregunta'],
                'solucion' => $pregunta['solucion'],
                'opciones' => $opciones
            ];
        }

        // Devolver las preguntas y opciones como JSON
        echo json_encode($examenData);
    } catch (PDOException $e) {
        echo json_encode(['error' => "Error al obtener los datos: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => "Falta el parámetro 'id_examen'."]);
}
?>
