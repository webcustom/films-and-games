<div class="adminSidebar">
    <div class="contain">

        <div class="adminSidebar__top">
            <a href="{{route('home')}}">{{config('app.name')}}</a>
        </div>
        <nav class="menu_1">
            <ul>
                {{-- {{ __('Главная') }} такая запись позволит в дальнейшем легко сделать мультиязычную версию --}}
                {{-- active_link('articles*') - такая запись говорит что активный пункт articles и все его вложенные маршруты --}}
                <li><a class="{{ active_link('home') }}" href="{{ route('home') }}" aria-current="page">{{ __('На сайт') }}</a></li>
                <li><a class="{{ active_link('admin.index') }}" href="{{ route('admin.index') }}" aria-current="page">{{ __('Админ/Главная') }}</a></li>
                
                <li><a class="{{ active_link('admin.categories.index') }}" href="{{ route('admin.categories.index') }}" aria-current="page">{{ __('Админ/Категории') }}</a></li>

                
                <li><a class="{{ active_link('admin.collections.index') }}" href="{{ route('admin.collections.index') }}" aria-current="page">{{ __('Админ/Подборки') }}</a></li>
                <li><a class="{{ active_link('admin.films.index') }}" href="{{ route('admin.films.index') }}">{{ __('Админ/Фильмы') }}</a></li>
                {{-- <li><a class="{{ active_link('user*') }}" href="{{ route('user.articles.index') }}">{{ __('Юзер') }}</a></li> --}}
                {{-- <li><a class="{{ active_link('user.donates') }}" href="{{ route('user.donates') }}">{{ __('Донаты') }}</a></li> --}}

            </ul>
        </nav>

        <div class="adminSidebar__bottom">
            <ul class="list_1">
                @if (Auth::check())
                    {{-- в route прописываем ->name() роута --}}
                    {{-- <li><a href="{{ route('register.index') }}" class="{{ Route::is('register.index') ? '_active' : '' }}">{{ __('Регистрация') }}</a></li>
                    <li><a href="{{ route('login.index') }}" class="{{ active_link('login.index') }}">{{ __('Вход') }}</a></li> --}}
                    <li><a href="{{ route('logout.index') }}">{{ __('Выход') }}</a></li>
                @else
                    <li><a href="{{ route('logout.index') }}">{{ __('Вход') }}</a></li>
                @endif
            </ul>
        </div>

        
    </div>
</div>