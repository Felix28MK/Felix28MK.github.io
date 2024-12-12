<?php
$configFile = __DIR__ . '/config.txt';

if (!file_exists($configFile)) {
    die("Error: Archivo de configuración no encontrado. Por favor, configure la aplicación.");
}

$config = json_decode(file_get_contents($configFile), true);

$host = $config['host'];
$port = $config['port'];
$user = $config['user'];
$password = $config['password'];
$dbname = $config['dbname'];

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
