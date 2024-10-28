<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Survey</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM+OPvv+0DFeZxSTLsLnYy0Y6xzFgN6C/tn6iGQ" crossorigin="anonymous">
</head>
<body>
    <h1>Create Survey</h1>
    <form id="surveyForm" action="/survey/create" method="POST">
        @csrf
        <div id="questionsContainer"></div>
        <input type="text" name="user_id" value="{{ auth()->user()->id }}">
        <button type="button" onclick="addQuestion()">Add Question</button>
        <button type="submit">Create Survey</button>
    </form>

    <script>
        let questionCount = 0;

        function addQuestion() {
            questionCount++;
            const questionId = `question_${questionCount}`;

            // Create question container
            const questionContainer = document.createElement('div');
            questionContainer.id = questionId;
            questionContainer.innerHTML = `
                <h3>Question ${questionCount}</h3>
                <label>Content:</label>
                <input type="text" name="questions[${questionCount}][content]" required><br>

                <label>Is Final Question:</label>
                <input type="hidden" name="questions[${questionCount}][is_final]" value="0">
                <input type="checkbox" name="questions[${questionCount}][is_final]" value="1" onchange="updateNextQuestionId(${questionCount - 1}, ${questionCount})">

                <label>Type:</label>
                <select name="questions[${questionCount}][type]" required>
                    <option value="single">Single Answer</option>
                    <option value="multiple">Multiple Choice</option>
                </select><br>

                <div id="choicesContainer_${questionCount}"></div>
                <button type="button" onclick="addChoice(${questionCount})">Add Choice</button>
                <hr>
            `;

            document.getElementById('questionsContainer').appendChild(questionContainer);

            // Update next_question_id of the previous question if it's not final
            if (questionCount > 1) {
                const previousQuestionId = questionCount - 1;
                const previousQuestionIsFinal = document.querySelector(`input[name="questions[${previousQuestionId}][is_final]"]`).checked;
                if (!previousQuestionIsFinal) {
                    document.querySelector(`input[name="questions[${previousQuestionId}][next_question_id]"]`).value = questionCount; // Set next_question_id
                }
            }
        }

        function updateNextQuestionId(previousQuestionIndex, currentQuestionIndex) {
            // Update next_question_id for the previous question based on whether it's final
            const previousQuestionIsFinal = document.querySelector(`input[name="questions[${previousQuestionIndex}][is_final]"]`).checked;
            if (!previousQuestionIsFinal) {
                document.querySelector(`input[name="questions[${previousQuestionIndex}][next_question_id]"]`).value = currentQuestionIndex;
            }
        }

        function addChoice(questionIndex) {
            const choicesContainer = document.getElementById(`choicesContainer_${questionIndex}`);
            const choiceCount = choicesContainer.childElementCount + 1;
            const choiceContainer = document.createElement('div');
            choiceContainer.innerHTML = `
                <label>Choice ${choiceCount}:</label>
                <input type="text" name="questions[${questionIndex}][choices][${choiceCount}][content]" required>
                <label>Next Question ID (optional):</label>
                <input type="number" name="questions[${questionIndex}][choices][${choiceCount}][next_question_id]">
                <br>
            `;

            choicesContainer.appendChild(choiceContainer);
        }
    </script>
</body>
</html>
