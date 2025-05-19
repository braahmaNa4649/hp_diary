<?php

namespace App\UseCases;

use App\Repositories\DiaryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
}
