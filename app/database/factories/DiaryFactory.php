<?php

namespace Database\Factories;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diary>
 */
class DiaryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => optional(User::inRandomOrder()->first())->id ?? User::factory(),
            'content' => $this->faker->realText(200),
            'file_name' => $this->faker->randomElement([
                $this->faker->unique()->lexify('????.jpg'),
                'noimage.jpg',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
