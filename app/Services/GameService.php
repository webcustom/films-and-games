<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Collection;
use App\Services\FileService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GameService
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function createGame(Request $request): Game
    {
        $data = $this->prepareData($request);
        $game = Game::create($data);
        $this->syncCollections($game, $request);
        return $game;
    }

    public function updateGame(Request $request, Game $game): Game
    {
        $data = $this->prepareData($request, $game);
        $game->update($data);
        $this->syncCollections($game, $request);
        return $game;
    }


    public function deleteGame(Game $game): void
    {

        DB::table('collection_game')->where('game_id', $game->id)->delete();
        FileService::deleteFiles([$game->img_medium, $game->img_thumbnail]);

        if ($game->additional_imgs) {
            foreach (json_decode($game->additional_imgs) as $img) {
                // $this->deleteFiles([$img->image->medium, $img->image->thumbnail]);
                FileService::deleteFiles([$img->image->medium, $img->image->thumbnail]);
            }
        }

        $game->delete();
    }

    private function prepareData(Request $request, ?Game $game = null): array
    {
        $validated = $request->validated();

        // $cast = isset($validated['cast'])
        //     ? array_map('trim', explode(',', $validated['cast']))
        //     : null;

        // подключаем сервис картинок
        // подключаем сервис картинок
        $imageService = new ImageService('games'); //games - директория куда загружаются файлы
        $mainImage = $imageService->handleMainImage($request, $game);
        $additional = $imageService->handleAdditionalImages($request, $game);

        return [
            'title' => $validated['title'],
            'slug' =>  $validated['slug'],
            'img_medium' => $mainImage['img_medium'],
            'img_thumbnail' => $mainImage['img_thumbnail'],
            'additional_imgs' => $additional,
            'iframe_video' => $validated['iframe_video'] ?? null,
            'description' => $validated['description'] ?? null,
            'release' => $validated['release'] ?? null,
            'genre' => $validated['genre'] ?? null,
            'budget' => $validated['budget'] ?? null, 
            'maker' => $validated['maker'] ?? null,
            'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
            'platforms' => $validated['platforms'] ?? null,
        ];
    }

    private function syncCollections(Game $game, Request $request): void
    {
        // если в $request есть поле collections
        if ($request->filled('collections')) {
            // получаем id коллекции, у которой значение поля 'slug' совпадает с одним из значений в массиве $request->collections
            $collectionIds = Collection::whereIn('slug', $request->collections)->pluck('id')->all();
            // Здесь мы используем отношение collections() модели фильма для синхронизации коллекций. 
            // Метод sync() синхронизирует коллекцию с заданными идентификаторами. В результате, коллекции фильма будут соответствовать значениям в $collectionIds
            $game->collections()->sync($collectionIds);
        }
    }

}