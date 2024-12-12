<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asocia Palabras</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('images/palabras.jpg') no-repeat center center fixed;
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
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            width: 90%;
            max-width: 600px;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .container h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #3d85c6;
        }

        .timer {
            font-size: 20px;
            font-weight: bold;
            color: #f44336;
            margin-bottom: 20px;
        }

        .word-pairs {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .word {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            padding: 15px 20px;
            background: linear-gradient(135deg, #6fa8dc, #3d85c6);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
        }

        .word:hover {
            background: linear-gradient(135deg, #3d85c6, #0b5394);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .word.correct {
            background: #4caf50;
        }

        .word.incorrect {
            background: #f44336;
        }

        .result {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #4caf50;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-weight: bold;
            color: white;
            text-align: center;
            background: #007BFF;
            border-radius: 8px;
            text-decoration: none;
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
        <h1>Asocia Palabras</h1>
        <div id="timer" class="timer">Tiempo restante: 60s</div>
        <div id="game-board" class="word-pairs"></div>
        <div id="result" class="result"></div>
        <a href="index.php" class="back-button">Volver a la Página Principal</a>
    </div>

    <script>
        let levels = [
            [
                { word: "Cat", pair: 1 },
                { word: "Gato", pair: 1 },
                { word: "Dog", pair: 2 },
                { word: "Perro", pair: 2 },
                { word: "Sun", pair: 3 },
                { word: "Sol", pair: 3 },
                { word: "Moon", pair: 4 },
                { word: "Luna", pair: 4 }
            ],
            [
                { word: "House", pair: 1 },
                { word: "Casa", pair: 1 },
                { word: "Tree", pair: 2 },
                { word: "Árbol", pair: 2 },
                { word: "Car", pair: 3 },
                { word: "Coche", pair: 3 },
                { word: "Sky", pair: 4 },
                { word: "Cielo", pair: 4 }
            ],
            [
                { word: "Water", pair: 1 },
                { word: "Agua", pair: 1 },
                { word: "Fire", pair: 2 },
                { word: "Fuego", pair: 2 },
                { word: "Earth", pair: 3 },
                { word: "Tierra", pair: 3 },
                { word: "Wind", pair: 4 },
                { word: "Viento", pair: 4 }
            ]
        ];

        let currentLevel = 0;
        let timeLeft = 60;
        const timerElement = document.getElementById('timer');
        const gameBoard = document.getElementById('game-board');
        let selectedWords = [];

        function startLevel(levelIndex) {
            gameBoard.innerHTML = '';
            levels[levelIndex].forEach(item => {
                const button = document.createElement('button');
                button.textContent = item.word;
                button.className = 'word';
                button.dataset.pair = item.pair;
                button.onclick = () => selectWord(button);
                gameBoard.appendChild(button);
            });
        }

        const timerInterval = setInterval(() => {
            timeLeft--;
            timerElement.textContent = `Tiempo restante: ${timeLeft}s`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                endGame();
            }
        }, 1000);

        function selectWord(button) {
            if (selectedWords.length < 2 && !button.classList.contains('correct')) {
                button.classList.add('selected');
                selectedWords.push(button);

                if (selectedWords.length === 2) {
                    checkMatch();
                }
            }
        }

        function checkMatch() {
            const [firstWord, secondWord] = selectedWords;
            if (firstWord.dataset.pair === secondWord.dataset.pair) {
                firstWord.classList.add('correct');
                secondWord.classList.add('correct');
                document.getElementById('result').textContent = '¡Correcto!';

                if (document.querySelectorAll('.word:not(.correct)').length === 0) {
                    currentLevel++;
                    if (currentLevel < levels.length) {
                        setTimeout(() => {
                            document.getElementById('result').textContent = '';
                            startLevel(currentLevel);
                        }, 1000);
                    } else {
                        document.getElementById('result').textContent = '¡Completaste todos los niveles!';
                        clearInterval(timerInterval);
                    }
                }
            } else {
                firstWord.classList.add('incorrect');
                secondWord.classList.add('incorrect');
                document.getElementById('result').textContent = 'Incorrecto. Intenta de nuevo.';
            }
            setTimeout(() => {
                firstWord.classList.remove('selected', 'incorrect');
                secondWord.classList.remove('selected', 'incorrect');
                selectedWords = [];
                document.getElementById('result').textContent = '';
            }, 1000);
        }

        function endGame() {
            document.getElementById('result').textContent = '¡Se acabó el tiempo!';
            document.querySelectorAll('.word').forEach(button => {
                button.disabled = true;
            });
        }

        startLevel(currentLevel);
    </script>
</body>

</html>
