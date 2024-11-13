<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
// use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;


use App\Models\Film;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class FilmsController extends Controller
{
    public function __invoke(Request $request)
    {
        return 'Films__invoke';
    }
    

    public function index(Request $request){

        
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:50'], //строка поиска
        ]);


        $query = Film::with('collections');
        // $query = Film::query();
            // ->where('published', true);
            // ->whereNotNull('published_at');

        // для снижения количества запросов используем with() что бы не получать collection в цикле
        // $films = Film::with('collections')->latest('published_at')->paginate(12);

        // dd($films);

        
        // выводим посты по параметрам формы поиска
        if($search = $validated['search'] ?? null){
            $query->where('title', 'like', "%{$search}%");
        }
        // $films = $query->latest('published_at')->paginate(12); 
        $films = $query->latest('published_at')->paginate(48);


        // $category = Category::where('slug', 'films')->first();
        // $collections = $category->collections;

        // dd(Film::with('collections')->find(3));

        return view('admin.films.index', compact('films'));
    }


    public function create(){
        // $collections = Collection::all(); //передаем наши коллекции
        $category = Category::where('slug', 'films')->first();
        $collections = $category->collections;
        return view('admin.films.create', compact('collections'));
    }

    

    public function store(Request $request){
    
        // dd($request);
    
        // ЧЕТВЕРТЫЙ СПОСОБ ВАЛИДАЦИИ (такой же как и первый только прокидываем через созданную нами функцию validate() в helpers.php)
        // $validated = $request->validate(Film::validationRules());
        // dd(new Carbon($validated['published_at']));


        // if($request->file('img')){
        //     $manager = new ImageManager(new Driver());
        //     $name_gen = hexdec(uniqid()).'.'.$request->file('img')->getClientOriginalExtension();
        //     dd($name_gen);
        // }

        // $validated = $request->validate(Film::validationRules());
        // $validated = $request->validate([
        //     // пишем арпвила валидации для каждого элемента в запросе $request
        //     // если эти правила валидации часто повторяются их можно вынести в модель , как это сделать см. урок 16 конец ролика
        //     'title' => ['required', 'string', 'max:150'], // обязательный, строка, максимум 100 символов
        //     'description' => ['required', 'string', 'max:10000'],
        //     'category_id' => ['nullable', 'integer'],
        //     'rating_imdb' => ['nullable', 'string', 'max:30'],
        //     'rating_kinopoisk' => ['nullable', 'string', 'max:30'],
        //     'release' => ['nullable', 'string', 'max:30'],
        //     'duration' => ['nullable', 'string', 'max:30'],
        //     'genre' => ['nullable', 'string', 'max:30'],
        //     'country' => ['nullable', 'string', 'max:50'], ///////////////
        //     'budget' => ['nullable', 'integer'], 
        //     'fees_usa' => ['nullable', 'integer'],
        //     'fees_world' => ['nullable', 'integer'],
        //     'director' => ['nullable', 'string', 'max:100'],
        //     'cast' => ['nullable', 'string'],
        //     'published_at' => ['nullable', 'string', 'date'],
        //     'published' => ['nullable', 'boolean'],
        // ]);


        // Привязка фильма к коллекциям
        // if (isset($validated['collections'])) {
        //     $film->collections()->attach($validated['collections']);
        // }

        // $film = Film::query()->create(Film::validationCreate($request));

        // dd($film);
        // $film->collections()->attach($validated['collections']);

        Film::validationCreate($request);

        // $film = Film::query()->create([
        //     // 'user_id' => Film::query()->value('id'),
        //     'title' => $validated['title'],
        //     'description' => $validated['description'],
        //     'category_id' => Category::query()->value('id'), //$validated['category_id'],
        //     'rating_imdb' => $validated['rating_imdb'] ?? null,
        //     'rating_kinopoisk' => $validated['rating_kinopoisk'] ?? null,
        //     'release' => $validated['release'] ?? null,
        //     'duration' => $validated['duration'] ?? null,
        //     'genre' => $validated['genre'] ?? null,
        //     'country' => $validated['country'] ?? null, ///////////////
        //     'budget' => $validated['budget'] ?? null, 
        //     'fees_usa' => $validated['fees_usa'] ?? null,
        //     'fees_world' => $validated['fees_world'] ?? null,
        //     'director' => $validated['director'] ?? null,
        //     'cast' => $cast ?? null, //$validated['cast'] ?? null,
        //     'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
        //     // 'published_at' => isset($validated['published_at']) ? Carbon::parse($validated['published_at']) : null,
        //     'published' => $validated['published'] ?? false, // false значение по умолчанию
        // ]);
        
        
        alert(__('Сохранено')); //добавляем сессию alert смотреть helpers.php
        
        return redirect()->route('admin.films.index');
    }

    // редактирование -> страница формы
    public function edit(Request $request, $film_id){
        $film = Film::query()->findOrFail($film_id); //позволяет получить конкретную запись из базы по ее id, а если ничего не найдет то вернет 404
        // $collections = Collection::all(); //передаем наши коллекции

        // $category = Category::query()->findOrFail($category_id);
        // $category = Category::all();
        // $filmsCatId = 1;

        // выбираем категорию фильмы по slug и получаем коллекции привязанные к этой категории
        $category = Category::where('slug', 'films')->first();
        $collections = $category->collections;
        // выбираем коллекции из категории фильмы
        // $collections = $category->find($filmsCatId)->collections;

        return view('admin.films.edit', compact('film', 'collections'));
    }
    

    // редактирование -> сохранение изменений
    public function update(Request $request, Film $film){

        // $validated = $request->validate(Film::validationRules());
        // dd($request);
        // $validated = $request->validate(Film::validationRules());

        // $validated = $request->validate([
        //     // пишем арпвила валидации для каждого элемента в запросе $request
        //     // если эти правила валидации часто повторяются их можно вынести в модель , как это сделать см. урок 16 конец ролика
        //     'title' => ['required', 'string', 'max:150'], // обязательный, строка, максимум 100 символов
        //     'description' => ['required', 'string', 'max:10000'],
        //     'category_id' => ['nullable', 'integer'],
        //     'rating_imdb' => ['nullable', 'string', 'max:30'],
        //     'rating_kinopoisk' => ['nullable', 'string', 'max:30'],
        //     'release' => ['nullable', 'string', 'max:30'],
        //     'duration' => ['nullable', 'string', 'max:30'],
        //     'genre' => ['nullable', 'string', 'max:30'],
        //     'country' => ['nullable', 'string', 'max:50'], ///////////////
        //     'budget' => ['nullable', 'integer'], 
        //     'fees_usa' => ['nullable', 'integer'],
        //     'fees_world' => ['nullable', 'integer'],
        //     'director' => ['nullable', 'string', 'max:100'],
        //     'cast' => ['nullable', 'string'],
        //     'published_at' => ['nullable', 'string', 'date'],
        //     'published' => ['nullable', 'boolean'],
        // ]);

        // dd(Film::validationCreate($request, $film));

        // $film->update(Film::validationCreate($request, $film));
        Film::validationCreate($request, $film);


        // $film->update([
        //     // 'user_id' => Film::query()->value('id'),
        //     'title' => $validated['title'],
        //     'description' => $validated['description'],
        //     'category_id' => Category::query()->value('id'), //$validated['category_id'],
        //     'rating_imdb' => $validated['rating_imdb'] ?? null,
        //     'rating_kinopoisk' => $validated['rating_kinopoisk'] ?? null,
        //     'release' => $validated['release'] ?? null,
        //     'duration' => $validated['duration'] ?? null,
        //     'genre' => $validated['genre'] ?? null,
        //     'country' => $validated['country'] ?? null, ///////////////
        //     'budget' => $validated['budget'] ?? null, 
        //     'fees_usa' => $validated['fees_usa'] ?? null,
        //     'fees_world' => $validated['fees_world'] ?? null,
        //     'director' => $validated['director'] ?? null,
        //     'cast' => $cast ?? null, //$validated['cast'] ?? null,
        //     'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
        //     // 'published_at' => isset($validated['published_at']) ? Carbon::parse($validated['published_at']) : null,
        //     'published' => $validated['published'] ?? false, // false значение по умолчанию
        // ]);


        alert(__('Сохранено')); //добавляем сессию alert смотреть helpers.php
        return redirect()->back();
        // return redirect()->route('admin.films.index');

    }

    
    public function delete(Request $request){


        // dd($request);
        $ids = explode(",", $request->field_delete_id);
        // dd($ids);

        forEach($ids as $id){
            // получаем значение нашего поля field_delete_id
            $film = Film::find($id);

            // Перед удаление записи в базе удаляем связь в таблице collection_film  для этого можно использовать модель, но если модели нет, то можно использовать фасад DB
            DB::table('collection_film')->where('film_id', $id)->delete();

            
            // удаляем файл из хранилища если он там есть
            $imgs_paths = [$film->img, $film->img_medium, $film->img_thumbnail];
            Film::deleteFiles($imgs_paths);

            // удаляем данные из базы
            $film->delete();
        }


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
        $textDel = 'Фильм удален';
        if(count($ids) > 1){
            $textDel = 'Фильмы удалены';
        }
        
        alert(__($textDel));

        return redirect()->route('admin.films.index');
    }
}
