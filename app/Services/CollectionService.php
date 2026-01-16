<?php

namespace App\Services;


use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        
        // Получаем id элементов, связь с которыми нужно удалить
        $deleteElemsId = $request->delete_elems;
        
        if ($deleteElemsId) {
            $deleteElemsId = explode(',', $deleteElemsId); // Превращаем строку в массив
            
            // Удаляем связи элементов с коллекцией в зависимости от типа категории
            $this->detachElementsFromCollection($collection, $deleteElemsId);
        }

        $collection->update($data);
        
        // Очищаем кеш коллекций после обновления
        Collection::clearCollectionCache($collection);
        
        return $collection;
    }


    public function deleteCollection(Collection $collection): void
    {
        FileService::deleteFiles([$collection->img_medium, $collection->img_thumbnail]);
        
        // Удаляем все связи коллекции с элементами (фильмами или играми) в зависимости от категории
        $this->detachAllElementsFromCollection($collection);
        
        $collection->delete();
        
        // Очищаем кеш коллекций после удаления
        Collection::clearCollectionCache($collection);
    }



    /**
     * Определяет тип связи коллекции на основе категории
     * 
     * @param Collection $collection
     * @return string|null Возвращает 'films', 'games' или null
     */
    private function getCollectionRelationType(Collection $collection): ?string
    {
        if (!$collection->category_id) {
            return null;
        }

        // Используем кешированную категорию для лучшей производительности
        $category = Category::getCachedById($collection->category_id, 3600);
        
        if (!$category) {
            return null;
        }

        return match ($category->slug) {
            'filmy' => 'films',
            'igry' => 'games',
            default => null,
        };
    }

    /**
     * Удаляет конкретные элементы из связи с коллекцией
     * 
     * @param Collection $collection
     * @param array $elementIds Массив ID элементов для удаления
     */
    private function detachElementsFromCollection(Collection $collection, array $elementIds): void
    {
        $relationType = $this->getCollectionRelationType($collection);
        
        if (!$relationType) {
            Log::warning('CollectionService: Не удалось определить тип связи коллекции', [
                'collection_id' => $collection->id,
                'category_id' => $collection->category_id
            ]);
            return;
        }

        // Используем Eloquent отношения вместо прямых SQL-запросов
        if ($relationType === 'films') {
            $collection->films()->detach($elementIds);
        } elseif ($relationType === 'games') {
            $collection->games()->detach($elementIds);
        }
    }

    /**
     * Удаляет все элементы из связи с коллекцией
     * 
     * @param Collection $collection
     */
    private function detachAllElementsFromCollection(Collection $collection): void
    {
        $relationType = $this->getCollectionRelationType($collection);
        
        if (!$relationType) {
            Log::warning('CollectionService: Не удалось определить тип связи коллекции при удалении', [
                'collection_id' => $collection->id,
                'category_id' => $collection->category_id
            ]);
            return;
        }

        // Используем Eloquent отношения вместо прямых SQL-запросов
        if ($relationType === 'films') {
            $collection->films()->detach(); // Удаляет все связи
        } elseif ($relationType === 'games') {
            $collection->games()->detach(); // Удаляет все связи
        }
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