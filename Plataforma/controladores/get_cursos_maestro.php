<?php
require_once 'conexion.php';

if (isset($_GET['id_materia']) && isset($_GET['id_maestro'])) {
    $idMateria = $_GET['id_materia'];
    $idMaestro = $_GET['id_maestro'];

    // Consulta para obtener los cursos relacionados con la materia y el maestro
    $sqlCursos = "SELECT * FROM cursos WHERE id_materia = :id_materia AND id_maestro = :id_maestro";
    $stmtCursos = $conn->prepare($sqlCursos);
    $stmtCursos->bindParam(':id_materia', $idMateria, PDO::PARAM_STR);
    $stmtCursos->bindParam(':id_maestro', $idMaestro, PDO::PARAM_STR);
    $stmtCursos->execute();

    // Obtener los resultados y devolverlos como JSON
    $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cursos);  // Convertir el array de cursos a formato JSON
}
?>