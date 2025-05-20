<?php

namespace App\Repositories;

use App\Models\Diary;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DiaryRepository
{
    /**
     * 一覧ページのデータを取得
     *
     * @param int $count
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getIndexList(int $userId, int $count): LengthAwarePaginator
    {
        $diaries = Diary::where('user_id', $userId)->orderBy('created_at', 'desc')->paginate($count);
        return $diaries;
    }

    /**
     * 日記をDBに保存
     *
     * @param int $userId
     * @param string $content
     * @param string|null $fileName
     * @return bool
     */
    public function create(int $userId, string $content, ?string $fileName): bool
    {
        $diary = new Diary();
        $diary->user_id = $userId;
        $diary->content = $content;
        if ($fileName) {
            $diary->file_name = $fileName;
        }
        return $diary->save();
    }

    /**
     * 画像を保存
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @return string
     */
    public function storeImage($image): string
    {
        $filename = $image->hashName();
        $filePath = $image->storeAs('images', $filename, 'public');
        if (!Storage::disk('public')->exists($filePath)) {
            $filePath = '';
        }

        return $filePath;
    }

    /**
     * 日記の最大文字数（サイズ）をDBから取得
     *
     * @return int
     */
    public function getMaxContentLength(): int
    {
        return DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'diaries')
            ->where('COLUMN_NAME', 'content')
            ->value('CHARACTER_MAXIMUM_LENGTH');
    }
}
