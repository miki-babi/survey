<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/survey/create', [SurveyController::class, 'createSurvey']);
Route::post('/survey/answer', [SurveyController::class, 'submitAnswer']);
Route::get('/survey/create', function () {
    return view('create-survey');
});
// Route::get('/survey/get', [SurveyController::class, 'getSurvey']);
Route::post('/survey/answer', [SurveyController::class, 'submitAnswer']);
Route::get('/survey/answer', function () {
    return view('answer-survey'); // Ensure this returns your Blade view
});
