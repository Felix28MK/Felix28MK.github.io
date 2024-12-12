<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos enviados desde el formulario para la base de datos de la escuela
    $hostEscuela = $_POST['host_escuela'];
    $portEscuela = $_POST['port_escuela'];
    $userEscuela = $_POST['user_escuela'];
    $passwordEscuela = $_POST['password_escuela'];
    $dbnameEscuela = $_POST['dbname_escuela'];

    // Datos enviados desde el formulario para la base de datos del videojuego
    $hostVideojuego = $_POST['host_videojuego'];
    $portVideojuego = $_POST['port_videojuego'];
    $userVideojuego = $_POST['user_videojuego'];
    $passwordVideojuego = $_POST['password_videojuego'];
    $dbnameVideojuego = $_POST['dbname_videojuego'];

    // Datos para guardar en los archivos de configuración
    $configEscuela = [
        'host' => $hostEscuela,
        'port' => $portEscuela,
        'user' => $userEscuela,
        'password' => $passwordEscuela,
        'dbname' => $dbnameEscuela,
    ];

    $configVideojuego = [
        'host' => $hostVideojuego,
        'port' => $portVideojuego,
        'user' => $userVideojuego,
        'password' => $passwordVideojuego,
        'dbname' => $dbnameVideojuego,
    ];

    // Rutas donde se guardarán los archivos de configuración
    $configPathEscuela = __DIR__ . '/Plataforma/controladores/config.txt';
    $configPathVideojuego = __DIR__ . '/Videojuego/config.txt';

    // Guardar los archivos
    $successEscuela = file_put_contents($configPathEscuela, json_encode($configEscuela, JSON_PRETTY_PRINT));
    $successVideojuego = file_put_contents($configPathVideojuego, json_encode($configVideojuego, JSON_PRETTY_PRINT));

    // Preparar el mensaje para el modal
    if ($successEscuela && $successVideojuego) {
        $mensaje = "Archivos de configuración guardados correctamente:<br>1. Configuración de Escuela guardada en: $configPathEscuela<br>2. Configuración de Videojuego guardada en: $configPathVideojuego";
    } else {
        $mensaje = "Error al guardar los archivos de configuración. Verifique los permisos de escritura.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Base de Datos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding-top: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px 40px;
            width: 90%;
            max-width: 600px;
        }

        h1 {
            text-align: center;
            color: #555;
        }

        h2 {
            margin-top: 20px;
            color: #444;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 4px rgba(0, 123, 255, 0.25);
        }

        button {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            display: block;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        button:focus {
            outline: none;
            box-shadow: 0 0 4px rgba(0, 123, 255, 0.5);
        }

        a {
            color: #ffffff;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }

        .modal-content p {
            margin: 20px 0;
            font-size: 16px;
            color: #555;
        }

        .close-btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .close-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Configurar Base de Datos</h1>
        <form method="POST">
            <h2>Base de datos de la escuela</h2>
            <label for="host_escuela">Host:</label>
            <input type="text" id="host_escuela" name="host_escuela" required>

            <label for="port_escuela">Puerto:</label>
            <input type="number" id="port_escuela" name="port_escuela" value="3307" required>

            <label for="user_escuela">Usuario:</label>
            <input type="text" id="user_escuela" name="user_escuela" required>

            <label for="password_escuela">Contraseña:</label>
            <input type="password" id="password_escuela" name="password_escuela" required>

            <label for="dbname_escuela">Nombre de la Base de Datos:</label>
            <input type="text" id="dbname_escuela" name="dbname_escuela" required>

            <h2>Base de datos del videojuego</h2>
            <label for="host_videojuego">Host:</label>
            <input type="text" id="host_videojuego" name="host_videojuego" required>

            <label for="port_videojuego">Puerto:</label>
            <input type="number" id="port_videojuego" name="port_videojuego" value="3307" required>

            <label for="user_videojuego">Usuario:</label>
            <input type="text" id="user_videojuego" name="user_videojuego" required>

            <label for="password_videojuego">Contraseña:</label>
            <input type="password" id="password_videojuego" name="password_videojuego" required>

            <label for="dbname_videojuego">Nombre de la Base de Datos:</label>
            <input type="text" id="dbname_videojuego" name="dbname_videojuego" required>

            <button type="submit">Guardar Configuración</button>

            <table width="600">
                <tr>
                    <td>
                        <button>
                            <a href="index.php" class="button mysql">
                                <i class="fas fa-gamepad"></i>
                                Volver al Inicio
                            </a>
                        </button>
                    
                    </td>
                    <td>
                        <button>
                            <a href="mysql.php" class="button" download>
                                <i class="fas fa-database"></i> Descargar MySQL
                            </a>
                        </button>
                        
                    </td>
                </tr>
            </table>
            
        </form>
    </div>

    <!-- Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <p id="mensaje"><?php echo isset($mensaje) ? $mensaje : ''; ?></p>
            <button class="close-btn">Cerrar</button>
        </div>
    </div>

    <script>
        const modal = document.getElementById('successModal');
        const closeModalBtn = document.querySelector('.close-btn');

        // Mostrar modal si se ha enviado el formulario
        <?php if (isset($mensaje)) { ?>
            modal.style.display = 'block';
        <?php } ?>

        closeModalBtn.addEventListener('click', function () {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
