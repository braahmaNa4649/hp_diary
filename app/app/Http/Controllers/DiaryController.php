<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\UseCases\DiaryUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiaryController extends Controller
{
    private ?DiaryUseCase $usecase = null;

    /**
     * コンストラクタ
     *
     */
    public function __construct()
    {
        $this->usecase = app(DiaryUseCase::class);
    }

    /**
     * 一覧ページ表示
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $diaries = $this->usecase->getIndexParameters();
        return view('diary.index', compact('diaries'));
    }

    /**
     * 日記作成ページ表示
     *
     * @return \Illuminate\View\View
     */
    public function showCreate(): View
    {
        $params = $this->usecase->getShowCreateParameters();
        return view('diary.create', $params);
    }

    /**
     * 日記作成
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $responseCode = 400;
        if (!$request->ajax()) {
            return response()->json(['message' => '不正なリクエストです'], $responseCode);
        }

        $createRules = $this->usecase->getCreateRules();
        $request->validate($createRules);

        $text = $request->input('text');
        $image = $request->file('image');
        list($isSuccess, $message) = $this->usecase->create($text, $image);

        if ($isSuccess) {
            $responseCode = 200;
            $message = '日記を作成しました';
        }
        return response()->json(['message' => $message], $responseCode);
    }
}
