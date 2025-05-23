<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\UseCases\DiaryUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        return view('diary.save', $params);
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

        $saveRules = $this->usecase->getSaveRules();
        $request->validate($saveRules);

        $text = $request->input('text');
        $image = $request->file('image');
        list($isSuccess, $message) = $this->usecase->save($text, $image);

        if ($isSuccess) {
            $responseCode = 200;
            $message = '日記を作成しました';
        }
        return response()->json(['message' => $message], $responseCode);
    }

    /**
     * 日記編集ページ表示
     *
     * @param \Illuminate\Http\Request $request
     * @param int $diaryId
     * @return \Illuminate\View\View
     */
    public function showEdit(Request $request, int $diaryId): View
    {
        $this->usecase->authorizeShowEdit($diaryId);
        $params = $this->usecase->getShowEditParameters($diaryId);
        return view('diary.save', $params);
    }

    /**
     * 日記編集
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        $responseCode = 400;
        if (!$request->ajax()) {
            return response()->json(['message' => '不正なリクエストです'], $responseCode);
        }

        $diaryId = (int)$request->input('diary_id');
        $text = $request->input('text');
        $image = $request->file('image');
        // true/falseが文字列として渡されるので、booleanに変換
        $removeImage = filter_var($request->input('remove_image'), FILTER_VALIDATE_BOOLEAN);

        $this->usecase->authorizeEdit($diaryId);

        $saveRules = $this->usecase->getSaveRules();
        $request->validate($saveRules);

        list($isSuccess, $message) = $this->usecase->save($text, $image, $diaryId, $removeImage);

        if ($isSuccess) {
            $responseCode = 200;
            $message = '日記を編集しました';
        }
        return response()->json(['message' => $message], $responseCode);
    }

    /**
     * 日記削除
     *
     * @param \Illuminate\Http\Request $request
     * @param int $diaryId
     * @return \Illuminate\Http\redirectResponse
     */
    public function remove(Request $request, int $diaryId): RedirectResponse
    {
        $responseCode = 400;

        $this->usecase->authorizeRemove($diaryId);

        if ($this->usecase->remove($diaryId)) {
            $responseCode = 200;
            $key = 'success';
            $message = '日記を削除しました';
        } else {
            $key = 'error';
            $message = '日記の削除に失敗しました';
        }
        return redirect(route('diary.index'))
            ->with($key, $message)
            ->with('responseCode', $responseCode);
    }
}
