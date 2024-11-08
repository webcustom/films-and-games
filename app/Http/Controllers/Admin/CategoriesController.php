<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CategoriesController extends Controller
{
    public function index(Request $request){

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:50'], //строка поиска
        ]);

        $query = Category::query();

        // $collections = Category::with('collections');//->find(2);
        // dd($collections->find(1));

        // $collection = Collection::with('categories')->find(2);
        // dd($collection);

        // ->where('published', true);
        // ->whereNotNull('published_at');
        // $qqq = Collection::find(10);
        // dd($qqq);

        if($search = $validated['search'] ?? null){
            $query->where('title', 'like', "%{$search}%");
        }


        $categories = $query->latest('published_at')->paginate(12);

        


        return view('admin.categories.index', compact('categories'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        // 
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Category::validationCreate($request);

        
        alert(__('Сохранено'));
        return redirect()->route('admin.categories.index');
    }




     // редактирование -> страница формы
    public function edit(Request $request, $category_id)
    {
        // $this->hasMany(Collection::class);
        // dd();
        // Category::collections();

        $category = Category::query()->findOrFail($category_id); //позволяет получить конкретную запись из базы по ее id, а если ничего не найдет то вернет 404
        

        // так можно получить коллекции привязанные к категории
        // dd($category->collections);
    
        return view('admin.categories.edit', compact('category' /*, 'films'*/));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        Category::validationCreate($request, $category);

        // получаем id фильмов связь с которыми нужно удалить и удаляем связь
        $deleteElemsId = $request->delete_elems;
        $deleteElemsId = explode(',', $deleteElemsId); //превращаем строку в массив
        foreach($deleteElemsId as $elem){
            // dump((int)$elem);
            // // $records = DB::table('collections')->where('category_id', (int)$elem)->get();
            // dd($records);
            DB::table('collections')->where('id', (int)$elem)->update(['category_id' => null]);
        }

        alert(__('Сохранено'));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request){

        // dd($request);
        $category = Category::find($request->field_delete_id);
        // удаляем данные из базы
        $category->delete();
        alert(__('Категория удалена'));
        
        return redirect()->route('admin.categories.index');
    }


    public function show($category_slug){
        // dump($collection_slug);
        // dd($category_slug);

        // получаем элемент по slug
        $category = Category::where('slug', $category_slug)->first();
        // dd($category->collections);
        // dd($category);
        // $collections = $query->latest('published_at')->paginate(12);

        // $order = $category->collections;
        // // Преобразуем массив в коллекцию
        // $sortOrder = collect($category->collections);
        // // Сортируем фильмы по индексу сортировки
        // $sortedElems = $order->sortBy(function ($elem) use ($sortOrder) {
        //     // Если id нет в массиве или индекс сортировки пустой, ставим самый большой индекс
        //     return (!empty($sortOrder[$elem->id])) ? $sortOrder[$elem->id] : PHP_INT_MAX;
        // }); 

        // dd($sortedElems);

    // @foreach ($sortedElems as $elem)


        $collectionsItems = $category->collections()->where('published', 1)->paginate(12);
        // dump($collectionsItems);
        // $category = Category::find($collection->category_id);
        // $category = Category::find($collection->category_id);
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
        return view('categories.show', compact('collectionsItems', 'category'));

    }
}
