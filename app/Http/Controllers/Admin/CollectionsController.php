<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Film;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CollectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
        // dd($request);
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:50'], //строка поиска
            'selectionByCat' => ['nullable', 'string'],
        ]);

        // $query = Collection::with('films');
        $query = Collection::with(['films', 'games']);

        // $collection = Collection::find(2);
        // dd($collection->films()->count());
        // $query = Collection::query();
        // $collections = Collection::with('films')->latest('published_at')->paginate(12); //with('films') делаем для того что бы был один запрос к базе а не при каждой иттерации цикла



        if($search = $validated['search'] ?? null){
            $query->where('title', 'like', "%{$search}%");
        }

        if($selectionByCat = $validated['selectionByCat'] ?? null){
            $query->where('category_id', 'like',  "%{$selectionByCat}%");
        }

        $collections = $query->latest('published_at')->paginate(12);

        $collections->appends(request()->query()); //позволяет сохранить get параметры в url при пагинации т.е. при переходе на страницу 2 ссылка будет не http://127.0.0.1:8000/admin/collections?page=2 а http://127.0.0.1:8000/admin/collections?selectionByCat=2&page=2

        $categories = Category::all();

        return view('admin.collections.index', compact('collections', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        // $films = Film::all();
        $categories = Category::all();
        return view('admin.collections.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        Collection::validationCreate($request);



        // $category = Category::find($collection->category_id);


        // dd($request);
        // $validated = $request->validate(Collection::validationRules());
        // $film = Collection::query()->create(Collection::validationCreate($request));
        alert(__('Сохранено')); //добавляем сессию alert смотреть helpers.php
        return redirect()->route('admin.collections.index');
    }



     // редактирование -> страница формы
    public function edit(Request $request, $collection_id)
    {
        $collection = Collection::query()->findOrFail($collection_id); //позволяет получить конкретную запись из базы по ее id, а если ничего не найдет то вернет 404
        // получаем категорию к которой прикреплена коллекция
        // $category = Category::find($collection->category_id);
        $categories = Category::all();
        // $category = $categories->find($collection->category_id);

        

        //передаем наши коллекции и категории
        return view('admin.collections.edit', compact('collection', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        // dd($request);
        Collection::validationCreate($request, $collection);

        // получаем id фильмов связь с которыми нужно удалить и удаляем связь
        $deleteElemsId = $request->delete_elems;
        $deleteElemsId = explode(',', $deleteElemsId); //превращаем строку в массив

        // dd($collection->category_id);
        // получаем категорию к которой привязана наша коллекция
        $category = Category::find($collection->category_id);


        // dd($category);

        if(isset($category->slug)){
            foreach($deleteElemsId as $elem){
                // dump((int)$elem);
                // $records = DB::table('collection_film')->where('film_id', (int)$elem)->get();
                // dd($records);
                if($category->slug === 'films'){
                    DB::table('collection_film')->where('film_id', (int)$elem)->delete();
                }
                if($category->slug === 'games'){
                    DB::table('collection_game')->where('game_id', (int)$elem)->delete();
                }

                // DB::table('collection_game')->where('game_id', (int)$elem)->delete();

            }
        }

        alert(__('Сохранено'));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request){
        // получаем значение нашего поля field_delete_id
        // $сollection = Collection::find($request->field_delete_id);



        $collection = Collection::find($request->field_delete_id);
        // dd($collection);

        $category = Category::find($collection->category_id);

        // dd($category);
        // Перед удаление записи в базе удаляем связь в таблице collection_film  для этого можно использовать модель, но если модели нет, 
        // то можно использовать фасад DB
        // $relatedRecords = CollectionFilm::where('film_id', 46)->get();
        // DB::table('collection_film')->where('collection_id', $collection->id)->delete();
        // DB::table('collection_game')->where('collection_id', $collection->id)->delete();

        $imgs_paths = [$collection->img, $collection->img_medium, $collection->img_thumbnail];

        if(isset($category->slug)){
            if($category->slug === 'films'){
                DB::table('collection_film')->where('collection_id', $collection->id)->delete();
                // удаляем файл из хранилища если он там есть
                Film::deleteFiles($imgs_paths);

            }
            if($category->slug === 'games'){
                DB::table('collection_game')->where('collection_id', $collection->id)->delete();
                // удаляем файл из хранилища если он там есть
                Game::deleteFiles($imgs_paths); 

            }
        }

        // DB::table('collection_film')->where('film_id', $id)->delete();


        // удаляем файл из хранилища если он там есть
        // $imgs_paths = [$collection->img, $collection->img_medium, $collection->img_thumbnail];
        // Film::deleteFiles($imgs_paths);
        // Game::deleteFiles($imgs_paths);



        // // удаляем изображение фильма из хранилища
        // if(isset($collection->img)){
        //     Storage::delete($collection->img);
        // }
        // dd($collection);
        // удаляем данные из базы
        $collection->delete();
        alert(__('Подборка удалена'));

        return redirect()->route('admin.collections.index');
    }



    public function show($collection_slug){
        // dump($collection_slug);
        // dd($Collection);

        // получаем элемент по slug
        $collection = Collection::where('slug', $collection_slug)->first();
        // dd($collection);
        $category = Category::find($collection->category_id);
        // $films = Film::where()

        // $collection2 = Category::find(2);
        // if($collection->category_id === $category->id){
        //     $title = $category->title;
        //     $order = $collection->films;

        // }
        // dd($collection2);
        // dd($query);
        // $query->where('slug', 'like', "%{$collection_slug}%");
        // dd($query);
        return view('collections.show', compact('collection', 'category'));

    }

}
