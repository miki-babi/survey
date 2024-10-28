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
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

        <button type="button" id="addQuestionBtn">Add Question</button>
        <button type="submit">Create Survey</button>
    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    let questionCount = 0;

function addQuestion(existingQuestions) {
    questionCount++;
    const questionId = `question_${questionCount}`;

    const questionContainer = document.createElement('div');
    questionContainer.id = questionId;
    questionContainer.innerHTML = `
        <h3>Question ${questionCount}</h3>
        <label>Content:</label>
        <input type="text" name="questions[${questionCount}][content]" required><br>

        <label>Is Final Question:</label>
        <input type="hidden" name="questions[${questionCount}][is_final]" value="0">
        <input type="checkbox" name="questions[${questionCount}][is_final]" value="1">

        <label>Type:</label>
        <select name="questions[${questionCount}][type]" required>
            <option value="single">Single Answer</option>
            <option value="multiple">Multiple Choice</option>
        </select><br>

        <label>Next Question:</label>
        <select name="questions[${questionCount}][choices][0][next_question_id]">
            <option value="">Select Next Question</option>
        </select><br>

        <div id="choicesContainer_${questionCount}"></div>
        <button type="button" onclick="addChoice(${questionCount})">Add Choice</button>
        <hr>
    `;

    // Populate the next question dropdown
    const nextQuestionSelect = questionContainer.querySelector(`select[name="questions[${questionCount}][choices][0][next_question_id]"]`);
    existingQuestions.forEach(question => {
        const option = document.createElement('option');
        option.value = question.id;
        option.textContent = question.content; // Set the option text to the question content
        nextQuestionSelect.appendChild(option);
    });

    document.getElementById('questionsContainer').appendChild(questionContainer);

    // Submit the current question to the server
    submitCurrentQuestion(questionCount);
}

function submitCurrentQuestion(questionIndex) {
    const formData = new FormData(document.getElementById('surveyForm'));

    $.ajax({
        url: '/survey/create', // Adjust to your route for creating a survey
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log("Question submitted successfully:", response);
            // Handle success (e.g., notify user, etc.)
        },
        error: function(jqXHR) {
            console.error("Error submitting question:", jqXHR.responseJSON.errors);
            // Handle errors (e.g., display error messages)
        }
    });
}

function fetchQuestionIds() {
    $.ajax({
        url: "{{ route('survey.question.ids') }}", // Update with the correct route
        method: "GET",
        success: function(existingQuestions) {
            addQuestion(existingQuestions); // Pass existing questions to addQuestion
        }
    });
}

// Fetch questions when the page loads or as needed
document.addEventListener('DOMContentLoaded', fetchQuestionIds);
</script>
</body>
</html>
