<header id="header">
    <div class="contain">

        <div class="header__left">
            <a href="{{route('home')}}">{{config('app.name')}}</a>
        </div>
        <nav class="menu_1">
            <ul>
                <li><a href="{{route('admin.index')}}">Фильмы</a></li>
                <li><a href="{{route('admin.index')}}">Игры</a></li>
            </ul>
        </nav>

        <div class="header__right">
            <nav class="menu_1">
                <ul>
                    <a href="{{route('admin.index')}}">В админку</a>
                </ul>
            </nav>
        </div>

        
    </div>
</header>