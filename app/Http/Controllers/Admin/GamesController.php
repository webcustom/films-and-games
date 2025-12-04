<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Support\Facades\DB;

class GamesController extends Controller
{
    public function __invoke(Request $request)
    {
        return 'Games__invoke';
    }
    

    public function index(Request $request)
    {
        $games = Game::with('collections')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderByRaw("
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM collection_game 
                        WHERE collection_game.game_id = games.id
                    ) THEN 0 
                    ELSE 1 
                END
            ") //элементы у которых отсутствует подборка выводим в конце
            ->latest('published_at')
            ->paginate(48);

        return view('admin.games.index', compact('games'));
    }



    public function create()
    {
        $category = Category::where('slug', 'igry')->first();
        $collections = $category->collections;

        return view('admin.games.create', compact('collections'));
    }




    // создание фильма
    public function store(GameRequest $request, GameService $gameService)
    {
        $gameService->createGame($request);
        alert(__('Сохранено'));
        return redirect()->route('admin.games.index');
    }
    


    // редактирование -> страница формы
    public function edit(Game $game)
    {
        $category = Category::where('slug', 'igry')->first();
        $collections = $category->collections;

        return view('admin.games.edit', compact('game', 'collections'));
    }




    // редактирование -> сохранение изменений
    public function update(GameRequest $request, Game $game, GameService $gameService)
    {
        $gameService->updateGame($request, $game);
        alert(__('Сохранено'));

        return redirect()->back();
    }




    public function delete(Request $request, GameService $gameService)
    {
        $ids = explode(",", $request->field_delete_id);
        $games = Game::findMany($ids);

        foreach ($games as $game) {
            $gameService->deleteGame($game);
        }

        alert(count($ids) > 1 ? __('Игры удалены') : __('Игра удалена'));
        return redirect()->route('admin.games.index');
    }

}
