<?php

namespace App\Services;

use App\Services\ImageProcessor;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;



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
            try {
                $processor = new ImageProcessor();
                $paths = $processor->processImage(
                    $request->file($field),
                    $this->storageFolder,
                    [[800, 600], [300, 200]]
                );

                // Проверяем, что processImage вернул корректные данные
                if ($paths === null || !isset($paths[0]) || !isset($paths[1])) {
                    throw new \RuntimeException('ImageProcessor вернул некорректные данные');
                }

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
            } catch (\InvalidArgumentException $e) {
                // Ошибки валидации - пробрасываем дальше для отображения пользователю
                Log::warning('ImageService: Ошибка валидации изображения', [
                    'field' => $field,
                    'error' => $e->getMessage(),
                    'model' => $model ? get_class($model) : null,
                    'model_id' => $model?->id
                ]);
                throw $e;
            } catch (\Exception $e) {
                // Другие ошибки - логируем и пробрасываем
                Log::error('ImageService: Ошибка при обработке основного изображения', [
                    'field' => $field,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'model' => $model ? get_class($model) : null,
                    'model_id' => $model?->id
                ]);
                throw new \RuntimeException('Не удалось обработать изображение. Пожалуйста, попробуйте другое изображение.', 0, $e);
            }
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
                try {
                    $processor = new ImageProcessor();
                    $paths = $processor->processImage($file, $this->storageFolder, [[800, 440], [300, 200]]);

                    // Проверяем, что processImage вернул корректные данные
                    if ($paths === null || !isset($paths[0]) || !isset($paths[1])) {
                        Log::warning('ImageService: ImageProcessor вернул некорректные данные для дополнительного изображения', [
                            'key' => $key,
                            'file' => $file->getClientOriginalName()
                        ]);
                        continue; // Пропускаем это изображение, продолжаем обработку остальных
                    }

                    $arr_imgs[$key] = [
                        'image' => ['medium' => $paths[0], 'thumbnail' => $paths[1]],
                        'text' => $request->input("{$field}_text.{$key}") ?? null,
                        'sort' => $request->input("{$field}_sort.{$key}") ?? null,
                    ];
                } catch (\InvalidArgumentException $e) {
                    // Ошибки валидации - логируем и пропускаем это изображение
                    Log::warning('ImageService: Ошибка валидации дополнительного изображения', [
                        'key' => $key,
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ]);
                    continue; // Пропускаем это изображение, продолжаем обработку остальных
                } catch (\Exception $e) {
                    // Другие ошибки - логируем и пропускаем это изображение
                    Log::error('ImageService: Ошибка при обработке дополнительного изображения', [
                        'key' => $key,
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue; // Пропускаем это изображение, продолжаем обработку остальных
                }
            }
        }

        $arr_imgs = collect($arr_imgs)
            ->sortBy(fn($item) => $item['sort'] ?? INF)
            ->values()
            ->toArray();

        return json_encode($arr_imgs);
    }
}