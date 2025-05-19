<?php

namespace App\Repositories;

use App\Models\Diary;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DiaryRepository
{
    /**
     * 一覧ページのデータを取得
     *
     * @param int $count
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getIndexList(int $count): LengthAwarePaginator
    {
        $diaries = Diary::orderBy('created_at', 'desc')->paginate($count);
        return $diaries;
    }
}
