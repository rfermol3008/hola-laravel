<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectParticipantController;

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
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/participants', [ProjectParticipantController::class, 'store'])
        ->name('projects.participants.store');
    Route::delete('projects/{project}/participants/{user}', [ProjectParticipantController::class, 'destroy'])
        ->name('projects.participants.destroy');
    Route::patch('projects/{project}/participants/{user}', [ProjectParticipantController::class, 'update'])
    ->name('projects.participants.update');
});

require __DIR__ . '/auth.php';
