<?php

namespace Database\Factories;

use App\UseCases\DiaryUseCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diary>
 */
class DiaryFactory extends Factory
{
    public function definition(): array
    {
        $noImgName=app(DiaryUseCase::class)->getNoImgName();
        return [
            'user_id' => optional(User::inRandomOrder()->first())->id ?? User::factory(),
            'content' => $this->faker->realText(200),
            'file_name' => $noImgName,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
