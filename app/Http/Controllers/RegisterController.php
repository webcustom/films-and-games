<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\PendingUser;
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

    // сюда данные идут с фррмы регистрации
    public function store(Request $request){

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
        // Session::put('pending_user', [
        //     'name' => $validatedData['name'],
        //     'email' => $validatedData['email'],
        //     'password' => bcrypt($validatedData['password']),
        //     'token' => $verificationToken,
        // ]);

        $pendingUserDB = PendingUser::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'token' => $verificationToken,
        ]);
        

        // $userData2 = Session::get('pending_user');
        // dump($userData2);


        // Отправляем уведомление для верификации электронной почты
        // Здесь вы должны создать метод отправки письма с токеном
        // dd($pendingUserDB->id);
        Mail::to($validatedData['email'])->send(new VerifyEmail($verificationToken, $pendingUserDB->id));

        return redirect()->route('verification.notice', ['pendingUserId' => $pendingUserDB->id])->with('status', 'Пожалуйста, проверьте свою электронную почту для подтверждения регистрации.');
        
    }


    // при нажатии ссылки подтверждения на почте
    public function verifyEmail($token, $pendingUserId){
        // Получаем данные пользователя из сессии
        // $userData = Session::get('pending_user');ё
        // dd($pendingUserId);

        $pendingUser = PendingUser::where('token', $token)->where('id', $pendingUserId)->first();

        // dump($pendingUser);
        // Проверяем, существует ли пользователь и совпадает ли токен
        if (!$pendingUser || $pendingUser['token'] !== $token) {
            return redirect()->route('admin.index')->withErrors(['message' => 'Неверный токен.']);
        }

        // Создаем пользователя в базе данных
        $user = User::create([
            'name' => $pendingUser['name'],
            'email' => $pendingUser['email'],
            'password' => $pendingUser['password'],
            'verified' => true,
        ]);

        // Удаляем данные из сессии
        // Session::forget('pending_user');
        $pendingUser->delete();

        // Аутентификация пользователя (если необходимо)
        Auth::login($user);

        return redirect()->route('admin.index')->with('status', 'Ваш аккаунт успешно активирован!');

    }


    // повторная отправка письма
    public function resendVerificationEmail($pendingUserId)
    {
        $pendingUser = PendingUser::where('id', $pendingUserId)->first();

        // Проверяем, что пользователь есть в ожидающих
        if ($pendingUser) {
            // Отправляем письмо с верификацией
            Mail::to($pendingUser->email)->send(new VerifyEmail($pendingUser->token, $pendingUser->id));
    
            return back()->with('message', 'Ссылка для подтверждения была отправлена на вашу электронную почту.');
        }
    
        return back()->withErrors(['message' => 'Ваш email уже подтвержден или пользователь не найден.']);
    }
}
