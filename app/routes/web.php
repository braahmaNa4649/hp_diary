<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/', [DiaryController::class, 'index'])->name('diary.index');
    Route::get('/create', [DiaryController::class, 'showCreate'])->name('diary.show_create');
    Route::post('/create', [DiaryController::class, 'create'])->name('diary.create');
    Route::get('/edit/{diary_id}', [DiaryController::class, 'showEdit'])->name('diary.show_edit');
    Route::post('/edit', [DiaryController::class, 'edit'])->name('diary.edit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
