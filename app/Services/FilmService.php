<?php

namespace App\Services;

use App\Models\Film;
use App\Models\Collection;
use App\Services\FileService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FilmService
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function createFilm(Request $request): Film
    {
        $data = $this->prepareData($request);
        $film = Film::create($data);
        $this->syncCollections($film, $request);
        return $film;
    }

    public function updateFilm(Request $request, Film $film): Film
    {
        $data = $this->prepareData($request, $film);
        $film->update($data);
        $this->syncCollections($film, $request);
        return $film;
    }


    public function deleteFilm(Film $film): void
    {
        DB::table('collection_film')->where('film_id', $film->id)->delete();
        FileService::deleteFiles([$film->img_medium, $film->img_thumbnail]);

        if ($film->additional_imgs) {
            foreach (json_decode($film->additional_imgs) as $img) {
                FileService::deleteFiles([$img->image->medium, $img->image->thumbnail]);
            }
        }

        $film->delete();
    }

    private function prepareData(Request $request, ?Film $film = null): array
    {
        $validated = $request->validated();

        $cast = isset($validated['cast'])
            ? array_map('trim', explode(',', $validated['cast']))
            : null;

        // подключаем сервис картинок
        $imageService = new ImageService('films'); //films - директория куда загружаются файлы
        $mainImage = $imageService->handleMainImage($request, $film);
        $additional = $imageService->handleAdditionalImages($request, $film);


        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? null,
            'iframe_video' => $validated['iframe_video'] ?? null,
            'description' => $validated['description'] ?? null,
            'rating_imdb' => $validated['rating_imdb'] ?? null,
            'rating_kinopoisk' => $validated['rating_kinopoisk'] ?? null,
            'release' => $validated['release'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'genre' => $validated['genre'] ?? null,
            'country' => $validated['country'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'fees_usa' => $validated['fees_usa'] ?? null,
            'fees_world' => $validated['fees_world'] ?? null,
            'director' => $validated['director'] ?? null,
            'cast' => $cast,
            'published_at' => new Carbon($validated['published_at'] ?? null),
            'img_medium' => $mainImage['img_medium'],
            'img_thumbnail' => $mainImage['img_thumbnail'],
            'additional_imgs' => $additional,
        ];
    }

    private function syncCollections(Film $film, Request $request): void
    {
        // если в $request есть поле collections
        if ($request->filled('collections')) {
            // получаем id коллекции, у которой значение поля 'slug' совпадает с одним из значений в массиве $request->collections
            $collectionIds = Collection::whereIn('slug', $request->collections)->pluck('id')->all();

        // dd($collectionIds);

            // Здесь мы используем отношение collections() модели фильма для синхронизации коллекций. 
            // Метод sync() синхронизирует коллекцию с заданными идентификаторами. В результате, коллекции фильма будут соответствовать значениям в $collectionIds
            $film->collections()->sync($collectionIds);
        }else{
            // отвязываем все коллекции
            $film->collections()->detach();
        }

    }

}