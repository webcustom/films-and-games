<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Redis;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\Auth\Events\Registered; //для проверки аутентификации



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
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'unique:users'], // email - проверяет формат строки, exists:users,email - проверяет есть ли значение поля email в таблице users (должен обязательно быть)
            'password' => ['required', 'string', 'min:7', 'confirmed'], // confirmed - проверяет что в запросе еще должно быть поле password_confirmation и значение в нем должно совпадать со значением в исходном поле password, так же есть множество дополнительных параметров для пароля
            // 'password_confirmation' => ['required', 'string', 'min:7', 'confirmed'],
            'verified' => ['nullable', 'boolean'],
        ]);

        // Генерируем уникальный токен для верификации
        $verificationToken = Str::random(60);

        // Сохраняем данные пользователя в сессии
        Session::put('pending_user', [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'token' => $verificationToken,
        ]);
        

        // Отправляем уведомление для верификации электронной почты
        // Здесь вы должны создать метод отправки письма с токеном
        Mail::to($validatedData['email'])->send(new VerifyEmail($verificationToken));


        return redirect()->route('verification.notice')->with('status', 'Пожалуйста, проверьте свою электронную почту для подтверждения регистрации.');

        // return Redirect::route('verification.notice')->with('status', 'Пожалуйста, проверьте свою электронную почту для подтверждения регистрации.');
        // $user = User::create([
        //     'name' => $validatedData['name'],
        //     'email' => $validatedData['email'],
        //     'password' => bcrypt($validatedData['password']),
        // ]);




        // event(new Registered($user)); //для аутентификации почты

        // Auth::login($user); //утентифицируем пользователя

        

        // dd(Auth::user($user));


        // alert(__('Пожалуйста, проверьте свою электронную почту для подтверждения регистрации.')); //добавляем сессию alert смотреть helpers.php

        // return redirect()->route('admin.films.index');
        // return redirect()->route('verification.notice'); //перенаправляем на страницу верификации почты

        
    }


    public function verifyEmail($token){
        // Получаем данные пользователя из сессии
        $userData = Session::get('pending_user');

        // dd($userData);
        // Проверяем, существует ли пользователь и совпадает ли токен
        if (!$userData || $userData['token'] !== $token) {
            return redirect()->route('admin.index')->withErrors(['message' => 'Неверный токен.']);
        }

        // Создаем пользователя в базе данных
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'verified' => true,
        ]);

        // Удаляем данные из сессии
        Session::forget('pending_user');

        // Аутентификация пользователя (если необходимо)
        Auth::login($user);

        return redirect()->route('login.index')->with('status', 'Ваш аккаунт успешно активирован!');
    }


    public function resendVerificationEmail(Request $request)
    {
        // Валидация входящих данных
        // $validatedData = $request->validate([
        //     'email' => 'required|email|exists:users,email',
        // ]);
    
        // Получаем пользователя по email
        // $user = User::where('email', $validatedData['email'])->first();

        $userData = Session::get('pending_user');
        // if(isset($userData)){
            $email = $userData['email'];
            $token = $userData['token'];
        // }
        // dd($email);
    

        // Session::put('pending_user', [
        //     'name' => $validatedData['name'],
        //     'email' => $validatedData['email'],
        //     'password' => bcrypt($validatedData['password']),
        //     'token' => $verificationToken,
        // ]);

        // Проверяем, что email не верифицирован
        if ($email) {
            // Генерируем новый токен для верификации
            // $verificationToken = Str::random(60);
            
            // Обновляем токен в базе данных (добавьте поле verification_token в модель User)
            // $user->verification_token = $verificationToken;
            // $user->save();
    
            // Отправляем письмо с верификацией
            // Mail::to($validatedData['email'])->send(new VerifyEmail($verificationToken));
            Mail::to($email)->send(new VerifyEmail($token));
    
            return back()->with('message', 'Ссылка для подтверждения была отправлена на вашу электронную почту.');
        }
    
        return back()->withErrors(['message' => 'Ваш email уже подтвержден или пользователь не найден.']);
    }
}
