<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Support\Facades\DB;

class GamesController extends Controller
{
    public function __invoke(Request $request)
    {
        return 'Games__invoke';
    }
    

    public function index(Request $request)
    {
        $games = Game::with('collections')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest('published_at')
            ->paginate(48);

        return view('admin.games.index', compact('games'));
    }



    public function create()
    {
        $category = Category::where('slug', 'games')->first();
        $collections = $category->collections;

        return view('admin.games.create', compact('collections'));
    }




    // создание фильма
    public function store(GameRequest $request, GameService $gameService)
    {
        $gameService->createGame($request);
        alert(__('Сохранено'));
        return redirect()->route('admin.games.index');
    }
    


    // редактирование -> страница формы
    public function edit(Game $game)
    {
        $category = Category::where('slug', 'games')->first();
        $collections = $category->collections;

        return view('admin.games.edit', compact('game', 'collections'));
    }




    // редактирование -> сохранение изменений
    public function update(GameRequest $request, Game $game, GameService $gameService)
    {
        $gameService->updateGame($request, $game);
        alert(__('Сохранено'));

        return redirect()->back();
    }




    public function delete(Request $request, GameService $gameService)
    {
        $ids = explode(",", $request->field_delete_id);
        $games = Game::findMany($ids);

        foreach ($games as $game) {
            $gameService->deleteGame($game);
        }

        alert(count($ids) > 1 ? __('Игры удалены') : __('Игра удалена'));
        return redirect()->route('admin.games.index');
    }


    // // редактирование -> страница формы
    // public function edit(Request $request, $game_id){
    //     $game = Game::query()->findOrFail($game_id); //позволяет получить конкретную запись из базы по ее id, а если ничего не найдет то вернет 404
    //     // $collections = Collection::all(); //передаем наши коллекции


    //     // выбираем категорию игры по slug и получаем коллекции привязанные к этой категории
    //     $category = Category::where('slug', 'games')->first();
    //     // dd($category);
    //     // выбираем коллекции из категории фильмы
    //     // $collections = $category->find($gamesCatId)->collections;
    //     $collections = $category->collections;


    //     // dd($game);
    //     return view('admin.games.edit', compact('game', 'collections'));
    // }
    

    // // редактирование -> сохранение изменений
    // public function update(Request $request, Game $game){

    //     // $validated = $request->validate(Film::validationRules());
    //     // dd($request);
    //     // $validated = $request->validate(Film::validationRules());

    //     // $validated = $request->validate([
    //     //     // пишем арпвила валидации для каждого элемента в запросе $request
    //     //     // если эти правила валидации часто повторяются их можно вынести в модель , как это сделать см. урок 16 конец ролика
    //     //     'title' => ['required', 'string', 'max:150'], // обязательный, строка, максимум 100 символов
    //     //     'description' => ['required', 'string', 'max:10000'],
    //     //     'category_id' => ['nullable', 'integer'],
    //     //     'rating_imdb' => ['nullable', 'string', 'max:30'],
    //     //     'rating_kinopoisk' => ['nullable', 'string', 'max:30'],
    //     //     'release' => ['nullable', 'string', 'max:30'],
    //     //     'duration' => ['nullable', 'string', 'max:30'],
    //     //     'genre' => ['nullable', 'string', 'max:30'],
    //     //     'country' => ['nullable', 'string', 'max:50'], ///////////////
    //     //     'budget' => ['nullable', 'integer'], 
    //     //     'fees_usa' => ['nullable', 'integer'],
    //     //     'fees_world' => ['nullable', 'integer'],
    //     //     'director' => ['nullable', 'string', 'max:100'],
    //     //     'cast' => ['nullable', 'string'],
    //     //     'published_at' => ['nullable', 'string', 'date'],
    //     //     'published' => ['nullable', 'boolean'],
    //     // ]);

    //     // dd(Film::validationCreate($request, $film));

    //     // $film->update(Film::validationCreate($request, $film));
    //     Game::validationCreate($request, $game);


