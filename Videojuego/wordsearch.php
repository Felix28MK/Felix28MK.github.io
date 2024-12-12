<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sopa de Letras</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('images/sopa.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
            overflow: hidden;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 800px;
            position: relative;
            z-index: 2;
        }

        .container h1 {
            margin-bottom: 20px;
            color: #3d85c6;
            font-size: 28px;
            font-weight: bold;
        }

        .timer {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #f44336;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(10, 40px);
            grid-gap: 5px;
            justify-content: center;
        }

        .cell {
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid #ccc;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .cell:hover {
            transform: scale(1.1);
            border-color: #3d85c6;
        }

        .selected {
            background-color: #ffeb3b;
            color: #000;
        }

        .completed {
            background-color: #4caf50;
            color: #fff;
        }

        .word-list {
            margin-top: 20px;
            font-size: 16px;
            color: #555;
        }

        .word-list h2 {
            color: #3d85c6;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .word-list ul {
            list-style: none;
            padding: 0;
        }

        .word-list li {
            margin-bottom: 5px;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="overlay"></div>

    <div class="container">
        <h1>Sopa de Letras</h1>
        <div class="timer" id="timer">Tiempo restante: 2:00</div>
        <div class="grid" id="grid">
            <!-- Letras generadas dinámicamente aquí -->
        </div>
        <div class="word-list">
            <h2>Palabras a Encontrar:</h2>
            <ul id="wordList">
                <!-- Palabras generadas dinámicamente aquí -->
            </ul>
        </div>
        <a href="index.php" class="back-button">Volver a la Página Principal</a>
    </div>

    <script>
        const words = ['JUEGO', 'CODIGO', 'HTML', 'CSS', 'JAVASCRIPT']; // Palabras a buscar
        const gridSize = 10; // Tamaño de la cuadrícula
        const grid = document.getElementById('grid');
        const wordList = document.getElementById('wordList');
        const timerElement = document.getElementById('timer');
        let selectedCells = [];
        let timeLeft = 120; // 2 minutos

        // Generar la cuadrícula
        function generateGrid() {
            const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const gridArray = Array.from({ length: gridSize }, () => Array(gridSize).fill(null));

            words.forEach(word => {
                let placed = false;
                while (!placed) {
                    const row = Math.floor(Math.random() * gridSize);
                    const col = Math.floor(Math.random() * gridSize);
                    const direction = Math.random() > 0.5 ? 'horizontal' : 'vertical';

                    if (direction === 'horizontal' && col + word.length <= gridSize) {
                        if (gridArray[row].slice(col, col + word.length).every(cell => cell === null)) {
                            word.split('').forEach((letter, i) => {
                                gridArray[row][col + i] = letter;
                            });
                            placed = true;
                        }
                    } else if (direction === 'vertical' && row + word.length <= gridSize) {
                        if (gridArray.slice(row, row + word.length).every(row => row[col] === null)) {
                            word.split('').forEach((letter, i) => {
                                gridArray[row + i][col] = letter;
                            });
                            placed = true;
                        }
                    }
                }
            });

            for (let row = 0; row < gridSize; row++) {
                for (let col = 0; col < gridSize; col++) {
                    if (gridArray[row][col] === null) {
                        gridArray[row][col] = letters.charAt(Math.floor(Math.random() * letters.length));
                    }
                }
            }

            return gridArray;
        }

        // Renderizar la cuadrícula en el DOM
        function renderGrid(gridArray) {
            grid.innerHTML = '';
            gridArray.forEach((row, rowIndex) => {
                row.forEach((letter, colIndex) => {
                    const cell = document.createElement('div');
                    cell.classList.add('cell');
                    cell.textContent = letter;
                    cell.dataset.row = rowIndex;
                    cell.dataset.col = colIndex;
                    cell.addEventListener('click', handleCellClick);
                    grid.appendChild(cell);
                });
            });
        }

        // Renderizar la lista de palabras
        function renderWordList() {
            wordList.innerHTML = words.map(word => `<li>${word}</li>`).join('');
        }

        // Manejar clics en las celdas
        function handleCellClick(event) {
            const cell = event.target;

            if (cell.classList.contains('selected')) {
                cell.classList.remove('selected');
                selectedCells = selectedCells.filter(c => c !== cell);
            } else {
                cell.classList.add('selected');
                selectedCells.push(cell);
            }

            checkWord();
        }

        // Verificar si las celdas seleccionadas forman una palabra
        function checkWord() {
            const selectedWord = selectedCells.map(cell => cell.textContent).join('');
            if (words.includes(selectedWord)) {
                selectedCells.forEach(cell => {
                    cell.classList.remove('selected');
                    cell.classList.add('completed');
                });
                selectedCells = [];
                words.splice(words.indexOf(selectedWord), 1);
                renderWordList();
            }
        }

        // Temporizador
        function startTimer() {
            const timerInterval = setInterval(() => {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `Tiempo restante: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    endGame();
                }
            }, 1000);
        }

        function endGame() {
            document.querySelectorAll('.cell').forEach(cell => cell.removeEventListener('click', handleCellClick));
            timerElement.textContent = '¡Tiempo agotado!';
        }

        const gridArray = generateGrid();
        renderGrid(gridArray);
        renderWordList();
        startTimer();
    </script>
</body>

</html>
