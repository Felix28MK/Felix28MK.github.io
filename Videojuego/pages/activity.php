<?php
include '../db.php';

$activityId = $_GET['id'] ?? null;

if ($activityId) {
    // Consulta para obtener preguntas
    $query = $conn->prepare("SELECT * FROM questions WHERE activity_id = ?");
    $query->execute([$activityId]);
    $questions = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("Actividad no encontrada.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividad</title>
</head>
<body>
    <h1>Actividad</h1>
    <?php foreach ($questions as $question): ?>
        <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
    <?php endforeach; ?>
</body>
</html>
