<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Отображение главной страницы с коллекциями
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Используем кешированные опубликованные коллекции (TTL: 30 минут)
        $collections = Collection::getCachedPublished(19, 1800);

        // Проверяем наличие коллекций
        if ($collections->isEmpty()) {
            $collections = collect(); // Пустая коллекция для view
        }

        // Оптимизированная загрузка категорий с кешированием: только с опубликованными коллекциями
        $categoryIds = $collections->pluck('category_id')->unique()->filter();
        
        if ($categoryIds->isNotEmpty()) {
            // Получаем категории с кеша, но фильтруем только нужные
            $allCategories = Category::getCachedAll(3600); // TTL: 1 час
            $categories = $allCategories->whereIn('id', $categoryIds)->values();
        } else {
            $categories = collect();
        }

        return view('home.index', compact('collections', 'categories'));
    }
}
