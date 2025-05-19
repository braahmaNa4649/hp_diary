<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\UseCases\DiaryUseCase;
use App\Http\Controllers\Controller;

class DiaryController extends Controller
{
    /**
     * 一覧ページ表示
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $usecase = app(DiaryUseCase::class);
        $diaries = $usecase->index();
        return view('diary.index', compact('diaries'));
    }
}
