<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    private static $index = 0;
    public function definition(): array
    {


        // // Получаем текущую категорию по индексу
        // $category = $categories[self::$index];

        // // Увеличиваем индекс для следующего вызова
        // self::$index++;

        // // Если индекс превышает количество категорий, сбрасываем его
        // if (self::$index >= count($categories)) {
        //     self::$index = 0;
        // }
        $titles = ['Фильмы', 'Игры'];
        $slugs = ['films', 'games'];


        static $index = 0;

        $title = $titles[$index];
        $slug = $slugs[$index];

        $index = ($index + 1) % count($titles);

        return [
            'slug' => $slug,
            'title' => $title,
            // 'published' => $this->faker->boolean,
            'published_at' => $this->faker->dateTime,
        ];

        $index = $index + 1;
    }
}
