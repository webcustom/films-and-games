@extends('layouts.base')

@section('page.title')
    Админ панель
@endsection


@section('content')

{{-- @php
    if (!Auth::check()) {
        // Пользователь аутентифицирован
        // echo 11111;
        dd('asdasdasd');
        // redirect()->route('login.index');
    }
@endphp --}}

<section class="sectionAdmin _section">
    <div class="contain">
        
        <div class="titleItem">
            <h1 class="title_1">Админ панель главная</h1>
        </div>


     
    </div>
</section>

@endsection
