<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;


class SurveyController extends Controller
{
    public function createSurvey(Request $request)
    {

        // dd($request->all());

        try {

            $data = $request->validate([
                'user_id' => 'required|integer',
                'questions' => 'required|array',
                'questions.*.content' => 'required|string',
                'questions.*.is_final' => 'required|boolean',
                'questions.*.type' => 'required|in:single,multiple',
                'questions.*.choices' => 'required|array',
                'questions.*.choices.*.content' => 'required|string',
                'questions.*.choices.*.next_question_id' => 'nullable|integer'
            ]);


            // dd($data);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json(['errors' => $e->errors()], 422);
        }


        foreach ($data['questions'] as $questionData) {

            $question = Question::create([
                'content' => $questionData['content'],
                'is_final' => (bool)$questionData['is_final'],
                'type' => $questionData['type']
            ]);


            if (isset($questionData['choices'])) {
                foreach ($questionData['choices'] as $choiceData) {
                    $question->choices()->create([
                        'content' => $choiceData['content'],
                        'next_question_id' => $choiceData['next_question_id']
                    ]);
                }
            }
        }


        return response()->json(['message' => 'Survey created successfully'], 201);
    }


    public function editSurvey($id)
    {


        $questions = Question::with('choices')->where('id', $id)->get();


        $existingQuestions = Question::all();


        return view('edit-survey', compact('questions', 'existingQuestions'));
    }


public function updateSurvey(Request $request, $id)
{

    $data = $request->validate([
        'questions' => 'required|array',
        'questions.*.id' => 'required|exists:questions,id',
        'questions.*.content' => 'required|string',
        'questions.*.is_final' => 'required|boolean',
        'questions.*.type' => 'required|in:single,multiple',
        'questions.*.choices' => 'required|array',
        'questions.*.choices.*.id' => 'sometimes|exists:choices,id',
        'questions.*.choices.*.content' => 'required|string',
        'questions.*.choices.*.next_question_id' => 'nullable|integer'
    ]);


    foreach ($data['questions'] as $questionData) {

        $question = Question::find($questionData['id']);


        $question->update([
            'content' => $questionData['content'],
            'is_final' => (bool)$questionData['is_final'],
            'type' => $questionData['type']
        ]);


        $providedChoiceIds = [];

        if (isset($questionData['choices'])) {
            foreach ($questionData['choices'] as $choiceData) {
                if (isset($choiceData['id'])) {

                    $choice = $question->choices()->find($choiceData['id']);
                    if ($choice) {
                        $choice->update([
                            'content' => $choiceData['content'],
                            'next_question_id' => $choiceData['next_question_id']
                        ]);
                        $providedChoiceIds[] = $choice->id;
                    }
                } else {

                    $newChoice = $question->choices()->create([
                        'content' => $choiceData['content'],
                        'next_question_id' => $choiceData['next_question_id']
                    ]);
                    $providedChoiceIds[] = $newChoice->id;
                }
            }
        }


        $question->choices()->whereNotIn('id', $providedChoiceIds)->delete();
    }


    return response()->json(['message' => 'Survey updated successfully'], 200);
}




    public function getQuestionIds()
    {
        $existingQuestions = Question::pluck('id')->toArray();
        return response()->json($existingQuestions);
    }
    public function showCreateSurveyForm()
    {
        $existingQuestions = Question::pluck('id')->toArray();
        return view('create-survey', compact('existingQuestions'));
    }



    public function submitAnswers(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.choice_id' => 'required|array',
            'answers.*.choice_id.*' => 'integer|exists:choices,id'
        ]);

        foreach ($data['answers'] as $answerData) {
            foreach ($answerData['choice_id'] as $choiceId) {
                Answer::create([
                    'user_id' => $data['user_id'],
                    'question_id' => $answerData['question_id'],
                    'choice_id' => $choiceId
                ]);
            }
        }

        return response()->json(['message' => 'Answers submitted successfully'], 201);
    }



public function getSurvey()
{

    $questions = Question::with('choices')->get();


    return view('index', [
        'questions' => $questions
    ]);
}


}
