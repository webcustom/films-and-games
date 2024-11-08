@extends('layouts.base')

@section('page.title')
    Главная страница
@endsection


@section('content')

<section class="sectionMain _section">
    <div class="contain">
        @php
            // сначала выбираем опубликованные
            // $publishedItems = $collections->filter(function ($collection) {
            //     return $collection->published;
            // });

            // берем 3 певых элемента
            $topCollections = $collections->take(3);

            // создаем новый массив из массива $sortedItems за исключением того что у нас в $topCollections
            $remainingCollections = $collections->diff($topCollections);

        @endphp

        {{-- <p class="title_1 _mb20">Последнее:</p> --}}
        <div class="mainBlocks">
            @foreach($topCollections as $collection)
                <div class="mainBlocks__item {{ $loop->first ? 'firstEntry' : ($loop->iteration == 2 ? 'secondEntry' : 'thirdEntry') }}">
                    <a href="{{ route('collections.show', $collection->slug) }}" class="imgTotalWrap _backgroundDarkening">
                        <img class="imgTotal" src="{{ $collection->img }}" alt="img">
                        <p class="titleAbsolute" data-title="{{ $collection->title }}"><span>{{ $collection->title }}</span></p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

            
@foreach ($categories as $category)
<section class="sectionItem _section">
    <div class="contain">
        <div class="mainTop">
            <p class="title_1">Подборки: {{ $category->title }}</p>
            <a href={{ route('categories.show', $category->slug) }} class="button_1 _shadow">Смотреть все</a>
        </div>
        {{-- {{ dd($category) }} --}}
        <div class="blocksList_1 _mt40">
            @foreach($remainingCollections->where('category_id', $category->id)->take(8) as $collection)
                <a href="{{ route('collections.show', $collection->slug) }}" class="imgTotalWrap _backgroundDarkening">
                    <img class="imgTotal" src="{{ $collection->img }}" alt="img">
                    <p class="titleAbsolute" data-title="{{ $collection->title }}"><span>{{ $collection->title }}</span></p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endforeach





@endsection
