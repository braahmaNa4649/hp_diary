<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diary;

class DiarySeeder extends Seeder
{
    public function run(): void
    {
        $count = config('diary.pagination_count') * 5 + 1;
        Diary::factory()->count($count)->create();
    }
}
