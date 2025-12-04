<?php

namespace App\Services;

use App\Services\ImageProcessor;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Model;



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
                $paths = $processor->processImage($file, $this->storageFolder, [[800, 440], [300, 200]]);

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