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
    private int $maxContentLength = -1;
    private int $maxImageSize = -1;
    private string $imageType = '';

    /**
     * コンストラクタ
     *
     */
    public function __construct()
    {
        $this->usecase = app(DiaryUseCase::class);
        $this->maxContentLength = $this->usecase->getMaxContentLength();
        $this->maxImageSize = config('diary.max_image_size');
        $this->imageType = config('diary.image_type');
    }

    /**
     * 一覧ページ表示
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $diaries = $this->usecase->index();
        return view('diary.index', compact('diaries'));
    }

    /**
     * 日記作成ページ表示
     *
     * @return \Illuminate\View\View
     */
    public function showCreate(): View
    {
        $maxTextLength = $this->maxContentLength;
        $maxImageSize = $this->maxImageSize;
        $uploadUrl = route('diary.create');
        $listUrl = route('diary.index');
        $imageType = $this->imageType;
        return view('diary.create', compact('maxTextLength', 'maxImageSize', 'uploadUrl', 'listUrl', 'imageType'));
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

        $maxTextLength = $this->maxContentLength;
        $maxImageSize = $this->maxImageSize / 1024;
        $imageType = $this->imageType;
        $request->validate([
            'text' => "required|string|max:{$maxTextLength}",
            'image' => "nullable|image|mimes:{$imageType}|max:{$maxImageSize}",
        ]);

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
