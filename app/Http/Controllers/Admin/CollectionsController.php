<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Services\CollectionService;
use App\Http\Requests\CollectionRequest;

class CollectionsController extends Controller
{


    public function index(Request $request)
    {
        $query = Collection::with(['films', 'games']);
        
        $query->when($request->search, function($q) use ($request) {
            $q->where('title', 'like', "%{$request->search}%");
        });
        
        $query->when($request->filled('selectionByCat'), function($q) use ($request) {
            $q->where('category_id', $request->selectionByCat);
        });
        
        // При поиске или фильтрации не используем кеш, так как результаты могут отличаться
        // Используем кеш только для стандартного запроса
        $shouldCache = !$request->has('search') && !$request->filled('selectionByCat');
        
        if ($shouldCache) {
            // Можно добавить кеширование для базового списка, но с пагинацией это сложнее
            // Для простоты оставляем без кеша в админ-панели, так как данные могут часто меняться
        }
        
        $collections = $query->latest('published_at')->paginate(20);
        $collections->appends(request()->query());
        
        // Используем кешированные категории (TTL: 1 час)
        $categories = Category::getCachedAll(3600);
        
        return view('admin.collections.index', compact('collections', 'categories'));
    }




    // выводит страницы создания подборки
    public function create()
    {
        // Используем кешированные категории (TTL: 1 час)
        $categories = Category::getCachedAll(3600);
        return view('admin.collections.create', compact('categories'));
    }




    public function store(CollectionRequest $request, CollectionService $collectionService)
    {
        $collectionService->createCollection($request);
        alert(__('Сохранено'));
        return redirect()->route('admin.collections.index');
    }



     // редактирование -> страница формы
    public function edit(Request $request, $collection_id)
    {
        $collection = Collection::query()->findOrFail($collection_id); //позволяет получить конкретную запись из базы по ее id, а если ничего не найдет то вернет 404
        
        // Используем кешированные категории (TTL: 1 час)
        $categories = Category::getCachedAll(3600);
        
        //передаем наши коллекции и категории
        return view('admin.collections.edit', compact('collection', 'categories'));
    }



    // редактирование -> сохранение изменений
    public function update(CollectionRequest $request, Collection $collection, CollectionService $collectionService)
    {
        $collectionService->updateCollection($request, $collection);
        alert(__('Сохранено'));
        return redirect()->back();
    }



    public function delete(Request $request, CollectionService $collectionService)
    {

        $collection = Collection::find($request->field_delete_id); //получаем нашу коллекцию

        $collectionService->deleteCollection($collection);
        alert(__('Подборка удалена'));
        return redirect()->route('admin.collections.index');
    }



    public function show($collection_slug)
    {
        // Оптимизированный запрос: загружаем коллекцию с relations через eager loading
        // чтобы избежать N+1 запросов при обращении к films/games в view
        $collection = Collection::where('slug', $collection_slug)
            ->with(['films', 'games', 'category']) // Eager loading для избежания N+1 запросов
            ->first();

        // Проверяем существование коллекции
        if (!$collection) {
            abort(404, 'Коллекция не найдена');
        }

        // Получаем категорию из отношения (уже загружена через eager loading)
        $category = $collection->category;

        // Проверяем наличие категории
        if (!$category) {
            abort(404, 'Категория для коллекции не найдена');
        }

        // Преобразуем строку в объект Carbon
        $date = Carbon::parse($collection->published_at);
        $formattedDate = $date->format('d m Y'); // Например, "26 09 2023"
        
        return view('collections.show', compact('collection', 'category', 'formattedDate'));
    }

}
