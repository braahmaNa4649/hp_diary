<?php

namespace App\UseCases;

use App\Models\Diary;
use App\Repositories\DiaryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\UploadedFile;

class DiaryUseCase
{
    use AuthorizesRequests;

    private $repository = null;

    /**
     * コンストラクタ
     *
     * @param \App\Repositories\DiaryRepository $repository
     */
    public function __construct(DiaryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 一覧ページに表示する日記データを取得
     *
     * @param int $count
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getIndexParameters(): LengthAwarePaginator
    {
        $userId = auth()->id();
        $paginationCount = config('diary.pagination_count');
        $diaries = $this->repository->getIndexList($userId, $paginationCount);
        return $diaries;
    }

    /**
     * 日記保存
     *
     * @param string $text
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return array
     */
    public function save(string $text, ?UploadedFile $image, int $diaryId = -1, bool $removeImage = false): array
    {
        $isSuccess = false;
        $fileName = '';

        if ($image) {
            $fileName = $this->repository->storeImage($image);
            if (!$fileName) {
                return [$isSuccess, 'ファイル保存に失敗しました'];
            }
        } elseif ($image === null && $removeImage) { # 編集で画像を削除する場合
            $fileName = $this->repository->getImageFileName($diaryId);
            $this->repository->removeImage($fileName);
            $fileName = $this->repository->getNoImageFileName();
        }

        $userId = auth()->id();
        if (!$this->repository->save($userId, $text, $fileName, $diaryId)) {
            return [$isSuccess, 'DB保存に失敗しました'];
        }

        $isSuccess = true;
        return [$isSuccess, '日記を作成しました'];
    }

    /**
     * 日記の最大文字数を取得
     *
     * @return int
     */
    public function getMaxContentLength(): int
    {
        return $this->repository->getMaxContentLength();
    }

    /**
     * 日記の最大画像サイズを取得
     *
     * @return int
     */
    public function getMaxImageSize(): int
    {
        return $this->repository->getMaxImageSize();
    }

    /**
     * 日記の画像タイプを取得
     *
     * @return string
     */
    public function getImageType(): string
    {
        return $this->repository->getImageType();
    }

    /**
     * 日記作成・編集時のバリデーションルールを取得
     *
     * @return array
     */
    public function getSaveRules(): array
    {
        $maxImageSize = $this->getMaxImageSize() / 1024; // KBに変換
        return [
            'text' => 'required|string|max:' . $this->getMaxContentLength(),
            'image' => 'nullable|image|mimes:' . $this->getImageType() . '|max:' . $maxImageSize
        ];
    }

    /**
     * 日記作成ページに渡すパラメータを取得
     *
     * @return array
     */
    public function getShowCreateParameters(): array
    {
        $maxTextLength = $this->getMaxContentLength();
        $maxImageSize = $this->getMaxImageSize();
        $uploadUrl = route('diary.create');
        $listUrl = route('diary.index');
        $imageType = $this->getImageType();
        $mode = 'create';
        return  compact('maxTextLength', 'maxImageSize', 'uploadUrl', 'listUrl', 'imageType', 'mode');
    }

    /**
     * 日記モデルをidにて取得
     *
     * @param int $diaryId
     * @return app\Models\Diary
     */
    public function getById(int $diaryId): Diary
    {
        return $this->repository->getById($diaryId);
    }

    /**
     * 日記編集ページに渡すパラメータを取得
     *
     * @return array
     */
    public function getShowEditParameters(int $diaryId): array
    {
        $maxTextLength = $this->getMaxContentLength();
        $maxImageSize = $this->getMaxImageSize();
        $uploadUrl = route('diary.edit');
        $listUrl = route('diary.index');
        $imageType = $this->getImageType();
        $mode = 'edit';
        $diary = $this->getById($diaryId);
        return  compact('maxTextLength', 'maxImageSize', 'uploadUrl', 'listUrl', 'imageType', 'mode', 'diary');
    }

    /**
     * 編集データ閲覧権限の確認
     *
     * @param int $diaryId
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function authorizeShowEdit(int $diaryId): void
    {
        $diary = $this->getById($diaryId);
        $this->authorize('view', $diary);
    }

    /**
     * 編集権限の確認
     *
     * @param int $diaryId
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function authorizeEdit(int $diaryId): void
    {
        $diary = $this->getById($diaryId);
        $this->authorize('update', $diary);
    }

    /**
     * 削除権限の確認
     *
     * @param int $diaryId
     * @return void
     */
    public function authorizeRemove(int $diaryId): void
    {
        $diary = $this->getById($diaryId);
        $this->authorize('delete', $diary);
    }

    /**
     * 日記削除
     *
     * @param int $diaryId
     * @return bool
     */
    public function remove(int $diaryId): bool
    {
        return $this->repository->remove($diaryId);
    }

    /**
     * 日記の画像がない場合のファイル名を取得
     *
     * @return string
     */
    public function getNoImageName(): string
    {
        return $this->repository->getNoImageFileName();
    }
}
