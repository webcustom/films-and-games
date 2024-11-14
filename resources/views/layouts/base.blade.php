<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <!-- <base href="/"> -->

    <title>@yield('page.title', 'значение по умолчанию')</title>
    <meta name="description" content="Desctiption...">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


    <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon" />
    <link rel="icon" type="image/png" href="img/favicon/favicon.png" sizes="128x128" />
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png" />


<!--     <meta property="og:image" content="https://roza.friday.ru/img/share/share.jpg">
    <meta property="ok:image" content="https://roza.friday.ru/img/share/share.jpg">
    <meta property="vk:image" content="https://roza.friday.ru/img/share/share.jpg">
    <meta property="fb:image" content="https://roza.friday.ru/img/share/share.jpg">

    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height" content="630"/>

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:image" content="https://roza.friday.ru/img/share/share.jpg" /> -->

    <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#000">
    <!-- Windows Phone -->
    <!-- <meta name="msapplication-navbutton-color" content="#000"> -->
    <!-- iOS Safari -->
    <!-- <meta name="apple-mobile-web-app-status-bar-style" content="#000"> -->

    {{-- <link rel="stylesheet" href="css/libs.min.css"> --}}
    {{-- <link rel="stylesheet" href="css/main.css?v=1"> --}}



    @php
        use Illuminate\Support\Str;
    @endphp 

    {{-- если путь начинается с admin и пользователь авторизован и является админом --}}
    @if(Str::startsWith(request()->path(), 'admin/') && Auth::check() && Auth::user()->usertype === 'admin')
        @vite('resources/admin/sass/main.sass' /*, 'resources/js/app.js'*/)
        {{-- <link rel="stylesheet" href="{{ asset('assets/main-OSXSCQmp.css') }}"> --}}
        @stack('css_admin') 
        @stack('js_admin')
    @else
        @vite('resources/sass/main.sass' /*, 'resources/js/app.js'*/)
        {{-- <link rel="stylesheet" href="{{ asset('public/build/assets/main-OSXSCQmp.css') }}"> --}}

        @stack('css') 
        @stack('js')
    @endif
    

    {{-- @if (Auth::check() && Auth::user()->usertype === 'admin' || request()->is('login') || request()->is('register'))
        @vite('resources/admin/sass/main.sass' /*, 'resources/js/app.js'*/)
        @stack('css_admin') 
        @stack('js_admin')
    @else
        @vite('resources/sass/main.sass' /*, 'resources/js/app.js'*/)
        @stack('css') 
        @stack('js')
    @endif --}}

    {{-- если пользователь зашел в админку --}}
    {{-- @if(request()->is('admin*'))
        @vite('resources/js/admin/main.js')
    @endif --}}
        
    
    {{-- <script src="libs/jquery/jquery.min.js" defer></script> --}}
    {{-- <script src="js/libs.min.js" defer></script> --}}
    {{-- <script src="js/common.js?v=1" defer></script> --}}

    {{-- @stack и @push() - позволяет подгружать скрипты или стили именно на тех страницах где они нужны --}}
    {{-- @stack('css_admin')  --}}
    {{-- @stack('js_admin') --}}

</head>
<body>
    
    <div class="wrapper">
        <div class="body_content">
            {{-- если путь начинается с admin и пользователь авторизован и является админом --}}
            @if(Str::startsWith(request()->path(), 'admin/') && Auth::check() && Auth::user()->usertype === 'admin')
                @include('includes.admin._alert')
                @include('includes.admin._header')
            {{-- если мы находимся не на странице login или register --}}
            @elseif(!request()->is('login') && !request()->is('register'))
                @include('includes._header')
            @endif
            
            @yield('content')
            {{-- </div> --}}
        </div>
        <!-- body_content -->

        {{-- @include('includes.admin._footer') --}}

        @if(Str::startsWith(request()->path(), 'admin/') && Auth::check() && Auth::user()->usertype === 'admin')
            @include('includes.admin._footer')
            {{-- если мы находимся не на странице login или register --}}
        @elseif(!request()->is('login') && !request()->is('register'))
            @include('includes._footer')
        @endif
    </div>
    <!-- wrapper -->


    @include('includes.admin._svgSprite')


</body>
</html>
