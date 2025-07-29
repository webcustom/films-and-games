@extends('layouts.base')

@section('page.title')
    Регистрация
@endsection

@section('content')

<section class="sectionMain _section">
    <div class="contain">
        
        <h1 class="title_1 _center">Регистрация</h1>

        <p class="_fz16 _center _mt25">Спасибо за регистрацию! На указанный email отправлена ссылка, перейдите по ней для завершения регистрации</p>

        <div class="_mt15">
            <form class="form_1" action="{{ route('verification.resend', ['pendingUserId' => $pendingUserId]) }}" method="POST" novalidate autocomplete="off">
                @csrf

                {{-- @php
                    dd($pendingUser);
                @endphp --}}
                <p class="_fz16 _center">Если вы не получили ссылку - кликните по кнопке "Отправить повторно"</p>
                <div class="_center _mt10">
                    <p id="timer" class="_fz16">30</p>
                    <button class="button_1 _big _disable" type="submit">Отправить повторно</button>
                </div>
            </form>

            @if(session('message'))
                <div class="_center _mt20">
                    <p class="_fz16">{{ session('message') }}</p>
                </div>
            @endif
            
            @if($errors->any())
                <div class="_center _mt10">
                    @foreach ($errors->all() as $error)
                        <p class="_fz16">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

</section>

@endsection

<script>

document.addEventListener('DOMContentLoaded', function() {
    let timeLeft = 30; // Время в секундах
    let timerId;

    function startTimer() {
        // Сбрасываем таймер, если он уже запущен
        clearInterval(timerId);
        timeLeft = 30; // Сброс времени
        document.getElementById('timer').textContent = timeLeft;

        timerId = setInterval(() => {
            timeLeft--;
            document.getElementById('timer').textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(timerId)
                document.querySelector('.button_1').classList.remove('_disable')
                document.getElementById('timer').style.display = 'none'
            }
        }, 1000); // Обновление каждую секунду (1000 мс)
    }
    
    startTimer()

})
    // document.getElementById('startButton').addEventListener('click', startTimer);
</script>
