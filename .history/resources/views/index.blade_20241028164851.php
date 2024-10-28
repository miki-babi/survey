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
                                   name="answers[{{ $question->id }}][choice_id]{{ $question->type === 'single' ? '' : '[]' }}"
                                   value="{{ $choice->id }}"
                                   id="choice_{{ $choice->id }}">
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
