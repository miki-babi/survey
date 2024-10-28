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
        function addChoice(questionIndex) {
    const choicesContainer = document.getElementById(`choicesContainer_${questionIndex}`);
    const choiceCount = choicesContainer.childElementCount + 1;

    const questionId = document.querySelector(`input[name="questions[${questionIndex}][id]"]`).value; // Get question ID
    const questionContent = document.querySelector(`input[name="questions[${questionIndex}][content]"]`).value; // Get question content
    const questionIsFinal = document.querySelector(`input[name="questions[${questionIndex}][is_final]"]`).checked; // Get final status
    const questionType = document.querySelector(`select[name="questions[${questionIndex}][type]"]`).value; // Get question type

    const choiceContainer = document.createElement('div');
    choiceContainer.innerHTML = `
        <input type="hidden" name="questions[${questionIndex}][id]" value="${questionId}"> <!-- Hidden input for question ID -->
        <input type="hidden" name="questions[${questionIndex}][content]" value="${questionContent}"> <!-- Hidden input for question content -->
        <input type="hidden" name="questions[${questionIndex}][is_final]" value="${questionIsFinal}"> <!-- Hidden input for question is_final -->
        <input type="hidden" name="questions[${questionIndex}][type]" value="${questionType}"> <!-- Hidden input for question type -->

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
