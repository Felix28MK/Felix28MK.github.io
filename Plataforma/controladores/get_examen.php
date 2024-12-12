<?php
// Incluir la conexión
require_once 'conexion.php';

if (isset($_GET['id_curso'])) {
    $idCurso = $_GET['id_curso'];

    // Consulta para obtener los examenes relacionados con el curso
    $sqlExamenes = "SELECT * FROM examenes WHERE id_curso = :id_curso";
    $stmtExamenes = $conn->prepare($sqlExamenes);
    $stmtExamenes->bindParam(':id_curso', $idCurso, PDO::PARAM_STR);
    $stmtExamenes->execute();

    // Obtener los resultados y devolverlos como JSON
    $examenes = $stmtExamenes->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($examenes);  // Convertir el array de examens a formato JSON
}
?>
