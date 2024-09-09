<header id="headerAdmin">
    <div class="contain">

        <div class="header__left">
            <a href="{{route('home')}}">{{config('app.name')}}</a>
        </div>
        <nav class="menu_1">
            <ul>
                {{-- {{ __('Главная') }} такая запись позволит в дальнейшем легко сделать мультиязычную версию --}}
                {{-- active_link('articles*') - такая запись говорит что активный пункт articles и все его вложенные маршруты --}}
                {{-- {{ dd(Auth::user()->usertype) }} --}}
                @if (Auth::check() && Auth::user()->usertype === 'admin')
                    <li><a class="{{ active_link('home') }}" href="{{ route('home') }}" aria-current="page">{{ __('На сайт') }}</a></li>
                    <li><a class="{{ active_link('admin.index') }}" href="{{ route('admin.index') }}" aria-current="page">{{ __('Dashboard') }}</a></li>
                    <li><a class="{{ active_link('admin.categories.index') }}" href="{{ route('admin.categories.index') }}" aria-current="page">{{ __('Категории') }}</a></li>
                    <li><a class="{{ active_link('admin.collections.index') }}" href="{{ route('admin.collections.index') }}" aria-current="page">{{ __('Подборки') }}</a></li>
                    <li><a class="{{ active_link('admin.films.index') }}" href="{{ route('admin.films.index') }}">{{ __('Фильмы') }}</a></li>
                    <li><a class="{{ active_link('admin.games.index') }}" href="{{ route('admin.games.index') }}">{{ __('Игры') }}</a></li>
                    
                    {{-- <li><a class="{{ active_link('user*') }}" href="{{ route('user.articles.index') }}">{{ __('Юзер') }}</a></li> --}}
                    {{-- <li><a class="{{ active_link('user.donates') }}" href="{{ route('user.donates') }}">{{ __('Донаты') }}</a></li> --}}
                @endif
            </ul>
        </nav>

        <div class="header__right">
            <nav class="menu_1">
                <ul>
                    {{-- <img src="{{ asset('admin/img/setting.svg') }}" alt=""> --}}
                    @if (Auth::check() && Auth::user()->usertype === 'admin')
                        {{-- в route прописываем ->name() роута --}}
                        {{-- <li><a href="{{ route('register.index') }}" class="{{ Route::is('register.index') ? '_active' : '' }}">{{ __('Регистрация') }}</a></li>
                        <li><a href="{{ route('login.index') }}" class="{{ active_link('login.index') }}">{{ __('Вход') }}</a></li> --}}
                        <li>
                            <a class="iconRef {{ active_link('admin.settings.index') }}" href="{{ route('admin.settings.index') }}">
                                <svg><use xlink:href="#user" /></svg>
                            </a>
                        </li>
                        <li><a href="{{ route('logout.index') }}">{{ __('Выход') }}</a></li>
                    @else
                        <li><a href="{{ route('logout.index') }}">{{ __('Вход') }}</a></li>
                    @endif
                </ul>
            </nav>
        </div>

        
    </div>
</header>