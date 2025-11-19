<?php

namespace App\Services;

use App\Services\ImageProcessor;
use App\Models\Film;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Model;

// class ImageService
// {
//     // сохранение-удаление основного изображения
//     public function handleMainImage($request, ?Film $film = null): array
//     {

//         $deleteRequested = (bool) $request->input('delete_img');
        
//         // если изображение приходит в запросе
//         if ($request->hasFile('img')) {
//             $processor = new ImageProcessor();
//             $paths = $processor->processImage(
//                 $request->file('img'),
//                 'films',
//                 [[800, 600], [300, 200]]
//             );

//             // если заменили изображение на новое, то удаляем старое
//             if($film && $deleteRequested){
//                 FileService::deleteFiles([$film->img_medium, $film->img_thumbnail]);
//             }

//             return [
//                 'img_medium' => $paths[0],
//                 'img_thumbnail' => $paths[1],
//             ];
//         }

//         if ($film && !$request->input('delete_img')) {
//             return [
//                 'img_medium' => $film->img_medium,
//                 'img_thumbnail' => $film->img_thumbnail,
//             ];
//         }

//         if($film && $deleteRequested){
//             FileService::deleteFiles([$film->img_medium, $film->img_thumbnail]);
//         }

//         return [
//             'img_medium' => null,
//             'img_thumbnail' => null,
//         ];
//     }

//     // сохранение-удаление доп. изображений
//     public function handleAdditionalImages($request, ?Film $film = null): ?string
//     {

//         $arr_imgs = [];


//         // старые изображения (уже загруженные к элементу)
//         if ($film && $film->additional_imgs) {
//             $old_imgs = json_decode($film->additional_imgs, true);
//             foreach ($old_imgs as $key => $img) {
//                 $arr_imgs[$key] = [
//                     'image' => $img['image'],
//                     'text' => $request->additional_imgs_text[$key] ?? $img['text'] ?? null,
//                     'sort' => $request->additional_imgs_sort[$key] ?? $img['sort'] ?? null,
//                 ];
//             }
//         }


//         // удаление выбранных
//         if ($request->filled('delete_additional_img')) {
//             $newImgsArr = [];
//             $toDelete = explode(',', $request->delete_additional_img);
//             foreach ($toDelete as $key) {
//                 $newImgsArr[] = $arr_imgs[(int)$key]['image']['medium'];
//                 $newImgsArr[] = $arr_imgs[(int)$key]['image']['thumbnail'];
//                 unset($arr_imgs[(int)$key]);
//             }
//             FileService::deleteFiles($newImgsArr);
//         }


//         // новые или заменённые
//         if ($request->hasFile('additional_imgs')) {
//             foreach ($request->file('additional_imgs') as $key => $file) {
//                 $processor = new ImageProcessor();
//                 $paths = $processor->processImage($file, 'films', [[800, 600], [300, 200]]);

//                 $arr_imgs[$key] = [
//                     'image' => ['medium' => $paths[0], 'thumbnail' => $paths[1]],
//                     'text' => $request->additional_imgs_text[$key] ?? null,
//                     'sort' => $request->additional_imgs_sort[$key] ?? null,
//                 ];
//             }
//         }


//         // сортировка
//         $arr_imgs = collect($arr_imgs)
//             ->sortBy(fn($item) => $item['sort'] ?? INF)
//             ->values()
//             ->toArray();

//         return json_encode($arr_imgs);
//     }
// }




class ImageService
{
    protected string $storageFolder;

    public function __construct(string $storageFolder = 'uploads')
    {
        $this->storageFolder = $storageFolder;
    }

    // сохранение-удаление основного изображения
    public function handleMainImage($request, ?Model $model = null, string $field = 'img'): array
    {
        $deleteRequested = (bool) $request->input("delete_img");

        if ($request->hasFile($field)) {
            $processor = new ImageProcessor();
            $paths = $processor->processImage(
                $request->file($field),
                $this->storageFolder,
                [[800, 600], [300, 200]]
            );

            if ($model && $deleteRequested) {
                FileService::deleteFiles([
                    $model->getAttribute("img_medium"),
                    $model->getAttribute("img_thumbnail"),
                ]);
            }

            return [
                "img_medium" => $paths[0],
                "img_thumbnail" => $paths[1],
            ];
        }

        if ($model && !$deleteRequested) {
            return [
                "img_medium" => $model->getAttribute("img_medium"),
                "img_thumbnail" => $model->getAttribute("img_thumbnail"),
            ];
        }

        if ($model && $deleteRequested) {
            FileService::deleteFiles([
                $model->getAttribute("img_medium"),
                $model->getAttribute("img_thumbnail"),
            ]);
        }

        return [
            "img_medium" => null,
            "img_thumbnail" => null,
        ];
    }




    // сохранение-удаление дополнительных изображений
    public function handleAdditionalImages($request, ?Model $model = null, string $field = 'additional_imgs'): ?string
    {
        $arr_imgs = [];

        if ($model && $model->getAttribute($field)) {
            $old_imgs = json_decode($model->getAttribute($field), true);
            foreach ($old_imgs as $key => $img) {
                $arr_imgs[$key] = [
                    'image' => $img['image'],
                    'text' => $request->input("{$field}_text.{$key}") ?? $img['text'] ?? null,
                    'sort' => $request->input("{$field}_sort.{$key}") ?? $img['sort'] ?? null,
                ];
            }
        }

        if ($request->filled("delete_additional_img")) {
            $newImgsArr = [];
            // dd($request);
            $toDelete = explode(',', $request->input("delete_additional_img"));
            foreach ($toDelete as $key) {
                $newImgsArr[] = $arr_imgs[(int)$key]['image']['medium'];
                $newImgsArr[] = $arr_imgs[(int)$key]['image']['thumbnail'];
                unset($arr_imgs[(int)$key]);
            }
            FileService::deleteFiles($newImgsArr);
        }




        if ($request->hasFile($field)) {
            foreach ($request->file($field) as $key => $file) {
                $processor = new ImageProcessor();
                $paths = $processor->processImage($file, $this->storageFolder, [[800, 600], [300, 200]]);

                $arr_imgs[$key] = [
                    'image' => ['medium' => $paths[0], 'thumbnail' => $paths[1]],
                    'text' => $request->input("{$field}_text.{$key}") ?? null,
                    'sort' => $request->input("{$field}_sort.{$key}") ?? null,
                ];
            }
        }

        $arr_imgs = collect($arr_imgs)
            ->sortBy(fn($item) => $item['sort'] ?? INF)
            ->values()
            ->toArray();

        return json_encode($arr_imgs);
    }
}