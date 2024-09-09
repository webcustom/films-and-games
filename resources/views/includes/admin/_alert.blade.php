{{-- если в сессии есть alert session()->pull('alert') - такая запись позволяет 1 раз показать 
и затем при переходе на другую страницу сразу удалить данные из сессии--}}
@if($alert = session()->pull('alert'))
{{-- {{ dd(session()->all()) }} --}}
    <div class="alertItem {{ session()->has('alert_class') ? session('alert_class') : '' }}">
        {{ $alert }}
    </div>

    <script>
        console.log('Значение ключа "alert" в сессии:', sessionStorage.getItem('alert'))
        setTimeout(function() {
            document.querySelector('.alertItem').style.display = 'none'
        }, 4000)
    </script>
@endif