<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;



use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;




class SettingsController extends Controller
{
    //
    public function index(Request $request){
        // $query = User::query();

        $user = auth()->user(); //получаем текущего юзера
        // dd($user);

        return view('admin.settings.index', compact('user'));
    }

    // public function update(Request $request, User $user){
    //     // dump($user);
    //     // dump($user->password);
    //     // dump($request->password_old);

    //     $userPassword = $user->password;
    //     $userPasswordInput = $request->password_old;


    //     $validated = $request->validate([
    //         'name' => ['required', 'string', 'max:100'],
    //         'email' => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($user->id)],
    //         'password_new' => ['nullable', 'string', 'min:7', 'confirmed'],

    //     ]);



    //     $user->name = $validated['name']; // Устанавливаем имя из запроса
    //     $user->email = $validated['email']; // Устанавливаем email из запроса
    //     // $user->password_new = $validated['password_new'];

    //     // dd($validated['password_new']);
    //     // dump($userPasswordInput);
    //     // dump($userPassword);

    //     // if($userPasswordInput !== null){
    //     // проверяем старый пароль
    //     if (Hash::check($userPasswordInput, $userPassword)) {
    //         // dump('пароль совпадает');
    //         // dump($request['password_new'] !== null);
    //         if($validated['password_new'] !== null){
    //             $user->password = bcrypt($validated['password_new']);
    //         }
    //     }else{
    //         // dump('пароль не совпадает');
    //         session()->flash('error', 'Пароль не совпадает');
    //         // return back()->with('error', 'Пароль не совпадает');
    //         return redirect()->back();
    //     }
    //     // }


    //     // проверяем есть ли изменения в данных
    //     if ($user->isDirty()) {
    //         // dd($validated['password_new']);
    //         // dd('изменения есть');

    //         if($validated['password_new'] !== null){
    //             User::where('id', $user->id)->update([
    //                 'name' => $validated['name'],
    //                 'email' => $validated['email'],
    //                 'password' => bcrypt($validated['password_new']),
    //             ]);
    //         }else{
    //             User::where('id', $user->id)->update([
    //                 'name' => $validated['name'],
    //                 'email' => $validated['email'],
    //             ]);
    //         }

    //         alert(__('Сохранено')); //добавляем сессию alert смотреть helpers.php

    //     }


        


    //     // $user = User::query()->update([
    //     //     'name' => $validated['name'],
    //     //     // 'email' => $validated['email'],
    //     // ]);

    //     // $validArray = [
    //     //     'user' => $validated['title'],
    //     //     'img' => $path_original ?? null, //$validated['img'],
    //     //     'img_medium' => $path_medium ?? null, //$validated['img'],
    //     //     'img_thumbnail',
    //     // ];


    //     // $filmNew = $film->update($validArray);

    //     // $request->validate([
    //     //     'name' => ['required', 'string', 'max:100'], 
    //     //     'email' => ['required', 'string', 'email', 'unique:users,email'], 
    //     //     'password_old' => ['required', 'string', 'min:7'/*, 'exists:users,password'*/],
    //     //     'password_new' => ['required', 'string', 'min:7', 'confirmed'],
    //     //     'password_new_confirmation' => ['required', 'string', 'min:7'],
    //     // ]);


    //     return redirect()->back();
    //     // return redirect()->route('admin.settings.index');
    // }
    public function update(Request $request, User $user){
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password_new' => ['nullable', 'string', 'min:7', 'confirmed'],
        ]);

        if (Hash::check($request->password_old, $user->password)) {
            // dump('пароль совпадает');
            // dump($request['password_new'] !== null);
            if($validated['password_new'] !== null){
                $user->password = bcrypt($validated['password_new']);
            }
        }else{
            // dump('пароль не совпадает');
            session()->flash('error', 'Пароль не совпадает');
            // return back()->with('error', 'Пароль не совпадает');
            return redirect()->back();
        }
    
        // tap берет объект $user и добавялет к нему name и email
        $user = tap($user)->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        alert(__('Сохранено'));
    
        // if ($user->isDirty()) {
        //     dd('cj[h');
        //     $user->save();
        //     alert(__('Сохранено'));
        // }
    
        return redirect()->back();
    }
}
