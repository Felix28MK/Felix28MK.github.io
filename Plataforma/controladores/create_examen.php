<?php
// Incluir la conexión
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Leer los datos JSON enviados
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Verificar si los datos necesarios están presentes
        if (isset($data['nombre_examen'], $data['id_curso'])) {
            $nombreExamen = $data['nombre_examen'];
            $idCurso = $data['id_curso'];

            // Generar el id_examen con el formato deseado
            $idExamen = "Exam-" . $nombreExamen . "(" . $idCurso . ")";

            // Insertar el examen
            $sqlInsertExamen = "INSERT INTO examenes (id_examen, nombre, id_curso) 
                                VALUES (:id_examen, :nombre, :id_curso)";
            $stmt = $conn->prepare($sqlInsertExamen);
            $stmt->bindParam(':id_examen', $idExamen);
            $stmt->bindParam(':nombre', $nombreExamen);
            $stmt->bindParam(':id_curso', $idCurso);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "id_examen" => $idExamen]);
            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo crear el examen."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Faltan parámetros necesarios"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>