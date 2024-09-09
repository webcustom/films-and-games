@extends('layouts.base')

@section('page.title')
    Регистрация
@endsection

@section('content')

<section class="sectionAdmin _section">
    <div class="contain">
        
        <h1 class="title_1 _center">Регистрация</h1>

        <p>Спасибо за регистрацию! На указанный email отправлена ссылка, перейдите по ней для завершения регистрации</p>

        <div class="centerItem _maxW500 _mt25">
            <form class="form_1" action="{{ route('verification.send') }}" method="POST" novalidate autocomplete="off">
                @csrf

                <p>Если вы не получили ссылку - кликните по кнопке "Отправить повторно"</p>
                <div class="_center _mt10">
                    <button class="button_1 _big" type="submit">Отправить повторно</button>
                </div>
            </form>
        </div>
    </div>

</section>

@endsection


