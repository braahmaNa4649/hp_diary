<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diary>
 */
class DiaryFactory extends Factory
{
    public function definition(): array
    {
        return [
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
