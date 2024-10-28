<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Form</title>
</head>
<body>
    <form id="answerForm" action="{{ route('survey.answer') }}" method="POST">
        @csrf

        <div id="surveyContainer">
            @foreach ($questions as $question)
                <div class="question">
                    <h3>{{ $question->content }}</h3>
                    <div class="choices">
                        @foreach ($question->choices as $choice)
                            <div>
                                <input type="checkbox"
                                       name="answers[{{ $question->id }}][choice_id][]"
                                       value="{{ $choice->id }}"
                                       id="choice_{{ $choice->id }}"
                                       class="choice-checkbox {{ $question->type === 'single' ? 'single-choice' : '' }}"
                                       data-question-id="{{ $question->id }}">
                                <input type="hidden" name="answers[{{ $question->id }}][question_id]" value="{{ $question->id }}">
                                <label for="choice_{{ $choice->id }}">{{ $choice->content }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        <button type="submit">Submit Answers</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener to all checkboxes with 'single-choice' class
            const singleChoiceCheckboxes = document.querySelectorAll('.single-choice');

            singleChoiceCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const questionId = this.dataset.questionId;
                    // Deselect other checkboxes for the same question if one is checked
                    if (this.checked) {
                        const allChoicesForQuestion = document.querySelectorAll(
                            `.single-choice[data-question-id="${questionId}"]`
                        );
                        allChoicesForQuestion.forEach(choice => {
                            if (choice !== this) {
                                choice.checked = false;
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
