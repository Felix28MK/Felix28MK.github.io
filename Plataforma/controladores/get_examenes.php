<?php
// Incluir la conexión
require_once 'conexion.php';

header('Content-Type: application/json'); // Asegura que el contenido sea JSON

try {
    if (isset($_GET['id_curso'])) {
        $idCurso = $_GET['id_curso'];

        // Consulta para obtener los exámenes del curso especificado
        $sql = "SELECT id_examen, nombre FROM examenes WHERE id_curso = :id_curso";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_curso', $idCurso, PDO::PARAM_STR);
        $stmt->execute();

        $examenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devolver los exámenes en formato JSON
        echo json_encode($examenes);
    } else {
        // Error si no se proporciona el parámetro id_curso
        echo json_encode(["status" => "error", "message" => "El parámetro 'id_curso' es obligatorio."]);
    }
} catch (PDOException $e) {
    // Manejo de errores
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>

