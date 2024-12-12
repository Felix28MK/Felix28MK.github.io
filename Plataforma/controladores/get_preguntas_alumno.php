<?php
// Incluir el archivo de conexión
require_once 'conexion.php';

// Verificar si se recibió el parámetro 'id_examen' y 'id_alumno'
if (isset($_GET['id_examen']) && isset($_GET['id_alumno'])) {
    $id_examen = $_GET['id_examen'];
    $id_alumno = $_GET['id_alumno'];

    try {
        // Verificar si el alumno está registrado para el examen en la tabla 'evaluacion'
        $sqlEvaluacion = "SELECT * FROM evaluacion WHERE id_examen = :id_examen AND id_alumno = :id_alumno";
        $stmtEvaluacion = $conn->prepare($sqlEvaluacion);
        $stmtEvaluacion->bindParam(':id_examen', $id_examen, PDO::PARAM_STR);
        $stmtEvaluacion->bindParam(':id_alumno', $id_alumno, PDO::PARAM_STR);
        $stmtEvaluacion->execute();

        // Si el alumno no está registrado para este examen
        if ($stmtEvaluacion->rowCount() == 0) {
            echo json_encode(['error' => "No tienes acceso a este examen."]);
            exit;
        }

        // Consultar las preguntas del examen
        $sqlPreguntas = "SELECT * FROM preguntas WHERE id_examen = :id_examen";
        $stmtPreguntas = $conn->prepare($sqlPreguntas);
        $stmtPreguntas->bindParam(':id_examen', $id_examen, PDO::PARAM_STR);
        $stmtPreguntas->execute();
        $preguntas = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);

        $examenData = [];

        foreach ($preguntas as $pregunta) {
            $sqlOpciones = "SELECT id_opcion, descripcion FROM opciones WHERE id_pregunta = :id_pregunta";
            $stmtOpciones = $conn->prepare($sqlOpciones);
            $stmtOpciones->bindParam(':id_pregunta', $pregunta['id_pregunta'], PDO::PARAM_STR);
            $stmtOpciones->execute();
            $opciones = $stmtOpciones->fetchAll(PDO::FETCH_ASSOC);
        
            $examenData[] = [
                'pregunta' => $pregunta['pregunta'],
                'id_pregunta' => $pregunta['id_pregunta'],
                'numero_pregunta' => $pregunta['numero_pregunta'],
                'solucion' => $pregunta['solucion'],
                'opciones' => array_map(function($opcion) {
                    return [
                        'id_opcion' => $opcion['id_opcion'], // Aquí puedes mapear directamente 'A', 'B', etc.
                        'descripcion' => $opcion['descripcion']
                    ];
                }, $opciones)
            ];
        }

        // Devolver las preguntas y opciones como JSON
        echo json_encode($examenData);

    } catch (PDOException $e) {
        echo json_encode(['error' => "Error al obtener los datos: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => "Faltan los parámetros 'id_examen' o 'id_alumno'."]);
}
?>
