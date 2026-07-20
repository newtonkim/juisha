<?php

use App\Http\Controllers\{CategoryController, DashboardController, FocusController, FocusSessionController, HistoryController, TaskController, TimerSettingsController};
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    Route::get('/focus/{task}', [FocusController::class, 'show'])->name('focus.show');
    Route::post('/tasks/{task}/sessions', [FocusSessionController::class, 'store'])->name('sessions.store');
    Route::patch('/sessions/{session}/finish', [FocusSessionController::class, 'finish'])->name('sessions.finish');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/settings/timer', [TimerSettingsController::class, 'edit'])->name('settings.timer.edit');
    Route::patch('/settings/timer', [TimerSettingsController::class, 'update'])->name('settings.timer.update');
});

require __DIR__.'/settings.php';
