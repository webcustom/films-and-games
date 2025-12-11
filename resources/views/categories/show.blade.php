@extends('layouts.base')

@section('page.title', 'Создать пост')



@section('content')
<div class="goUp"><svg><use xlink:href="#arrow"/></svg></div>


<section class="sectionPage _section _scrollUp">
    <div class="contain">
        {{-- {{ dd($collectionsItems) }} --}}
        <h1 class="title_2 _mb20">Подборки {{ $category->title }}:</h1>
        <a href="/" class="ref_1 _fz16">на главную</a>


        @php
        // Преобразуем массив в коллекцию
        // $sortOrder = $collectionsItems->sort_elems;

        // dd($sortOrder);

        // // Сортируем фильмы по индексу сортировки
        // $sortedElems = $order->sortBy(function ($elem) use ($sortOrder) {
        //     // Если id нет в массиве или индекс сортировки пустой, ставим самый большой индекс
        //     return (!empty($sortOrder[$elem->id])) ? $sortOrder[$elem->id] : PHP_INT_MAX;
        // }); 
    @endphp

    {{-- Выводим отсортированные фильмы --}}


        <div class="collectionsLIst">
            @foreach ($collectionsItems as $collection)

                @php
                    // $description = Str::words($collection->description, 10, '...');
                    // dd($collection->img_medium);
                @endphp
                <div class="collectionItem">
                    <div class="collectionItem__img">
                        @if(isset($collection->img_medium))
                            <img class="lazyImg" data-src="/{{ $collection->img_medium }}" alt="img">
                        @endif
                    </div>
                    <div class="collectionItem__content">
                        <div>
                            <p class="collectionItem__title">{{ $collection->title }}</p>
                            <div class="collectionItem__description">{{ Str::words(strip_tags($collection->description), 10, '...') }}</div>
                        </div>
                        {{-- <p class="collectionItem__description">{!! $collection->description !!}</p> --}}
                        <a href="/collections/{{ $collection->slug }}" class="button_1">Подробнее</a>
                    </div>
                </div>

            @endforeach
        </div>
        {{-- collectionsLIst --}}

                    {{-- 
                погинация
                для того что бы править шаблон пагинации выполняем комманду php artisan vendor:publish --tag=laravel-pagination 
                которая выносит шаблоны пагинации в рабочую область и помещает их в resources/views/vendor/pagination
            --}}
            <div class="_mt30">{{ $collectionsItems->links() }}</div>


    </div>
</section>



{{-- подтверждение удаления --}}
{{-- @include('includes.admin._confirmDelete', ['text' => 'Удалить подборку', 'route' => route('admin.collections.delete')]) --}}

<script>
    // deleteEditImg() //удаление и редактирование изображения записи
    // untyingLinkedElems() // открепление записей от категории
    // sortLinkedElems('sort_elems') // сортировка записей в категории
</script>
@endsection


{{-- @stack и @push() - позволяет подгружать скрипты или стили именно на тех страницах где они нужны --}}
{{-- @once позволяет все что внутри вставить лишь один раз, иначе эти скрипты и стили подключатся столько раз сколько мы поместим редакторов trix на странице --}}
@once
    @push('js')
        <script src="/js/main.js" defer></script>
        {{-- <script src="/admin/js/deleteEditImg.js"></script> --}}
        {{-- <script src="/admin/js/untyingAndSortLinkedElems.js"></script> --}}

    @endpush
@endonce
