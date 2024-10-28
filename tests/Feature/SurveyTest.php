<?php

namespace Tests\Feature;

use App\Models\Choice;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_survey_can_be_created()
    {
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

        $response = $this->post('/survey/create', $data); // Adjust route as necessary

        $response->assertStatus(302); // Check for redirect after creation
        $this->assertDatabaseHas('questions', [
            'content' => 'What is your favorite color?',
        ]);
    }

    /** @test */
    public function a_survey_can_be_fetched()
    {
        // Create a survey with a question and choices
        $question = Question::create([
            'content' => 'What is your favorite color?',
            'is_final' => true,
            'type' => 'single',
        ]);

        Choice::create(['content' => 'Red', 'question_id' => $question->id]);
        Choice::create(['content' => 'Blue', 'question_id' => $question->id]);

        $response = $this->get('/survey/get'); // Adjust endpoint as needed

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
        // Create a question and choice for answering
        $question = Question::create([
            'content' => 'What is your favorite color?',
            'is_final' => true,
            'type' => 'single',
        ]);

        $choice = Choice::create(['content' => 'Red', 'question_id' => $question->id]);

        $data = [
            'answers' => [
                $question->id => $choice->id,
            ],
        ];

        $response = $this->post('/survey/answer', $data); // Adjust route as necessary

        $response->assertStatus(302); // Check for redirect after submission
        $this->assertDatabaseHas('answers', [
            'question_id' => $question->id,
            'choice_id' => $choice->id,
        ]);
    }
}
