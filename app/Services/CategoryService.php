<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }


    public function createCategory(Request $request): Category
    {
        $data = $this->prepareData($request);
        $category = Category::create($data);
        return $category;
    }


    public function updateCategory(Request $request, Category $category): Category
    {
        $data = $this->prepareData($request, $category);
        // получаем id фильмов связь с которыми нужно удалить и удаляем связь
        $deleteElemsId = $request->delete_elems;
        $deleteElemsId = explode(',', $deleteElemsId); //превращаем строку в массив
        foreach($deleteElemsId as $elem){
            DB::table('collections')->where('id', (int)$elem)->update(['category_id' => null]);
        }

        $category->update($data);
        return $category;
    }



    private function prepareData(Request $request, ?Category $category = null): array
    {
        $validated = $request->validated();

        if(isset($validated['sort_collections'])){
            $sort_collections = json_decode($validated['sort_collections']);
        }

        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
            // 'published' => $validated['published'] ?? false, // false значение по умолчанию
            'sort_collections' => $sort_collections ?? null,
        ];
    }




}