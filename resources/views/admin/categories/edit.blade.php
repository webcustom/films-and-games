@extends('layouts.base')

@section('page.title', 'Создать пост')



@section('content')


<section class="sectionAdmin _section">
    <div class="contain">

        <h1 class="title_1">Редактирование категории: {{ $category->title }}</h1>
        <a class="ref_1 _mt10" href="{{ route('admin.categories.index') }}">Назад</a>

        <div class="_mt25">
            <button class="button_1 deleteElem_js" value="{{ $category->id }}" data-title="Удалить: <span class='_bold'>{{ $category->title }}</span>" onclick="show_popup('confirmDelete')">Удалить</button>
            <button class="button_1" type="submit" form="editForm">Сохранить</button>
        </div>

        <form id="editForm" class="form_1 _maxW800 _mt30" action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
            @csrf
            @method('PUT')

            <x-admin.input type="text" name="title" title="Заголовок" required value="{{ $category->title }}"/>
            <x-admin.input class="_mt20" type="text" name="slug" title="Слаг" value="{{ $category->slug }}"/>
            <x-admin.input type="text" name="published_at" title="Дата публикации" class="_mt20" value="{{ $category->published_at }}"/>
            {{-- <x-admin.checkbox name="published" value="1" class="_mt20" checked="{{ $category->published ? 'checked' : ''}}">Опубликовано</x-admin.checkbox> --}}

{{-- 
            @php
                dd($category->collections);
            @endphp --}}


            @if(count($category->collections) > 0)
                {{-- {{ dd($category->collections); }} --}}
                <div class="catElemsListWrap _mt20">
                    <p class="catElemsListWrap__title">Подборки привязанные к категории:</p>
                    <ul class="catElemsList__marks">
                        <li>сорт-ка</li>
                        <li>название</li>
                    </ul>
                    <ul class="catElemsList">
                        @php

                            // dd($category->sort_collections);
                            // Преобразуем массив в коллекцию
                            $sortOrder = collect($category->sort_collections);

                            // dd($category->sort_collections);
                            
                            // Сортируем фильмы по индексу сортировки
                            $sortedCollections = $category->collections->sortBy(function ($collection) use ($sortOrder) {
                                // Если id нет в массиве или индекс сортировки пустой, ставим самый большой индекс
                                return (!empty($sortOrder[$collection->id])) ? $sortOrder[$collection->id] : PHP_INT_MAX;
                            }); 

                        @endphp

                        {{-- Выводим отсортированные фильмы --}}
                        @foreach ($sortedCollections as $collection)
                            {{-- @php
                                dd($collection);
                            @endphp --}}
                            <li class="catElemsList__li">
                                <input type="text" class="catElemsList__elem_input elemsSort_js" value="{{ $category->sort_collections[$collection->id] ?? '' }}" data-id="{{ $collection->id }}">
                                <a href="{{ route('admin.collections.edit', $collection->id) }}" class="catElemsList__elem">
                                    <p>{{ $collection['title'] }}</p>
                                    <span class="closeIcon untieElement_js" data-id="{{ $collection->id }}" title="отвязать от подборки"><svg><use xlink:href="#close"/></svg></span>
                                </a>
                            </li>
                        @endforeach

                    </ul>
                </div>
                {{-- поле для id фильмов, связи с которыми нужно удалить --}}
                <input type="hidden" name="delete_elems" readonly value="">

                {{-- поле для добавления id и индекса сортировки --}}
                <input type="hidden" name="sort_collections" readonly value="">

                
            @else
                <p class="catElemsListWrap__title _mt20">Нет подборок привязанных к категории</p>
            @endif


            <button class="button_1 _big _mt20" type="submit">Сохранить</button>
        </form>


    </div>
</section>


{{-- подтверждение удаления --}}
@include('includes.admin._confirmDelete', [/*'text' => 'Удалить категорию',*/ 'route' => route('admin.collections.delete')])

<script>
    deleteEditImg() //удаление и редактирование изображения записи
    untyingLinkedElems() // открепление записей от категории
    sortLinkedElems('sort_collections') // сортировка записей в категории
</script>
@endsection


{{-- @stack и @push() - позволяет подгружать скрипты или стили именно на тех страницах где они нужны --}}
{{-- @once позволяет все что внутри вставить лишь один раз, иначе эти скрипты и стили подключатся столько раз сколько мы поместим редакторов trix на странице --}}
@once
    @push('js_admin')
        <script src="/admin/js/main.js" defer></script>
        <script src="/admin/js/deleteEditImg.js"></script>
        <script src="/admin/js/untyingAndSortLinkedElems.js"></script>

    @endpush
@endonce
