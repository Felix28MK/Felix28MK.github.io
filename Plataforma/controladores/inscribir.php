<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'alumno') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alumno_id = $_SESSION['user_id'];
    $cursos_seleccionados = $_POST['cursos'] ?? [];

    foreach ($cursos_seleccionados as $id_curso) {
        // Verificar si ya está inscrito
        $stmt = $conn->prepare("SELECT * FROM inscripciones WHERE id_alumno = :id_alumno AND id_curso = :id_curso");
        $stmt->execute(['id_alumno' => $alumno_id, 'id_curso' => $id_curso]);
        if ($stmt->rowCount() > 0) {
            continue; // Ya inscrito
        }

        // Obtener el nombre de la materia asociada
        $materia_stmt = $conn->prepare("
            SELECT m.nombre 
            FROM cursos c 
            JOIN materias m ON c.id_materia = m.id 
            WHERE c.id = :id_curso
        ");
        $materia_stmt->execute(['id_curso' => $id_curso]);
        $materia_nombre = $materia_stmt->fetchColumn();

        // Insertar inscripción
        $insert_stmt = $conn->prepare("
            INSERT INTO inscripciones (id_alumno, id_curso, materia) 
            VALUES (:id_alumno, :id_curso, :materia)
        ");
        $insert_stmt->execute([
            'id_alumno' => $alumno_id,
            'id_curso' => $id_curso,
            'materia' => $materia_nombre
        ]);
    }

    header("Location: horario_alumno.php");
    exit;
}
?>
