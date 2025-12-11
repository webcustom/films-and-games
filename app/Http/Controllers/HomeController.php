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


        $collections = $query->where('published', 1)->whereNotNull('category_id')->latest('published_at')->limit(19)->get();

        // $collections = $query->latest('published_at')->where('published', 1)->where('category_id', !null)->paginate(19);

        $categories = Category::all();


        return view('home.index', compact('collections', 'categories'));

    }

}
