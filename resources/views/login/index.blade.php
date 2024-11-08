@extends('layouts.base')

@section('page.title')
    Авторизация
@endsection


@section('content')

<section class="sectionPage _section">
    <div class="contain">
        
        <h1 class="title_1 _center">Вход</h1>

        <div class="centerItem _maxW500 _mt20">
            <form class="form_1" action="{{ route('login.store') }}" method="POST" novalidate autocomplete="off">
                @csrf
                {{-- проверяем есть ли ошибки --}}
                {{-- @if($errors->any())
                    <ul class="alertItem _red _left _mt10">
                        @foreach($errors->all() as $message)
                            <li>
                                {{ $message }}
                            </li>
                        @endforeach
                    </ul>
                @endif --}}

                <x-admin.input class="_mt20" type="email" name="email" title="Email" required/>
                <x-admin.input class="_mt20" type="password" name="password" title="Пароль" required />

                {{-- <x-admin.input class="_mt20" type="password" name="password" title="Пароль" required  notError/> --}}




                {{-- <div class="inputWrap_1 _mt20">
                    <label class="inputWrap_1__name">Email</label>
                    <input type="email" name="email" class="input_1 @error('email') _error @enderror" value="{{ request()->old('email') }}" autofocus>
                </div>
                <div class="inputWrap_1 _mt20">
                    <label class="inputWrap_1__name">Пароль</label>
                    <input type="password" name="password" class="input_1 @error('password') _error @enderror" value="{{ request()->old('password') }}">
                </div> --}}

                <div class="_center _mt10">
                    <button class="button_1 _big _mb20" type="submit">Войти</button>
                    <br>
                    {{-- <a href="{{ route('register.index') }}" class="_center ref_1 _fz16">Зарегистрироваться</a> --}}
                </div>
            </form>
        </div>

    </div>

</section>

@endsection

{{-- @once --}}
    {{-- @push('js_css') --}}
    {{-- @vite('resources/admin/sass/main.sass' /*, 'resources/js/app.js'*/) --}}
    {{-- @endpush --}}
{{-- @endonce --}}
