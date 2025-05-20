<?php

namespace App\UseCases;

use App\Repositories\DiaryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class DiaryUseCase
{
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
    public function index(): LengthAwarePaginator
    {
        $userId = auth()->id();
        $paginationCount = config('diary.pagination_count');
        $diaries = $this->repository->getIndexList($userId, $paginationCount);
        return $diaries;
    }

    /**
     * 日記作成
     *
     * @param string $text
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return array
     */
    public function create(string $text, ?UploadedFile $image): array
    {
        $isSuccess = false;
        $filePath = '';

        if ($image) {
            $filePath = $this->repository->storeImage($image);
            if (!$filePath) {
                return [$isSuccess, 'ファイル保存に失敗しました。'];
            }
        }

        $userId = auth()->id();
        if (!$this->repository->create($userId, $text, pathinfo($filePath, PATHINFO_BASENAME))) {
            return [$isSuccess, 'DB保存に失敗しました。'];
        }

        $isSuccess = true;
        return [$isSuccess, '日記を作成しました。'];
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
}
