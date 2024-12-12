<?php
header('Content-Type: application/json');
require 'conexion.php'; // Conexión a la base de datos

// Obtener los datos enviados
$data = json_decode(file_get_contents('php://input'), true);

// Verifica si los datos se decodificaron correctamente
if ($data === null) {
    echo json_encode(['success' => false, 'error' => 'Datos JSON inválidos o no recibidos']);
    error_log('Error al decodificar JSON: ' . json_last_error_msg());
    exit;
}

// Mostrar los datos recibidos para depuración
error_log(print_r($data, true)); // Log de los datos recibidos

if (isset($data['id_examen'], $data['id_alumno'], $data['respuestas'])) {
    $id_examen = $data['id_examen'];
    $id_alumno = $data['id_alumno'];
    $respuestas = $data['respuestas'];

    try {
        $stmt = $conn->prepare("INSERT INTO respuestas (id_respuesta, id_alumno, id_pregunta, respuesta) VALUES (:id_respuesta, :id_alumno, :id_pregunta, :respuesta)");
        foreach ($respuestas as $respuesta) {
            // Validar cada respuesta
            if (!isset($respuesta['id_pregunta'], $respuesta['respuesta'])) {
                error_log("Respuesta inválida: " . json_encode($respuesta));
                throw new Exception("Datos incompletos en respuesta");
            }
    
            $id_respuesta = $id_alumno . '-' . $respuesta['id_pregunta'];
    
            // Ejecutar la inserción
            $stmt->execute([
                'id_respuesta' => $id_respuesta,
                'id_alumno' => $id_alumno,
                'id_pregunta' => $respuesta['id_pregunta'],
                'respuesta' => $respuesta['respuesta'],
            ]);
        }
        
        
        // Calcular la calificación
        $calificacion = 0;
        $preguntas_totales = 0;

        // Obtener las respuestas correctas
        $stmt_correctas = $conn->prepare("SELECT id_pregunta, solucion FROM preguntas WHERE id_examen = :id_examen");
        $stmt_correctas->execute(['id_examen' => $id_examen]);
        $respuestas_correctas = $stmt_correctas->fetchAll(PDO::FETCH_ASSOC);

        // Crear un mapa de preguntas correctas para comparación
        $mapa_correctas = [];
        foreach ($respuestas_correctas as $pregunta) {
            $mapa_correctas[$pregunta['id_pregunta']] = $pregunta['solucion'];
        }

        // Comparar respuestas enviadas con correctas
        foreach ($respuestas as $respuesta) {
            $id_pregunta = $respuesta['id_pregunta'];
            if (isset($mapa_correctas[$id_pregunta]) && $mapa_correctas[$id_pregunta] === $respuesta['respuesta']) {
                $calificacion++;
            }
            $preguntas_totales++;
        }

        // Calcular la calificación final
        $calificacion_final = ($preguntas_totales > 0) ? ($calificacion / $preguntas_totales) * 100 : 0;

        // Actualizar la tabla de evaluación
        $stmt_update = $conn->prepare("UPDATE evaluacion SET calificacion = :calificacion WHERE id_alumno = :id_alumno AND id_examen = :id_examen");
        $stmt_update->execute([
            'calificacion' => $calificacion_final,
            'id_alumno' => $id_alumno,
            'id_examen' => $id_examen,
        ]);

        // Obtener la unidad del examen
        $stmt_unidad = $conn->prepare("SELECT unidad FROM evaluacion WHERE id_alumno = :id_alumno AND id_examen = :id_examen");
        $stmt_unidad->execute([
            'id_alumno' => $id_alumno,
            'id_examen' => $id_examen,
        ]);
        $unidad = $stmt_unidad->fetchColumn();

        if ($unidad) {
            // Construir el nombre de la columna de la unidad
            $unidad_columna = "unidad_" . $unidad;

            // Actualizar la calificación de la unidad en la tabla inscripciones
            $stmt_update_inscripciones = $conn->prepare("
                UPDATE inscripciones
                SET $unidad_columna = :calificacion
                WHERE id_alumno = :id_alumno AND id_curso = (
                    SELECT id_curso
                    FROM examenes
                    WHERE id_examen = :id_examen
                )
            ");
            $stmt_update_inscripciones->execute([
                'calificacion' => $calificacion_final,
                'id_alumno' => $id_alumno,
                'id_examen' => $id_examen,
            ]);

            // Recalcular la calificación final de inscripciones
            $stmt_recalcular = $conn->prepare("
                UPDATE inscripciones
                SET calificacion_final = ROUND(
                    (COALESCE(unidad_1, 0) + COALESCE(unidad_2, 0) + COALESCE(unidad_3, 0) + 
                    COALESCE(unidad_4, 0) + COALESCE(unidad_5, 0)) /
                    (CASE
                        WHEN (unidad_1 IS NOT NULL) + (unidad_2 IS NOT NULL) + 
                            (unidad_3 IS NOT NULL) + (unidad_4 IS NOT NULL) + 
                            (unidad_5 IS NOT NULL) > 0 
                        THEN 
                            (unidad_1 IS NOT NULL) + (unidad_2 IS NOT NULL) + 
                            (unidad_3 IS NOT NULL) + (unidad_4 IS NOT NULL) + 
                            (unidad_5 IS NOT NULL)
                        ELSE 1
                    END), 2)
                WHERE id_alumno = :id_alumno AND id_curso = (
                    SELECT id_curso
                    FROM examenes
                    WHERE id_examen = :id_examen
                )
            ");
            $stmt_recalcular->execute([
                'id_alumno' => $id_alumno,
                'id_examen' => $id_examen,
            ]);
        }

        echo json_encode(['success' => true, 'calificacion' => $calificacion_final]);
        
        
    } catch (PDOException $e) {
        error_log("Error de base de datos: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Error al insertar las respuestas en la base de datos']);
    } catch (Exception $e) {
        error_log("Error en datos: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}
?>


