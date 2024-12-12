<?php
require_once 'conexion.php';

if (isset($_GET['id_materia'])) {
    $idMateria = $_GET['id_materia'];

    // Consulta para obtener los cursos relacionados con la materia
    $sqlCursos = "SELECT * FROM cursos WHERE id_materia = :id_materia";
    $stmtCursos = $conn->prepare($sqlCursos);
    $stmtCursos->bindParam(':id_materia', $idMateria, PDO::PARAM_STR);
    $stmtCursos->execute();

    // Obtener los resultados y devolverlos como JSON
    $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cursos);  // Convertir el array de cursos a formato JSON
}
?>