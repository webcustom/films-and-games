<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function index(){


        //2 способа получить данные из сессии
        // $foo = session()->get('foo'); 
        // $foo = session('foo');
        // $all = session()->all(); //получить все данные из сессии

        // session()->has('foo'); //вернет true если данные foo есть в сессии

        // if($test = session('test')){ //если в сессии есть test тогда сразу помещаем ее в переменную 
        //     dd($test);
        // }

        // dd($all);
        // if (Auth::check()) {

        //     dd('aaaaa');
        //     // Пользователь аутентифицирован
        //     Auth::logout();
        // }
        return view('login.index');
        // альтернативные способы вывода, через ->make редко используются
        // return app('view')->make('login.index');
        // return view()->make('login.index');
        // return View:make('login.index');
        // return view('admin.index');


    }
    public function store(Request $request){
        

        $validatedData = $request->validate([
            'email' => ['required', 'string', 'email'], // email - проверяет формат строки, exists:users,email - проверяет есть ли значение поля email в таблице users (должен обязательно быть)
            'password' => ['required', 'string'], // confirmed - проверяет что в запросе еще должно быть поле password_confirmation и значение в нем должно совпадать со значением в исходном поле password, так же есть множество дополнительных параметров для пароля
        ]);

        // отправляем наши провалидированные данные в фасад Auth и он проверяет есть ли такой пользователь в таблице users
        if(!Auth::attempt($validatedData)){
            // если аутентификация не удалась
            return back()
                ->withInput() //сохранить введенные данные в input
                ->withErrors([
                    'email' => 'Пользователь с таким именем и паролем не зарегистрирован.'
                ]);
        }

        alert(__('Добро пожаловать')); //добавляем сессию alert смотреть helpers.php
        // если аутентификация удалась

        return redirect()->route('admin.index');
        
    }

    // выход из аккаунта
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // return redirect()->route('login.index');
        return redirect()->route('home');

    }
}
