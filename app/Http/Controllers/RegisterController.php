<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

use Illuminate\Auth\Events\Registered; //для проверки аутентификации

class RegisterController extends Controller
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

        return view('register.index');
        // альтернативные способы вывода, через ->make редко используются
        // return app('view')->make('login.index');
        // return view()->make('login.index');
        // return View:make('login.index');


    }
    public function store(Request $request){
        
        // dd($request->all());


        // $validator = Validator::make($request->all(), [
        //     'name' => 'requered|string|max:50',
        //     'email' => 'required|string|email|unique:users', // email - проверяет формат строки, exists:users,email - проверяет есть ли значение поля email в таблице users (должен обязательно быть)
        //     'password' => 'required|string|min:7|confirmed', // confirmed - проверяет что в запросе еще должно быть поле password_confirmation и значение в нем должно совпадать со значением в исходном поле password, так же есть множество дополнительных параметров для пароля
        //     'password_confirmation' => 'required|string|min:7|confirmed',
        // ]);

        // // $validated = validate($request->all(), [
        // //     'name' => 'requered|string|max:50',
        // //     'email' => 'required|string|email|unique:users', // email - проверяет формат строки, exists:users,email - проверяет есть ли значение поля email в таблице users (должен обязательно быть)
        // //     'password' => 'required|string|min:7|confirmed', // confirmed - проверяет что в запросе еще должно быть поле password_confirmation и значение в нем должно совпадать со значением в исходном поле password, так же есть множество дополнительных параметров для пароля
        // //     'password_confirmation' => 'required|string|min:7|confirmed',


        // //     // 'name' => ['requered', 'string', 'max:50'],
        // //     // 'email' => ['required', 'string', 'email', 'exists:users,email'], // email - проверяет формат строки, exists:users,email - проверяет есть ли значение поля email в таблице users (должен обязательно быть)
        // //     // 'password' => ['required', 'string', 'min:7', 'confirmed'], // confirmed - проверяет что в запросе еще должно быть поле password_confirmation и значение в нем должно совпадать со значением в исходном поле password, так же есть множество дополнительных параметров для пароля
        // //     // 'password_confirmation' => ['required', 'string', 'min:7', 'confirmed'],
        // // ]);

        // $validated = $validator->validated();

        // dd($validated);



        // $user = User::create([
        //     'name' => $validated['name'],
        //     'email' => $validated['email'],
        //     'password' => bcrypt($validated['password']),
        // ]);





        $validatedData = $request->validate([
            // 'name' => 'required|string|max:50',
            // 'email' => 'required|string|email|unique:users',
            // 'password' => 'required|string|min:7|confirmed',
            // 'password_confirmation' => 'required|string|min:7|confirmed',
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'unique:users'], // email - проверяет формат строки, exists:users,email - проверяет есть ли значение поля email в таблице users (должен обязательно быть)
            'password' => ['required', 'string', 'min:7', 'confirmed'], // confirmed - проверяет что в запросе еще должно быть поле password_confirmation и значение в нем должно совпадать со значением в исходном поле password, так же есть множество дополнительных параметров для пароля
            // 'password_confirmation' => ['required', 'string', 'min:7', 'confirmed'],
        ]);
        
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        event(new Registered($user)); //для аутентификации почты

        Auth::login($user); //утентифицируем пользователя

        

        // dd(Auth::user($user));


        alert(__('Админ зарегистрирован')); //добавляем сессию alert смотреть helpers.php

        // return redirect()->route('admin.films.index');
        return redirect()->route('verification.notice'); //перенаправляем на страницу верификации почты

        
    }
}
