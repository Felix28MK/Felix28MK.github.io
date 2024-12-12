<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-scale, initial-scale=1.0">
    <title>Juego del Cubo Disparador</title>
    <style>
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            margin: 0;
            overflow: hidden;
            background-color: black;
            position: relative;
        }

        #starCanvas,
        #gameCanvas {
            position: absolute;
            top: 0;
            left: 0;
        }

        #starCanvas {
            width: 100vw;
            /* Ancho completo de la ventana */
            height: 100vh;
            /* Alto completo de la ventana */
            display: block;
            z-index: 0;
            /* Detrás del canvas de juego */
        }

        #gameCanvas {
            width: 100vw;
            /* Ancho completo de la ventana */
            height: 100vh;
            /* Alto completo de la ventana */
            display: block;
            z-index: 1;
            /* Delante del fondo de estrellas */
        }

        #scoreBoard,
        #levelBoard,
        #timer,
        #startMessage,
        #topScores,
        #restartButton {
            position: absolute;
            color: white;
            text-shadow: 2px 2px 4px black;
            font-family: Arial, sans-serif;
            font-size: 40px;
        }


        #scoreBoard {
            top: 1.5vh;
            /* Relativo a la altura de la ventana */
            right: 2vw;
            /* Relativo al ancho de la ventana */
            position: absolute;
            font-size: 2vw;
            /* Escala de fuente basada en el ancho de la ventana */
        }

        #levelBoard {
            top: 1.5vh;
            left: 2vw;
            position: absolute;
            font-size: 3vw;
        }

        #timer {
            top: 90vh;
            /* Aproximadamente la posición de 700px en una pantalla estándar */
            right: 2vw;
            position: absolute;
            font-size: 3vw;
        }

        /* Mensaje para iniciar el juego centrado en la pantalla */
        #startMessage {
            top: 50%;
            left: 50%;
            transform: translate(-50%, 650%);
            /* Centrado tanto horizontal como verticalmente */
            font-size: 4vw;
            /* Tamaño de fuente basado en el ancho de la ventana */
            text-align: center;
            display: none;
            z-index: 2;
        }

        /* Top Scores centrado en la pantalla */
        #topScores {
            top: 7vh;
            /* Posición aproximada equivalente a 60px */
            left: 50%;
            transform: translateX(-50%);
            display: none;
            font-size: 2vw;
        }

        /* Botón de reinicio centrado horizontalmente y más bajo en la pantalla */
        #restartButton {
            position: absolute;
            top: 60vh;
            /* Aproximado a 550px en una pantalla estándar */
            left: 50%;
            transform: translateX(-50%);
            padding: 1vh 2vw;
            /* Escala padding basado en altura y ancho de la ventana */
            background-color: blue;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            font-size: 2vw;
            color: white;
            display: none;
            z-index: 2;
        }

        /* Contenedor de vidas */
        #livesDisplay {
            position: absolute;
            top: 90vh;
            /* Ajustado para que esté más bajo en la pantalla */
            left: 1vw;
            /* Relativo al ancho de la ventana */
            display: flex;
            gap: 1vw;
            /* Espacio entre los corazones */
            z-index: 2;
        }

        /* Iconos de corazón para las vidas */
        .heart {
            width: 3vw;
            /* Tamaño basado en el ancho de la ventana */
            height: auto;
            user-select: none;
        }
    </style>
</head>

