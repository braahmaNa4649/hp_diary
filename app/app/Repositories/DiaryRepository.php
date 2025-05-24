<?php

namespace App\Repositories;

use App\Models\Diary;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Filesystem\FilesystemAdapter;
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
        $diaries = Diary::where('user_id', $userId)->orderBy('updated_at', 'desc')->paginate($count);
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
    public function save(int $userId, string $content, string $fileName, int $diaryId = -1): bool
    {
        if ($diaryId > 0) {
            $diary = Diary::find($diaryId);
        } else {
            $diary = app(Diary::class);
        }

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
        $fileName = $image->hashName();
        $filePath = $image->storeAs('images', $fileName, 'public');
        if (!$this->getPublicDisk()->exists($filePath)) {
            $filePath = '';
        }
        return $fileName;
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

    /**
     * 日記の最大画像サイズを設定から取得
     *
     * @return int
     */
    public function getMaxImageSize(): int
    {
        return config('diary.max_image_size');
    }

    /**
     * 日記の画像タイプを設定から取得
     *
     * @return string
     */
    public function getImageType(): string
    {
        return config('diary.image_type');
    }

    /**
     * idから日記モデルを取得
     *
     * @param int $diaryId
     * @return \App\Models\Diary
     */
    public function getById(int $diaryId): Diary
    {
        // 見つからない場合は404エラーを返す
        return Diary::findOrFail($diaryId);
    }

    /**
     * 日記の画像がない場合のファイル名をDBから取得
     *
     * @return string
     */
    public function getNoImageFileName(): string
    {
        $default = DB::selectOne("
            SELECT COLUMN_DEFAULT
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = 'diaries'
                AND COLUMN_NAME = 'file_name'
                AND TABLE_SCHEMA = DATABASE()
        ");
        return $default->COLUMN_DEFAULT;
    }

    /**
     * 日記データの削除フラグを立てる
     *
     * @param int $diaryId
     * @return bool
     */
    public function remove(int $diaryId): bool
    {
        $diary = Diary::find($diaryId);
        if (!$diary) {
            return false;
        }

        $isRemoved = $this->removeImage($diary->file_name);
        return $diary->delete();
    }

    /**
     * 画像ファイルを削除
     *
     * @param string $imageFileName
     * @return bool
     */
    public function removeImage(string $imageFileName): bool
    {
        $isSuccess = true;
        if ($imageFileName !== $this->getNoImageFileName()) {
            $isSuccess = $this->getPublicDisk()->delete('images/' . $imageFileName);
        }
        return $isSuccess;
    }

    /**
     * 日記の画像ファイル名を取得
     *
     * @param int $diaryId
     * @return string
     */
    public function getImageFileName(int $diaryId): string
    {
        $diary = Diary::find($diaryId);
        if ($diary) {
            $fileName = $diary->file_name;
        } else {
            $fileName = '';
        }
        return $fileName;
    }

    /**
     * 公開ディスクを取得
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    private function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }
}
