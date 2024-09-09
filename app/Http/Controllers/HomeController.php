<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index(Request $request){
        $query = Collection::with(['films', 'games']);
        // dd($query);

        $collections = $query->latest('published_at')->where('published', 1)->paginate(19);
        // dd($collections);


        // $publishedItems = $collections->filter(function ($collection) {
        //     return $collection->published;
        // });

        // $sortedItems = $collections->sortByDesc('published_at');




        // $collections = $query->latest('published_at')->paginate(19);
        $categories = Category::all();

        // dd($collections);

        return view('home.index', compact('collections', 'categories'));

    }

    // public function show($collection_slug){
    //     dd($collection_slug);
    // }
}
