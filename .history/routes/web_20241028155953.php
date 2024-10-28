<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



use App\Http\Controllers\SurveyController;

// Create a survey
Route::post('/survey/create', [SurveyController::class, 'createSurvey'])->name('survey.create');
Route::get('/survey/create',
function () {
    return view('create-survey');
}
)->name('survey.create');

// Get a survey (for answering)
Route::get('/survey/get', [SurveyController::class, 'getSurvey'])->name('survey.get');

// Submit answers for a survey
Route::post('/survey/answer', [SurveyController::class, 'submitAnswers'])->name('survey.answer');




require __DIR__.'/auth.php';
