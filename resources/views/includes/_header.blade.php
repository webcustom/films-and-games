<header id="header">
    <div class="contain">

        <div class="header__left">
            <a href="{{route('home')}}" class="header__logo _img100"><img src="{{ asset('img/logo.png') }}" alt=""></a>
        </div>
        <nav class="menu_1">
            <ul>
                <li><a href="{{route('categories.show', 'films')}}">Фильмы</a></li>
                <li><a href="{{route('categories.show', 'games')}}">Игры</a></li>
            </ul>
        </nav>

        @if(Auth::check() && Auth::user()->usertype === 'admin')
            <div class="header__right">
                <nav class="menu_1">
                    <ul>
                        <li><a href="{{route('admin.index')}}">В админку</a></li>
                    </ul>
                </nav>
            </div>
        @endif

        
    </div>
</header>