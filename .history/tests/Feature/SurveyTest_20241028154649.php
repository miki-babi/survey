<?php

namespace Tests\Feature;

use App\Models\Choice;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SurveyTest extends TestCase
{
    use RefreshDatabase; // This will reset the database after each test

    /** @test */
    public function a_survey_can_be_created()
    {
        // Simulate data for the survey
        $data = [
            'questions' => [
                [
                    'content' => 'What is your favorite color?',
                    'is_final' => true,
                    'type' => 'single',
                    'choices' => [
                        ['content' => 'Red', 'next_question_id' => null],
                        ['content' => 'Blue', 'next_question_id' => null],
                    ],
                ],
            ],
        ];

        $response = $this->post('/survey/create', $data); // Adjust the endpoint as needed

        $response->assertStatus(302); // Check for redirection
        $this->assertDatabaseHas('questions', [
            'content' => 'What is your favorite color?',
        ]);
    }

    /** @test */
    public function a_survey_can_be_fetched()
    {
        // Create a survey with a question and choice
        $survey = Survey::create(['title' => 'Favorite Colors']);
        $question = Question::create([
            'content' => 'What is your favorite color?',
            'is_final' => true,
            'type' => 'single',
            'survey_id' => $survey->id,
        ]);
        Choice::create(['content' => 'Red', 'question_id' => $question->id]);

        $response = $this->get('/survey/get'); // Adjust the endpoint to match your fetching logic

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'questions' => [
                '*' => [
                    'id',
                    'content',
                    'type',
                    'choices' => [
                        '*' => [
                            'id',
                            'content',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function survey_answers_can_be_submitted()
    {
        // Create a survey and questions for answering
        $survey = Survey::create(['title' => 'Favorite Colors']);
        $question = Question::create([
            'content' => 'What is your favorite color?',
            'is_final' => true,
            'type' => 'single',
            'survey_id' => $survey->id,
        ]);
        $choice = Choice::create(['content' => 'Red', 'question_id' => $question->id]);

        $data = [
            'answers' => [
                $question->id => $choice->id,
            ],
        ];

        $response = $this->post('/survey/answer', $data); // Adjust the endpoint as needed

        $response->assertStatus(302); // Check for redirection after submission
        $this->assertDatabaseHas('answers', [
            'question_id' => $question->id,
            'choice_id' => $choice->id,
        ]);
    }
}
