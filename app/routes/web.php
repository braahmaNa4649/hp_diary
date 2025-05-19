<?php

use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DiaryController::class, 'index'])->name('diary.index');
