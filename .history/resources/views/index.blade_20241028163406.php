<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Answer Survey</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .question { margin-bottom: 20px; }
        .choices { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Answer Survey</h1>
    <form id="answerForm" action="{{ route('survey.answer') }}" method="POST">
        @csrf

        <div id="surveyContainer">
            @foreach ($questions as $question)
                <div class="question">
                    <h3>{{ $question->content }}</h3>
                    <div class="choices">
                        @foreach ($question->choices as $choice)
                            <div>
                                <input type="{{ $question->type === 'single' ? 'radio' : 'checkbox' }}"
                                       name="answers[{{ $question->id }}]{{ $question->type === 'single' ? '' : '[]' }}"
                                       value="{{ $choice->id }}"
                                       id="choice_{{ $choice->id }}">
                                       <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                <label for="choice_{{ $choice->id }}">{{ $choice->content }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <button type="submit">Submit Answers</button>
    </form>
</body>
</html>
