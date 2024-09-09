<?php

namespace Database\Factories;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Collection>
 */



class CollectionFactory extends Factory
{
    protected $model = Collection::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug,
            'title' => $this->faker->sentence,
            'img' => $this->faker->imageUrl(),
            'description' => $this->faker->paragraph,
            'published' => $this->faker->boolean,
            'published_at' => $this->faker->dateTime,
            // 'sort_films' => $this->faker->json,
            'category_id' => $this->faker->randomElement([1,2]),
            // 'category_id' => $this->faker->randomNumber([1,2]),
        ];
    }
}
