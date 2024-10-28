<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Survey</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <h1>Edit Survey</h1>
    <form id="surveyForm" action="{{ route('survey.update', ['id' => $questions->first()->id]) }}" method="POST">
        @csrf
        <div id="questionsContainer">
            @foreach ($questions as $question)
                <div id="question_{{ $question->id }}">
                    <h3>Question</h3>
                    <label>Content:</label>
                    <input type="text" name="questions[{{ $loop->index }}][content]" value="{{ $question->content }}" required>
                    <input type="hidden" name="questions[{{ $loop->index }}][id]" value="{{ $question->id }}">

                    <label>Is Final Question:</label>
                    <input type="checkbox" name="questions[{{ $loop->index }}][is_final]" value="1" {{ $question->is_final ? 'checked' : '' }}>

                    <label>Type:</label>
                    <select name="questions[{{ $loop->index }}][type]" required>
                        <option value="single" {{ $question->type == 'single' ? 'selected' : '' }}>Single Answer</option>
                        <option value="multiple" {{ $question->type == 'multiple' ? 'selected' : '' }}>Multiple Choice</option>
                    </select>

                    <div id="choicesContainer_{{ $question->id }}">
                        @foreach ($question->choices as $choice)
                            <div>
                                <label>Choice:</label>
                                <input type="text" name="questions[{{ $loop->index }}][choices][{{ $loop->index }}][content]" value="{{ $choice->content }}" required>
                                <input type="hidden" name="questions[{{ $loop->index }}][choices][{{ $loop->index }}][id]" value="{{ $choice->id }}">

                                <label>Next Question:</label>
                                <select name="questions[{{ $loop->index }}][choices][{{ $loop->index }}][next_question_id]">
                                    <option value="">Select Next Question (Optional)</option>
                                    @foreach ($existingQuestions as $existingQuestion)
                                        <option value="{{ $existingQuestion->id }}" {{ $choice->next_question_id == $existingQuestion->id ? 'selected' : '' }}>
                                            {{ $existingQuestion->content }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addChoice({{ $question->id }})">Add Choice</button>
                </div>
            @endforeach
        </div>
        <button type="submit">Update Survey</button>
    </form>

    <script>
        function addQuestion() {
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

        <div id="choicesContainer_${questionCount}"></div>
        <button type="button" onclick="addChoice(${questionCount})">Add Choice</button>
        <hr>
    `;

    document.getElementById('questionsContainer').appendChild(questionContainer);
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
