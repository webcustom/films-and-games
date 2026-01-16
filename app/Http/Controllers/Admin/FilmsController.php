<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilmRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Film;
use App\Services\FilmService;


class FilmsController extends Controller
{
    public function index(Request $request)
    {
        // Оптимизированный запрос: используем withCount вместо сложного orderByRaw с EXISTS
        // Это намного быстрее, так как выполняется один JOIN вместо подзапроса для каждой записи
        $films = Film::with('collections')
            ->withCount('collections') // Подсчитываем количество связанных коллекций
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderBy('collections_count', 'asc') // Фильмы без коллекций (0) будут в конце
            ->latest('published_at') // Затем сортируем по дате публикации
            ->paginate(48);

        return view('admin.films.index', compact('films'));
    }



    public function create()
    {
        // Используем кешированную категорию (TTL: 1 час)
        $category = Category::getCachedBySlug('filmy', 3600);
        
        if (!$category) {
            abort(404, 'Категория "filmy" не найдена');
        }

        $collections = $category->collections;

        return view('admin.films.create', compact('collections'));
    }



    // создание фильма
    public function store(FilmRequest $request, FilmService $filmService)
    {
        $filmService->createFilm($request);
        alert(__('Сохранено'));
        return redirect()->route('admin.films.index');
    }


    
    // редактирование -> страница формы
    public function edit(Film $film)
    {
        // Используем кешированную категорию (TTL: 1 час)
        $category = Category::getCachedBySlug('filmy', 3600);
        
        if (!$category) {
            abort(404, 'Категория "filmy" не найдена');
        }

        $collections = $category->collections;

        return view('admin.films.edit', compact('film', 'collections'));
    }




    // редактирование -> сохранение изменений
    public function update(FilmRequest $request, Film $film, FilmService $filmService)
    {
        $filmService->updateFilm($request, $film);
        alert(__('Сохранено'));

        return redirect()->back();
    }




    public function delete(Request $request, FilmService $filmService)
    {
        $ids = explode(",", $request->field_delete_id);
        $films = Film::findMany($ids);

        foreach ($films as $film) {
            $filmService->deleteFilm($film);
        }

        alert(count($ids) > 1 ? __('Фильмы удалены') : __('Фильм удален'));
        return redirect()->route('admin.films.index');
    }
}