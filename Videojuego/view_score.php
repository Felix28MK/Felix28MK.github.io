<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $query = $conn->prepare("
        SELECT activity_id, score, completion_date 
        FROM results 
        WHERE user_id = ? 
        ORDER BY completion_date DESC
    ");
    $query->execute([$user_id]);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Resultados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #4caf50;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Mis Resultados</h1>
    <table>
        <tr>
            <th>Actividad</th>
            <th>Puntaje</th>
            <th>Fecha</th>
        </tr>
        <?php foreach ($results as $result): ?>
            <tr>
                <td>
                    <?php 
                        // Muestra nombres de actividades basados en `activity_id`
                        switch ($result['activity_id']) {
                            case 1: echo 'Sopa de Letras'; break;
                            case 2: echo 'Juego de Preguntas'; break;
                            case 3: echo 'Asocia Palabras'; break;
                            case 4: echo 'Cubo Disparador'; break;
                            default: echo 'Actividad Desconocida';
                        }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($result['score']); ?></td>
                <td><?php echo htmlspecialchars($result['completion_date']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
