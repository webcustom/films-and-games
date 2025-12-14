<?php

namespace App\Services;


use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CollectionService
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }


    public function createCollection(Request $request): Collection
    {
        $data = $this->prepareData($request);
        $collection = Collection::create($data);
        return $collection;
    }


    public function updateCollection(Request $request, Collection $collection): Collection
    {
        $data = $this->prepareData($request, $collection);
        // получаем id фильмов связь с которыми нужно удалить и удаляем связь
        $deleteElemsId = $request->delete_elems;
        $deleteElemsId = explode(',', $deleteElemsId); //превращаем строку в массив
        
        $category = Category::find($collection->category_id);
        // $slug = $category?->slug;
        // dd($collection);

        if(isset($category->slug)){
            foreach($deleteElemsId as $elem){
                if($category->slug === 'filmy'){
                    DB::table('collection_film')->where('film_id', (int)$elem)->delete();
                }
                if($category->slug === 'igry'){
                    DB::table('collection_game')->where('game_id', (int)$elem)->delete();
                }
            }
        }

        $collection->update($data);
        return $collection;
    }


    public function deleteCollection(Collection $collection): void
    {
        FileService::deleteFiles([$collection->img_medium, $collection->img_thumbnail]);
        $category = Category::find($collection->category_id);

        // удаляем связи в таблицах collection_film и collection_game
        if(isset($category->slug)){
            if($category->slug === 'filmy'){
                DB::table('collection_film')->where('collection_id', $collection->id)->delete();
                // $collection->films()->detach(); //альтернативный способ
            }
            if($category->slug === 'igry'){
                DB::table('collection_game')->where('collection_id', $collection->id)->delete();
                // $collection->games()->detach();
            }
        }
        $collection->delete();
    }



    private function prepareData(Request $request, ?Collection $collection = null): array
    {
        $validated = $request->validated();

        // подключаем сервис картинок
        $imageService = new ImageService('collections'); //collections - директория куда загружаются файлы
        $mainImage = $imageService->handleMainImage($request, $collection);

        if(isset($validated['sort_elems'])){
            $sort_elems = json_decode($validated['sort_elems']);
        }

        
        return [
            'title' => $validated['title'],
            'title_seo' => $validated['title_seo'],
            'slug' => $validated['slug'],
            'img_medium' => $mainImage['img_medium'] ?? null, //$validated['img'],
            'img_thumbnail' => $mainImage['img_thumbnail'] ?? null,
            'description' => $validated['description'] ?? null,
            'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
            'published' => $validated['published'] ?? false, // false значение по умолчанию
            'sort_elems' => $sort_elems ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ];
    }




}