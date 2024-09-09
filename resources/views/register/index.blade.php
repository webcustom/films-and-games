@extends('layouts.base')

@section('page.title')
    Регистрация
@endsection


@section('content')

<section class="sectionPage _section">
    <div class="contain">
        
        <h1 class="title_1 _center">Регистрация</h1>

        <div class="centerItem _maxW500 _mt25">
            {{-- @guest --}}
                <form class="form_1" action="{{ route('register.store') }}" method="POST" novalidate autocomplete="off">
                    @csrf
                    <div class="inputWrap_1">
                        <label class="inputWrap_1__name _required">Логин</label>
                        <input type="text" name="name" class="input_1 {{ $errors->has('name') ? '_error' : '' }}" value="{{ request()->old('name') }}" autofocus>
                        @if($errors->has('name'))
                            {{-- @php
                            dd($errors);
                            @endphp --}}
                            <p class="inputAlert _red">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                    <div class="inputWrap_1 _mt20">
                        <label class="inputWrap_1__name _required">Email</label>
                        <input type="email" name="email" class="input_1 @error('email') _error @enderror" value="{{ request()->old('email') }}" autofocus>
                        @error('email')
                            <p class="inputAlert _red">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="inputWrap_1 _mt20">
                        <label class="inputWrap_1__name _required">Пароль</label>
                        <input type="password" name="password" class="input_1 @error('password') _error @enderror" value="{{ request()->old('password') }}">
                        @error('password')
                            <p class="inputAlert _red">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="inputWrap_1 _mt20">
                        <label class="inputWrap_1__name _required">Пароль еще раз</label>
                        <input type="password" name="password_confirmation" class="input_1 @error('password_confirmation') _error @enderror" value="{{ request()->old('password_confirmation') }}">
                    </div>

                    <div class="_center _mt10">
                        <button class="button_1 _big _mb20" type="submit">Зарегистрироваться</button>
                        <br>
                        <a href="{{ route('login.index') }}" class="_center ref_1 _fz16">Я уже зарегистрирован</a>
                    </div>
                </form>
            {{-- @endguest --}}

            {{-- если пользователь аутентифицировался т.е его данные внесенные в форму регистрации прошли валидацию 
            мы говорим ему о том что нужно еще верифицироваться т.е подтвердить email --}}
            {{-- @auth
                <p>Спасибо за регистрацию! На указанный email отправлена ссылка, перейдите по ней для завершения регистрации</p>
                <p>Если вы не получили ссылку - кликните по кнопке "Отправить повторно"</p>
                <form class="form_1" action="{{ route('verification.send') }}" method="POST" novalidate autocomplete="off">
                    @csrf

                    <p>Если вы не получили ссылку - кликните по кнопке "Отправить повторно"</p>
                    <div class="_center _mt10">
                        <button class="button_1 _big" type="submit">Отправить повторно</button>
                    </div>
                </form>
            @endauth --}}
        </div>


    </div>

</section>

@endsection

{{-- @once --}}
    {{-- @push('js_css') --}}
    {{-- @vite('resources/admin/sass/main.sass' /*, 'resources/js/app.js'*/) --}}
    {{-- @endpush --}}
{{-- @endonce  --}}