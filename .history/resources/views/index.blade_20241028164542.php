<form id="answerForm" action="{{ route('survey.answer') }}" method="POST">
    @csrf
    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

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
                            <label for="choice_{{ $choice->id }}">{{ $choice->content }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <button type="submit">Submit Answers</button>
</form>
