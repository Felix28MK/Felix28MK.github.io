<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Preguntas</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('images/preguntas.jpg') no-repeat center center fixed;
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
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 600px;
            position: relative;
            z-index: 2;
        }

        .container h1 {
            margin-bottom: 10px;
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

        .question {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .option {
            padding: 15px 20px;
            background: linear-gradient(135deg, #6fa8dc, #3d85c6);
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .option:hover {
            background: linear-gradient(135deg, #3d85c6, #0b5394);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
    <!-- Capa de superposición -->
    <div class="overlay"></div>

    <div class="container">
        <h1>Juego de Preguntas</h1>
        <div class="timer" id="timer">Tiempo restante: 60s</div>
        <div class="question" id="question"></div>
        <div class="options" id="options"></div>
        <div class="result" id="result"></div>
        <a href="index.php" class="back-button">Volver a la Página Principal</a>
    </div>

    <script>
        const questions = [
            {
                question: "¿Cuál es la capital de Francia?",
                options: ["Londres", "París", "Roma", "Berlín"],
                correct: 1
            },
            {
                question: "¿Cuál es el resultado de 3 + 5?",
                options: ["5", "8", "10", "15"],
                correct: 1
            },
            {
                question: "¿Quién escribió 'Cien años de soledad'?",
                options: ["Gabriel García Márquez", "Pablo Neruda", "Octavio Paz", "Jorge Luis Borges"],
                correct: 0
            },
            {
                question: "¿Cuál es el planeta más grande del sistema solar?",
                options: ["Tierra", "Marte", "Júpiter", "Saturno"],
                correct: 2
            },
            {
                question: "¿Cuál es el idioma más hablado en el mundo?",
                options: ["Inglés", "Español", "Mandarín", "Hindi"],
                correct: 2
            }
        ];

        let currentQuestionIndex = 0;
        let timeLeft = 60;
        const timerElement = document.getElementById('timer');
        const questionElement = document.getElementById('question');
        const optionsElement = document.getElementById('options');
        const resultElement = document.getElementById('result');

        function loadQuestion() {
            const currentQuestion = questions[currentQuestionIndex];
            questionElement.textContent = currentQuestion.question;
            optionsElement.innerHTML = '';

            currentQuestion.options.forEach((option, index) => {
                const button = document.createElement('button');
                button.classList.add('option');
                button.textContent = option;
                button.onclick = () => checkAnswer(index);
                optionsElement.appendChild(button);
            });
        }

        function checkAnswer(selectedIndex) {
            const currentQuestion = questions[currentQuestionIndex];
            if (selectedIndex === currentQuestion.correct) {
                resultElement.textContent = '¡Respuesta Correcta!';
                resultElement.style.color = '#4caf50';
                currentQuestionIndex++;

                if (currentQuestionIndex < questions.length) {
                    setTimeout(() => {
                        resultElement.textContent = '';
                        loadQuestion();
                        resetTimer();
                    }, 1000);
                } else {
                    endGame(true);
                }
            } else {
                resultElement.textContent = 'Respuesta Incorrecta. Intenta nuevamente';
                resultElement.style.color = '#f44336';
            }
        }

        function startTimer() {
            const timerInterval = setInterval(() => {
                timeLeft--;
                timerElement.textContent = `Tiempo restante: ${timeLeft}s`;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    endGame(false);
                }
            }, 1000);
        }

        function resetTimer() {
            timeLeft = 60;
            timerElement.textContent = `Tiempo restante: ${timeLeft}s`;
        }

        function endGame(won) {
            questionElement.textContent = won ? '¡Felicidades, completaste todas las preguntas!' : '¡Se acabó el tiempo!';
            optionsElement.innerHTML = '';
            timerElement.style.display = 'none';
        }

        loadQuestion();
        startTimer();
    </script>
</body>

</html>