<body>
    <canvas id="starCanvas"></canvas>
    <canvas id="gameCanvas"></canvas>
    <div id="scoreBoard">Puntuación: 0</div>
    <div id="levelBoard">Nivel: 1</div>
    <div id="timer">Tiempo: 00:00</div>
    <div id="startMessage">Haz clic para iniciar el juego</div>
    <div id="topScores"></div>
    <button id="restartButton">Reiniciar Juego</button>
    <div id="livesDisplay">Vidas: 3</div>

    <div id="livesDisplay">
        <img src="https://i.postimg.cc/sX6wJdSR/corazon.png" alt="Vida" class="heart" />
        <img src="https://i.postimg.cc/sX6wJdSR/corazon.png" alt="Vida" class="heart" />
        <img src="https://i.postimg.cc/sX6wJdSR/corazon.png" alt="Vida" class="heart" />
    </div>



    <script>
        // Configuración del fondo de estrellas
        const starCanvas = document.getElementById("starCanvas");
        const starCtx = starCanvas.getContext("2d");
        const gameCanvas = document.getElementById("gameCanvas");
        const ctx = gameCanvas.getContext("2d");
        let stars = [];

        // Ajusta el tamaño de ambos lienzos
        function resizeCanvas() {
            starCanvas.width = window.innerWidth;
            starCanvas.height = window.innerHeight;
            gameCanvas.width = window.innerWidth;
            gameCanvas.height = window.innerHeight;
        }
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        function createStars(count) {
            stars = [];
            for (let i = 0; i < count; i++) {
                stars.push({
                    x: Math.random() * starCanvas.width,
                    y: Math.random() * starCanvas.height,
                    radius: Math.random() * 1.5 + 0.5,
                    opacity: Math.random(),
                    fading: Math.random() > 0.5 ? 0.02 : -0.02
                });
            }
        }
        createStars(200);

        function drawStars() {
            starCtx.clearRect(0, 0, starCanvas.width, starCanvas.height);
            for (const star of stars) {
                starCtx.beginPath();
                starCtx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
                starCtx.fillStyle = `rgba(255, 255, 255, ${star.opacity})`;
                starCtx.fill();

                star.opacity += star.fading;
                if (star.opacity <= 0 || star.opacity >= 1) {
                    star.fading = -star.fading;
                }
            }
            requestAnimationFrame(drawStars);
        }
        drawStars();

        // Variables y funciones del juego
        const scoreBoard = document.getElementById("scoreBoard");
        const levelBoard = document.getElementById("levelBoard");
        const startMessage = document.getElementById("startMessage");
        const timerDisplay = document.getElementById("timer");
        const topScoresDisplay = document.getElementById("topScores");
        const restartButton = document.getElementById("restartButton");

        const cubeImage = new Image();
        cubeImage.src = 'https://i.postimg.cc/c1pTywZY/pngwing-com.png';

        const blockImage = new Image();
        blockImage.src = 'https://i.postimg.cc/Gh7sk12Q/pngegg.png';

        const catSound = new Audio('musi.mp3');

        let score = 0;
        let level = 1;
        let cube = { x: 370, y: 550, width: 60, height: 60, speed: 5, direction: 1 };
        let enemyBullets = []; // Nueva variable para almacenar balas enemigas
        let bullets = [];
        let blocks = [];
        let lives = 3; // Número inicial de vidas
        let shootingIntervalId; // ID del intervalo para actualizar o limpiar
        let monster = { x: gameCanvas.width / 2 - 100, y: 25, width: 200, height: 150, life: 5000, direction: 1, speed: 7.5 };
        let gameStarted = false;
        let gamePaused = false;
        let seconds = 0;
        let timerInterval;
        let topScores = [];
        let keysPressed = {}; // Guardar el estado de las teclas
        let heart = null; // Objeto para el corazón
        let heartSpawned = false; // Estado del corazón
        let heartFallInterval; // Intervalo para controlar la caída del corazón
        let timeElapsed = 0; // Tiempo transcurrido en segundos



        // Función para manejar el movimiento de la nave
        function handleMovement() {
            if (keysPressed['ArrowLeft'] || keysPressed['a']) {
                cube.x -= cube.speed;
            }
            if (keysPressed['ArrowRight'] || keysPressed['d']) {
                cube.x += cube.speed;
            }
            // Mantener la nave dentro de los límites
            cube.x = Math.max(0, Math.min(gameCanvas.width - cube.width, cube.x));
        }

        // Eventos de teclado para detectar pulsaciones
        document.addEventListener('keydown', (event) => {
            keysPressed[event.key] = true;
        });

        document.addEventListener('keyup', (event) => {
            keysPressed[event.key] = false;
        });

        function createBlocks() {
            blocks = [];
            if (level === 1) {
                let rows = 6 * level;
                let cols = 12 * level;
                let blockWidth = 80;
                let blockHeight = 30;
                let totalWidth = cols * blockWidth + (cols - 1) * 10;
                let startX = (gameCanvas.width - totalWidth) / 2;

                for (let i = 0; i < rows; i++) {
                    for (let j = 0; j < cols; j++) {
                        blocks.push({
                            x: startX + j * (blockWidth + 10),
                            y: 50 * i + 10,
                            width: blockWidth,
                            height: blockHeight
                        });
                    }
                }
            } else if (level === 2) {
                monster.life = 5000;
                blocks.push(monster);
            }
        }

        createBlocks();

        function startEnemyShooting() {
            // Función para disparar desde un bloque aleatorio en columnas activas
            function shootFromRandomBlock() {
                if (blocks.length > 0 && gameStarted && !gamePaused) {
                    let columns = Array.from({ length: 12 }, (_, i) => i);
                    let activeColumns = columns.filter(col => blocks.some((_, index) => index % 12 === col));

                    if (activeColumns.length > 0) {
                        let randomColumn = activeColumns[Math.floor(Math.random() * activeColumns.length)];
                        let columnBlocks = blocks.filter((block, index) => index % 12 === randomColumn);

                        if (columnBlocks.length > 0) {
                            let shootingBlock = columnBlocks[columnBlocks.length - 1];
                            enemyBullets.push({
                                x: shootingBlock.x + shootingBlock.width / 2 - 2.5,
                                y: shootingBlock.y + shootingBlock.height,
                                width: 5,
                                height: 10,
                                speed: 5
                            });
                        }
                    }
                }
            }

            // Función para ajustar el intervalo de disparo según el número de bloques
            function adjustShootingInterval() {
                clearInterval(shootingIntervalId); // Limpiar cualquier intervalo previo

                // Calcular el intervalo dinámico basado en el número de bloques restantes
                let blockCount = blocks.length;
                let activeColumnsCount = Array.from({ length: 12 }, (_, i) => i)
                    .filter(col => blocks.some((_, index) => index % 12 === col)).length;

                // Determinar el intervalo base (más rápido con más bloques)
                let shootingInterval = Math.max(1000, 3000 / Math.max(1, activeColumnsCount));

                // Si quedan pocos bloques (por ejemplo, menos de 10), duplicar el intervalo para disparos más lentos
                if (blockCount < 10) {
                    shootingInterval *= 2; // Doblar el intervalo si quedan pocos bloques
                }

                // Iniciar el primer disparo de inmediato
                shootFromRandomBlock();

                // Configurar el intervalo de disparo ajustado
                shootingIntervalId = setInterval(() => {
                    shootFromRandomBlock();
                }, shootingInterval);
            }

            // Inicia el disparo y ajusta el intervalo
            adjustShootingInterval();

            // Observa el cambio en el número de bloques y ajusta el intervalo dinámicamente
            setInterval(() => {
                if (blocks.length > 0) {
                    adjustShootingInterval(); // Ajusta el intervalo cada vez que cambia la cantidad de bloques
                }
            }, 500);
        }

        function startHeartTimer() {
            setInterval(() => {
                timeElapsed++;

                // Después de 15 segundos, generar un corazón en intervalos aleatorios
                if (timeElapsed >= 15 && !heartSpawned) {
                    spawnHeart(); // Generar el corazón
                    heartSpawned = true; // Asegurar que solo se genere una vez al inicio
                }
            }, 1000); // Incrementar cada segundo
        }


        function spawnHeart() {
            // Generar un intervalo aleatorio entre 10 y 40 segundos para el próximo corazón
            const spawnInterval = Math.floor(Math.random() * (40000 - 10000 + 1)) + 10000;

            // Crear el corazón en una posición aleatoria en la parte superior del canvas
            heart = {
                x: Math.random() * (gameCanvas.width - 40), // Posición horizontal aleatoria
                y: 0, // Comienza en la parte superior de la pantalla
                width: 40,
                height: 40,
                speed: 2.5 // Velocidad de caída
            };

            // Controlar la caída del corazón
            heartFallInterval = setInterval(() => {
                if (heart) {
                    heart.y += heart.speed;

                    // Si el corazón llega al fondo de la pantalla, lo elimina y establece un nuevo intervalo
                    if (heart.y > gameCanvas.height) {
                        clearInterval(heartFallInterval);
                        heart = null; // Eliminar el corazón
                        setTimeout(spawnHeart, spawnInterval); // Configurar el siguiente corazón en un tiempo aleatorio
                    }
                }
            }, 30);
        }




        document.addEventListener("click", function () {
            if (!gameStarted) {
                startGame();
                startEnemyShooting();
                startHeartTimer();
            }
            if (!gamePaused) {
                bullets.push({ x: cube.x + cube.width / 2 - 2.5, y: cube.y, width: 5, height: 10, speed: 7 });
            }
        });

        restartButton.addEventListener("click", function () {
            resetGame();
        });

        function startGame() {
            gameStarted = true;
            startMessage.style.display = "none";
            seconds = 0;
            timerDisplay.textContent = "Tiempo: 00:00";
            topScoresDisplay.style.display = "none";
            restartButton.style.display = "none";
            timerInterval = setInterval(() => {
                seconds++;
                let minutes = Math.floor(seconds / 60);
                let secs = seconds % 60;
                timerDisplay.textContent = "Tiempo: " +
                    (minutes < 10 ? "0" + minutes : minutes) + ":" +
                    (secs < 10 ? "0" + secs : secs);
            }, 1000);
        }

        function resetGame() {
            score = 0;
            level = 1;
            lives = 3; // Reiniciar vidas

            // Restaurar todos los corazones visibles
            const hearts = document.querySelectorAll("#livesDisplay .heart");
            hearts.forEach(heart => heart.style.visibility = "visible");

            cube = { x: 370, y: 550, width: 60, height: 60, speed: 5, direction: 1 };
            bullets = [];
            enemyBullets = []; // Limpiar balas enemigas
            createBlocks();
            gameStarted = false;
            gamePaused = false;
            seconds = 0;
            timerDisplay.textContent = "Tiempo: 00:00";
            scoreBoard.textContent = "Puntuación: " + score;
            levelBoard.textContent = "Nivel: " + level;
            topScoresDisplay.style.display = "none";
            restartButton.style.display = "none"; // Ocultar botón de reinicio al reiniciar
            clearInterval(timerInterval);
            ctx.clearRect(0, 0, gameCanvas.width, gameCanvas.height);
            startMessage.style.display = "block"; // Mostrar mensaje de inicio al reiniciar
        }




        function update() {
            if (!gameStarted || gamePaused) return;

            handleMovement(); // Llama a la función de movimiento
            updateEnemyBullets();

            // Colisión con el corazón
            if (heart &&
                heart.x < cube.x + cube.width &&
                heart.x + heart.width > cube.x &&
                heart.y < cube.y + cube.height &&
                heart.y + heart.height > cube.y
            ) {
                // Incrementar vidas si es menor a 3 y actualizar visualmente
                if (lives < 3) {
                    lives++;
                    const hearts = document.querySelectorAll("#livesDisplay .heart");
                    hearts[lives - 1].style.visibility = "visible"; // Mostrar un corazón
                }

                // Eliminar el corazón y detener el intervalo
                clearInterval(heartFallInterval);
                heart = null;
                heartSpawned = true; // Permitir que otro corazón caiga más tarde
            }

            for (let i = bullets.length - 1; i >= 0; i--) {
                bullets[i].y -= bullets[i].speed;
                if (bullets[i].y < 0) {
                    bullets.splice(i, 1);
                }
            }

            if (level === 2) {
                monster.x += monster.speed * monster.direction;
                if (monster.x <= 0 || monster.x >= gameCanvas.width - monster.width) {
                    monster.direction *= -1;
                }
            }

            for (let i = bullets.length - 1; i >= 0; i--) {
                for (let j = blocks.length - 1; j >= 0; j--) {
                    if (
                        bullets[i].x < blocks[j].x + blocks[j].width &&
                        bullets[i].x + bullets[i].width > blocks[j].x &&
                        bullets[i].y < blocks[j].y + blocks[j].height &&
                        bullets[i].y + bullets[i].height > blocks[j].y
                    ) {
                        bullets.splice(i, 1);
                        if (level === 2) {
                            blocks[j].life -= 100;
                        }
                        score += 100;
                        scoreBoard.textContent = "Puntuación: " + score;
                        catSound.play();

                        if (level === 2 && blocks[j].life <= 0) {
                            blocks.splice(j, 1);
                            clearInterval(timerInterval);
                            gamePaused = true;
                            topScores.push(seconds);
                            topScores.sort((a, b) => a - b);
                            if (topScores.length > 5) topScores.pop();
                            displayTopScores();
                            restartButton.style.display = "block";
                        } else if (level === 1) {
                            blocks.splice(j, 1);
                        }
                        break;
                    }
                }
            }

            if (blocks.length === 0) {
                if (level === 1) {
                    level += 1;
                    scoreBoard.textContent = "Puntuación: " + score;
                    levelBoard.textContent = "Nivel: " + level;
                    createBlocks();
                }
            }


            function updateEnemyBullets() {
                for (let i = enemyBullets.length - 1; i >= 0; i--) {
                    enemyBullets[i].y += enemyBullets[i].speed;
                    if (enemyBullets[i].y > gameCanvas.height) {
                        enemyBullets.splice(i, 1);
                        continue;
                    }

                    // Detectar colisión con la nave
                    if (
                        enemyBullets[i].x < cube.x + cube.width &&
                        enemyBullets[i].x + enemyBullets[i].width > cube.x &&
                        enemyBullets[i].y < cube.y + cube.height &&
                        enemyBullets[i].y + enemyBullets[i].height > cube.y
                    ) {
                        enemyBullets.splice(i, 1); // Eliminar la bala tras colisión
                        lives--; // Reducir una vida

                        // Actualizar visualmente el número de corazones
                        const hearts = document.querySelectorAll("#livesDisplay .heart");
                        if (hearts[lives]) {
                            hearts[lives].style.visibility = "hidden"; // Ocultar un corazón
                        }

                        // Verificar si la nave ha perdido todas las vidas
                        if (lives <= 0) {
                            showRestartButton(); // Mostrar botón de reinicio si no quedan vidas
                            gamePaused = true; // Pausar el juego tras el impacto fatal
                        }
                    }
                }
            }



            function showRestartButton() {
                startMessage.style.display = "none"; // Ocultar el mensaje de inicio
                restartButton.style.display = "block"; // Mostrar el botón de reinicio
            }



        }

        function displayTopScores() {
            topScoresDisplay.style.display = "block";
            topScoresDisplay.innerHTML = "<h2>Top 5 Tiempos</h2>";
            topScores.forEach((time, index) => {
                let minutes = Math.floor(time / 60);
                let secs = time % 60;
                topScoresDisplay.innerHTML += `<div>${index + 1}. ${minutes < 10 ? '0' + minutes : minutes}:${secs < 10 ? '0' + secs : secs}</div>`;
            });
        }

        function draw() {
            ctx.clearRect(0, 0, gameCanvas.width, gameCanvas.height);
            ctx.drawImage(cubeImage, cube.x, cube.y, cube.width, cube.height);

            // Dibuja las balas del jugador
            ctx.fillStyle = "WHITE"; // Color de las balas del jugador
            bullets.forEach(bullet => {
                ctx.fillRect(bullet.x, bullet.y, bullet.width, bullet.height);
            });

            // Dibuja bloques enemigos y sus balas
            blocks.forEach(block => {
                ctx.drawImage(blockImage, block.x, block.y, block.width, block.height);
                if (level === 2 && block.life > 0) {
                    const healthBarWidth = (block.life / 5000) * block.width;
                    ctx.fillStyle = "red";
                    ctx.fillRect(block.x, block.y - 10, healthBarWidth, 5);
                }
            });

            // Dibuja balas enemigas
            ctx.fillStyle = "red"; // Color de las balas enemigas
            enemyBullets.forEach(bullet => {
                ctx.fillRect(bullet.x, bullet.y, bullet.width, bullet.height);
            });

            // Dibuja el corazón si está activo
            if (heart) {
                const heartImage = new Image();
                heartImage.src = "https://i.postimg.cc/sX6wJdSR/corazon.png"; // URL del ícono de corazón
                ctx.drawImage(heartImage, heart.x, heart.y, heart.width, heart.height);
            }

            if (!gameStarted) {
                startMessage.style.display = "block";
            } else {
                startMessage.style.display = "none";
            }

            if (gamePaused && blocks.length === 0) {
                ctx.fillStyle = "white";
                ctx.font = "50px Arial";
                ctx.textAlign = "center";
                ctx.fillText("Juego terminado", gameCanvas.width / 2, gameCanvas.height / 2);
            }
        }

        function gameLoop() {
            update();
            draw();
            requestAnimationFrame(gameLoop);
        }

        gameLoop();
    </script>
</body>

</html>