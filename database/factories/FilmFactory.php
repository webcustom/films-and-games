<?php

namespace Database\Factories;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Film>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    private static $filmCount = 1;

    public function definition(): array
    {
        $collectionIds = Collection::pluck('id')->toArray(); // Получаем все существующие идентификаторы коллекций
        // dd($collectionIds);
        return [
            //
            // 'category_id' => $this->faker->randomNumber(),
            // 'collection_id' => $this->faker->randomElement($collectionIds), // Выбираем случайный идентификатор коллекции
            // 'title' => $this->faker->sentence,
            'title' => 'Фильм ' . self::$filmCount++,
            'slug' => $this->faker->slug,
            'img' => $this->faker->imageUrl(),
            'img_medium' => $this->faker->imageUrl(),
            'img_thumbnail' => $this->faker->imageUrl(),
            'description' => $this->faker->paragraph,
            'rating_imdb' => $this->faker->randomFloat(1, 1, 10),
            'rating_kinopoisk' => $this->faker->randomFloat(1, 1, 10),
            'release' => $this->faker->date,
            'duration' => $this->faker->numberBetween(60, 240),
            'genre' => $this->faker->word,
            'country' => $this->faker->country,
            'budget' => $this->faker->randomNumber(6),
            'fees_usa' => $this->faker->randomNumber(5),
            'fees_world' => $this->faker->randomNumber(7),
            'director' => $this->faker->name,
            'cast' => [],//$this->faker->words(3, true),
            'published' => $this->faker->boolean,
            'published_at' => $this->faker->dateTime,
        ];
    }
}
