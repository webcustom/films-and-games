<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Film;
use App\Models\Game;
use App\Models\User;
use Database\Factories\CollectionFilmFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// use Illuminate\Support\Collection;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory(1)->create();
        Category::factory(2)->create();

        $collections = Collection::factory(30)->create();
        $films = Film::factory(50)->create();
        $games = Game::factory(50)->create();




        // выбираем только подборки привязанные к разделам фильмы и игры
        $collections_films = $collections->filter(function ($collection) {
            return $collection->category_id === 1;
        });
        
        $collections_games = $collections->filter(function ($collection) {
            return $collection->category_id === 2;
        });



        // Связываем фильмы и коллекции
        foreach ($films as $film) {
            $randomCollections = $collections_films->random(rand(1, 2)); // выбираем от 1 до 3 случайных коллекций
            $film->collections()->attach($randomCollections);
        }

        foreach ($games as $game) {
            $randomCollections = $collections_games->random(rand(1, 2)); // выбираем от 1 до 3 случайных коллекций
            $game->collections()->attach($randomCollections);
        }


    }
}
