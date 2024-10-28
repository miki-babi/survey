<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Choice;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    // public function createSurvey(Request $request)
    // {
    //     $data = $request->validate([
    //         'content' => 'required|string',
    //         'is_final' => 'required|boolean',
    //         'type' => 'required|in:single,multiple',
    //     ]);

    //     // Save the question to the database
    //     $question = Question::create($data);

    //     return response()->json(['message' => 'Question added successfully', 'question_id' => $question->id]);
    // }
    // public function createSurvey(Request $request)
    // {
    //     // Validate incoming request data
    //     $data = $request->validate([
    //         'questions.*.content' => 'required|string|max:255',
    //         'questions.*.is_final' => 'required|boolean',
    //         'questions.*.type' => 'required|in:single,multiple',
    //         'questions.*.choices.*.content' => 'required|string|max:255',
    //         'questions.*.choices.*.next_question_id' => 'nullable|integer|exists:questions,id',
    //     ]);

    //     $questions = [];

    //     // Loop through each question
    //     foreach ($data['questions'] as $questionData) {
    //         // Save the question to the database
    //         $question = Question::create([
    //             'content' => $questionData['content'],
    //             'is_final' => $questionData['is_final'],
    //             'type' => $questionData['type'],
    //         ]);

    //         // Handle choices if present
    //         if (isset($questionData['choices'])) {
    //             foreach ($questionData['choices'] as $choiceData) {
    //                 Choice::create([
    //                     'content' => $choiceData['content'],
    //                     'question_id' => $question->id,
    //                     'next_question_id' => $choiceData['next_question_id'], // save the next question ID if present
    //                 ]);
    //             }
    //         }

    //         // Add the newly created question to the questions array
    //         $questions[] = [
    //             'id' => $question->id,
    //             'content' => $question->content,
    //         ];
    //     }

    //     return response()->json(['message' => 'Questions added successfully', 'questions' => $questions]);
    // }

    public function editSurvey($id)
{
    // Assuming 'id' is the survey ID or some identifier for the survey
    $survey = Survey::with('questions.choices')->findOrFail($id);

    return view('surveys.edit', compact('survey'));
}

public function updateSurvey(Request $request, $id)
{
    $data = $request->validate([
        'questions' => 'required|array',
        'questions.*.content' => 'required|string',
        'questions.*.is_final' => 'required|boolean',
        'questions.*.type' => 'required|in:single,multiple',
        'questions.*.choices' => 'required|array',
        'questions.*.choices.*.content' => 'required|string',
        'questions.*.choices.*.next_question_id' => 'nullable|integer|exists:questions,id',
    ]);

    // Update each question
    foreach ($data['questions'] as $questionData) {
        $question = Question::findOrFail($questionData['id']);
        $question->update([
            'content' => $questionData['content'],
            'is_final' => $questionData['is_final'],
            'type' => $questionData['type'],
        ]);

        // Update choices if needed
        foreach ($questionData['choices'] as $choiceData) {
            // Assuming 'id' is present in choice data to identify the choice
            if (isset($choiceData['id'])) {
                $choice = $question->choices()->findOrFail($choiceData['id']);
                $choice->update([
                    'content' => $choiceData['content'],
                    'next_question_id' => $choiceData['next_question_id'],
                ]);
            } else {
                // If no id is provided, create a new choice
                $question->choices()->create([
                    'content' => $choiceData['content'],
                    'next_question_id' => $choiceData['next_question_id'],
                ]);
            }
        }
    }

    return response()->json(['message' => 'Survey updated successfully']);
}
    public function getQuestionIds()
    {
        $existingQuestions = Question::pluck('id')->toArray();
        return response()->json($existingQuestions);
    }
    public function showCreateSurveyForm()
    {
        $existingQuestions = Question::pluck('id')->toArray(); // Fetch existing question IDs
        return view('create-survey', compact('existingQuestions')); // Pass the variable to the view
    }



    public function submitAnswers(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.choice_id' => 'required|array', // Ensures this is an array
            'answers.*.choice_id.*' => 'integer|exists:choices,id' // Validates each choice_id in the array
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



//    public function getSurvey()
// {
//     $questions = Question::get();

//     return response()->json([
//         'questions' => $questions->map(function ($question) {
//             return [
//                 'id' => $question->id,
//                 'content' => $question->content,
//                 'type' => $question->type,
//                 'choices' => $question->choices->map(function ($choice) {
//                     return [
//                         'id' => $choice->id,
//                         'content' => $choice->content,
//                     ];
//                 }),
//             ];
//         }),
//     ]);
// }
public function getSurvey()
{
    // Retrieve all questions along with their choices
    $questions = Question::with('choices')->get();

    // Return the Blade view and pass the questions data to it
    return view('index', [
        'questions' => $questions
    ]);
}


}
