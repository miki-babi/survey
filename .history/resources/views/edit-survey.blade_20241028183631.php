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
    <form id="surveyForm" action="{{ route('survey.update', ['id' => $questions->first()->id] }}" method="POST">
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
                                <input type="text" name="questions[{{ $loop->parent->index }}][choices][{{ $loop->index }}][content]" value="{{ $choice->content }}" required>
                                <input type="hidden" name="questions[{{ $loop->parent->index }}][choices][{{ $loop->index }}][id]" value="{{ $choice->id }}">
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
        function addChoice(questionId) {
            const choicesContainer = document.getElementById(`choicesContainer_${questionId}`);
            const choiceCount = choicesContainer.childElementCount + 1;
            const choiceContainer = document.createElement('div');
            choiceContainer.innerHTML = `
                <label>Choice:</label>
                <input type="text" name="questions[${questionId}][choices][${choiceCount}][content]" required>
            `;
            choicesContainer.appendChild(choiceContainer);
        }
    </script>
</body>
</html>
