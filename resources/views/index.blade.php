<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Form</title>
    <style>
        .error-message {
            color: red;
            display: none;
            margin-top: 5px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <form id="answerForm" action="{{ route('survey.answer') }}" method="POST">
        @csrf

        <div id="surveyContainer">
            @foreach ($questions as $question)
                <div class="question">
                    <h3>{{ $question->content }}</h3>
                    <div class="choices" data-question-id="{{ $question->id }}">
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
                    <span class="error-message" id="error-{{ $question->id }} ">Please select at least one option for this question.</span>
                </div>
            @endforeach
        </div>
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        <button type="submit">Submit Answers</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('answerForm');
            const singleChoiceCheckboxes = document.querySelectorAll('.single-choice');


            singleChoiceCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const questionId = this.dataset.questionId;
                    if (this.checked) {
                        document.querySelectorAll(`.single-choice[data-question-id="${questionId}"]`).forEach(choice => {
                            if (choice !== this) choice.checked = false;
                        });
                    }
                });
            });


            form.addEventListener('submit', function(event) {
                let isValid = true;


                document.querySelectorAll('.error-message').forEach(error => {
                    error.style.display = 'none';
                });


                document.querySelectorAll('.choices').forEach(choicesContainer => {
                    const questionId = choicesContainer.dataset.questionId;
                    const checkedChoices = choicesContainer.querySelectorAll('input[type="checkbox"]:checked');

                    if (checkedChoices.length === 0) {
                        isValid = false;
                        document.getElementById(`error-${questionId}`).style.display = 'block';
                    }
                });


                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
