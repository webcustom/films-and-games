<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\CategoryService;
use App\Http\Requests\CategoryRequest;

class CategoriesController extends Controller
{
    public function index(Request $request){
        $query = Category::query();
        
        $query->when($request->search, function($q) use ($request) {
            $q->where('title', 'like', "%{$request->search}%");
        });

        // if($search = $validated['search'] ?? null){
        //     $query->where('title', 'like', "%{$search}%");
        // }
        $categories = $query->latest('published_at')->paginate(12);
        return view('admin.categories.index', compact('categories'));
    }


    public function create(){
        // 
        return view('admin.categories.create');
    }



    public function store(CategoryRequest $request, CategoryService $categoryService)
    {
        $categoryService->createCategory($request);
        alert(__('Сохранено'));
        return redirect()->route('admin.categories.index');
    }



     // редактирование -> страница формы
    public function edit(Request $request, $category_id)
    {
        $category = Category::query()->findOrFail($category_id); //позволяет получить конкретную запись из базы по ее id, а если ничего не найдет то вернет 404
        // так можно получить коллекции привязанные к категории
        // dd($category->collections);
        return view('admin.categories.edit', compact('category' /*, 'films'*/));
    }


    public function update(CategoryRequest $request, Category $category, CategoryService $categoryService)
    {
        $categoryService->updateCategory($request, $category);
        alert(__('Сохранено'));
        return redirect()->back();
    }


    public function delete(Request $request){
        $category = Category::find($request->field_delete_id);
        // удаляем данные из базы
        $category->delete();
        alert(__('Категория удалена'));
        return redirect()->route('admin.categories.index');
    }


    public function show($category_slug){
        $category = Category::where('slug', $category_slug)->first();
        $collectionsItems = $category->collections()->where('published', 1)->paginate(12);
        return view('categories.show', compact('collectionsItems', 'category'));

    }
}
