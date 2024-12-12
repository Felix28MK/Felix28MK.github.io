<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $activity_id = $_POST['activity_id']; // ID de la actividad (ejemplo: 1 para Sopa de Letras, 2 para Preguntas, etc.)
    $score = $_POST['score'];
    $completion_date = date('Y-m-d H:i:s'); // Fecha de finalización

    try {
        $query = $conn->prepare("INSERT INTO results (user_id, activity_id, score, completion_date) VALUES (?, ?, ?, ?)");
        $query->execute([$user_id, $activity_id, $score, $completion_date]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
}
?>
