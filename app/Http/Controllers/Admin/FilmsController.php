<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilmRequest;
use App\Models\Category;
use App\Models\Collection;
// use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;


use App\Models\Film;
use App\Services\FilmService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FilmsController extends Controller
{
    public function index(Request $request)
    {
        $films = Film::with('collections')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest('published_at')
            ->paginate(48);

        return view('admin.films.index', compact('films'));
    }



    public function create()
    {
        $category = Category::where('slug', 'films')->first();
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
        $category = Category::where('slug', 'films')->first();
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