    //     // $film->update([
    //     //     // 'user_id' => Film::query()->value('id'),
    //     //     'title' => $validated['title'],
    //     //     'description' => $validated['description'],
    //     //     'category_id' => Category::query()->value('id'), //$validated['category_id'],
    //     //     'rating_imdb' => $validated['rating_imdb'] ?? null,
    //     //     'rating_kinopoisk' => $validated['rating_kinopoisk'] ?? null,
    //     //     'release' => $validated['release'] ?? null,
    //     //     'duration' => $validated['duration'] ?? null,
    //     //     'genre' => $validated['genre'] ?? null,
    //     //     'country' => $validated['country'] ?? null, ///////////////
    //     //     'budget' => $validated['budget'] ?? null, 
    //     //     'fees_usa' => $validated['fees_usa'] ?? null,
    //     //     'fees_world' => $validated['fees_world'] ?? null,
    //     //     'director' => $validated['director'] ?? null,
    //     //     'cast' => $cast ?? null, //$validated['cast'] ?? null,
    //     //     'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
    //     //     // 'published_at' => isset($validated['published_at']) ? Carbon::parse($validated['published_at']) : null,
    //     //     'published' => $validated['published'] ?? false, // false значение по умолчанию
    //     // ]);


    //     alert(__('Сохранено')); //добавляем сессию alert смотреть helpers.php
    //     return redirect()->back();
    //     // return redirect()->route('admin.films.index');

    // }

    
    // public function delete(Request $request){

    //     $ids = explode(",", $request->field_delete_id);
    //     $games = Game::findMany($ids);


    //     forEach($games  as $game){
    //         // Перед удаление записи в базе удаляем связь в таблице collection_film  для этого можно использовать модель, но если модели нет, то можно использовать фасад DB
    //         DB::table('collection_game')->where('game_id', $game->id)->delete();

    //         // удаляем файл из хранилища если он там есть
    //         $imgs_paths = [$game->img_medium, $game->img_thumbnail];

    //         $additional_imgs = json_decode($game->additional_imgs);
    //         foreach($additional_imgs as $key => $elem){
    //             $imgs_paths[] = $additional_imgs[$key]->image->medium;
    //             $imgs_paths[] = $additional_imgs[$key]->image->thumbnail;
    //         }

    //         Game::deleteFiles($imgs_paths);

    //         // удаляем данные из базы
    //         $game->delete();
    //     }



    //     // выводим сообщение
    //     $textDel = 'Игра удален';
    //     if(count($ids) > 1){
    //         $textDel = 'Игры удалены';
    //     }
        
    //     alert(__($textDel));

    //     return redirect()->route('admin.games.index');
    // }



        // // dd($request);
        // $ids = explode(",", $request->field_delete_id);
        // // dd($ids);

        // forEach($ids as $id){
        //     // получаем значение нашего поля field_delete_id
        //     $game = Game::find($id);

        //     // Перед удаление записи в базе удаляем связь в таблице collection_film  для этого можно использовать модель, но если модели нет, то можно использовать фасад DB
        //     DB::table('collection_game')->where('game_id', $id)->delete();

            
        //     // удаляем файл из хранилища если он там есть
        //     $imgs_paths = [$game->img, $game->img_medium, $game->img_thumbnail];
        //     Game::deleteFiles($imgs_paths);

        //     // удаляем данные из базы
        //     $game->delete();
        // }


        // dd($ids);

        // получаем значение нашего поля field_delete_id
        // $film = Film::find($request->field_delete_id);


        // Перед удаление записи в базе удаляем связь в таблице collection_film  для этого можно использовать модель, но если модели нет, то можно использовать фасад DB
        // $relatedRecords = CollectionFilm::where('film_id', 46)->get();
        // DB::table('collection_film')->where('film_id', $film->id)->delete();


        // удаляем файл из хранилища если он там есть
        // $imgs_paths = [$film->img, $film->img_medium, $film->img_thumbnail];
        // Film::deleteFiles($imgs_paths);

        
        // удаляем данные из базы
        // $film->delete();
    //     $textDel = 'Игра удалена';
    //     if(count($ids) > 1){
    //         $textDel = 'Игры удалены';
    //     }
        
    //     alert(__($textDel));

    //     return redirect()->route('admin.games.index');
    // }
}
