<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Choice;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

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
                'is_final' => (bool)$questionData['is_final'], // Ensure it's a boolean
                'type' => $questionData['type']
            ]);

            // If choices are provided, loop through and create them
            if (isset($questionData['choices'])) {
                foreach ($questionData['choices'] as $choiceData) {
                    $question->choices()->create([
                        'content' => $choiceData['content'],
                        'next_question_id' => $choiceData['next_question_id'] // You may want to validate this before creating
                    ]);
                }
            }
        }

        // Return a success response
        return response()->json(['message' => 'Survey created successfully'], 201);
    }


   public function submitAnswer(Request $request)
   {
       $data = $request->validate([
           'user_id' => 'required|integer|exists:users,id',
           'answers' => 'required|array',
           'answers.*.question_id' => 'required|integer|exists:questions,id',
           'answers.*.choice_id' => 'required|integer|exists:choices,id'
       ]);

       foreach ($data['answers'] as $answerData) {
           Answer::create([
               'user_id' => $data['user_id'],
               'question_id' => $answerData['question_id'],
               'choice_id' => $answerData['choice_id']
           ]);
       }

       return response()->json(['message' => 'Answers submitted successfully'], 201);
   }
   public function getSurvey()
{
    $questions = Question::get();

    return response()->json([
        'questions' => $questions->map(function ($question) {
            return [
                'id' => $question->id,
                'content' => $question->content,
                'type' => $question->type,
                'choices' => $question->choices->map(function ($choice) {
                    return [
                        'id' => $choice->id,
                        'content' => $choice->content,
                    ];
                }),
            ];
        }),
    ]);
}

}
