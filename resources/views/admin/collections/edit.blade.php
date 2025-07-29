@extends('layouts.base')

@section('page.title', 'Создать пост')



@section('content')


<section class="sectionAdmin _section">
    <div class="contain">

        <h1 class="title_1">Редактирование подборки: {{ $collection->title }}</h1>
        <a class="ref_1 _mt10" href="{{ route('admin.collections.index') }}">Назад</a>
        {{-- <a class="ref_1 _mt10" href="{{ url()->previous() }}">Назад</a> --}}

        

        <div class="_mt25">
            <button class="button_1 deleteElem_js" data-id="{{ $collection->id }}" data-title="Удалить подборку: <span class='_bold'>{{ $collection->title }}</span>" onclick="show_popup('confirmDelete')">Удалить</button>
            <button class="button_1" onclick="show_popup('catList')">Выбрать категорию</button>
            <button class="button_1" type="submit" form="editForm">Сохранить</button>
        </div>

        <form id="editForm" class="form_1 _maxW700 _mt30" action="{{ route('admin.collections.update', $collection) }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
            @csrf
            @method('PUT')


            @php
            if(isset($collection->category_id)){
                $category = $categories->find($collection->category_id);
                // dd($category);

                if($collection->category_id === $category->id){
                    // $order = $collection->films;
                    $order = $collection[$category['slug']]; //так получаем $collection->films или $collection->games
                    $title = $category->title;

                    // dd($category['slug']);
                    $category_id = (int)$collection->category_id;
                    // dd($category_id);

                }
            }

            @endphp
            



            <x-admin.categoryPopup :order="isset($order) ? $order : null" radio :elems="$categories" category_id="{{ isset($category_id) ? $category_id : null }}"></x-admin.categoryPopup>

            
            {{-- <div class="popupBlock" data-flag="catList">
                <div class="popupConfirm popupItem">
                    @if(isset($order) && count($order) > 0 )
                        <p class="_fz14 _red">Прежде чем сменить категорию, <br> отвяжите все элементы от подборки</p>
                    @endif
                    <div class="listElems_1 _mt20 _mb20 {{ (isset($order) && count($order) > 0 ) ? '_disable' : '' }}">
                        <ul>
                            <li>
                                <x-admin.checkbox type="radio" name="category_id" value="" checked="{{ (!isset($collection->category_id)) ? true : false }}">Нет категории</x-admin.checkbox>
                            </li>
                            @foreach ($categories as $category)

                                <li>
                                    <x-admin.checkbox type="radio" name="category_id" value="{{ $category->id }}" checked="{{ ($category->id === $collection->category_id) ? true : false }}">{{ $category->title }}</x-admin.checkbox>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <span class="button_1 _big" onclick="close_popup(this)">ОК</span>
                    </div>
                </div>
            </div> --}}

            <x-admin.input type="text" name="title" title="Заголовок" required value="{{ $collection->title }}"/>
            <x-admin.input class="_mt20" type="text" name="slug" title="Слаг" value="{{ $collection->slug }}"/>

            @if(isset($collection->img_medium))
                <div class="uploadImg _mt20">
                    <img src="/{{ $collection->img_medium }}"/>
                    <span class="closeIcon" onclick="deleteInputImg('{{ $collection->img_medium }}')"><svg><use xlink:href="#close"/></svg></span>
                </div>
            @endif
            <x-admin.input id="add_img" type="file" name="img" title="Загрузить изображение" class="_mt20"/>
            <input type="hidden" name="delete_img" readonly value="">

            <x-admin.input type="text" title="Содержание поста" name="description" class="_mt20" value="{{ strip_tags($collection->description) ?? '' }}">
                {{-- <x-slot name="trix"></x-slot> можно дополнять компоненту слотом --}}
                <x-slot name="trix"></x-slot>
            </x-admin.input>
            {{-- <x-admin.input type="text" name="resource_id" title="id фильмов или других элементов (через запятую)" class="_mt20"/> --}}
            <x-admin.input type="text" name="published_at" title="Дата публикации" class="_mt20" value="{{ $collection->published_at }}"/>
            <x-admin.checkbox name="published" value="1" class="_mt20" checked="{{ $collection->published ? 'checked' : ''}}">Опубликовано</x-admin.checkbox>



            

            @if(isset($order) && count($order) > 0 )

                <div class="catElemsListWrap _mt20">
                    <p class="catElemsListWrap__title">{{ $title }} привязанные к подборке:</p>
                    <ul class="catElemsList__marks">
                        <li>сорт-ка</li>
                        <li>название</li>
                    </ul>
                    <ul class="catElemsList">
                        @php

                            // Преобразуем массив в коллекцию
                            $sortOrder = collect($collection->sort_elems);
                            // Сортируем фильмы по индексу сортировки
                            $sortedElems = $order->sortBy(function ($elem) use ($sortOrder) {
                                // dd($elem);
                                // Если id нет в массиве или индекс сортировки пустой, ставим самый большой индекс
                                return (!empty($sortOrder[$elem->id])) ? $sortOrder[$elem->id] : PHP_INT_MAX;
                            }); 
                        @endphp

                        {{-- Выводим отсортированные фильмы --}}
                        @foreach ($sortedElems as $elem)
                            <li class="catElemsList__li">
                                <input type="text" class="catElemsList__elem_input elemsSort_js" value="{{ $collection->sort_elems[$elem->id] ?? '' }}" data-id="{{ $elem->id }}">
                                <a href="{{ route('admin.'.$category['slug'].'.edit', $elem->id) }}" class="catElemsList__elem">
                                    <p>{{ $elem['title'] }}</p>
                                    <span class="closeIcon untieElement_js" data-id="{{ $elem->id }}" title="отвязать от подборки"><svg><use xlink:href="#close"/></svg></span>
                                </a>
                            </li>
                        @endforeach

                    </ul>

                </div>
                {{-- поле для id фильмов, связи с которыми нужно удалить --}}
                <input type="hidden" name="delete_elems" readonly value="">

                {{-- поле для добавления id и индекса сортировки --}}
                <input type="hidden" name="sort_elems" readonly value="">
                {{-- <input type="hidden" name="sort_games" readonly value=""> --}}


                
            @else
                <p class="catElemsListWrap__title _mt20">Нет элементов привязанных к подборке</p>
            @endif

            {{-- {{ dd($collection->games) }} --}}
            {{-- @if(count($collection->games) > 0)
            <div class="catElemsListWrap _mt20">
                <p class="catElemsListWrap__title">Игры привязанные к подборке:</p>
                        <ul class="catElemsList__marks">
                            <li>сорт-ка</li>
                            <li>название</li>
                        </ul>
                        <ul class="catElemsList">
                            @php
                            // dd($collection->films);
                            // dd($collection->sort_films);
                            // Преобразуем массив в коллекцию
                            $sortOrder2 = collect($collection->sort_games);
                            // dd($collection->films);
                            // Сортируем фильмы по индексу сортировки
                            $sortedGames = $collection->games->sortBy(function ($game) use ($sortOrder2) {
                                // Если id нет в массиве или индекс сортировки пустой, ставим самый большой индекс
                                return (!empty($sortOrder2[$game->id])) ? $sortOrder2[$game->id] : PHP_INT_MAX;
                            }); 
                            // dd($sortedFilms);

                        @endphp
                        @foreach ($sortedGames as $game)

                            <li class="catElemsList__li">
                                <input type="text" class="catElemsList__elem_input elemsSort_js" value="{{ $collection->sort_games[$game->id] ?? '' }}" data-id="{{ $game->id }}">
                                <div class="catElemsList__elem">
                                    <p>{{ $game['title'] }}</p>
                                    <span class="closeIcon untieElement_js" data-id="{{ $game->id }}" title="отвязать от подборки"><svg><use xlink:href="#close"/></svg></span>
                                </div>
                            </li>
                        @endforeach
                        </ul>
            </div>
            @else
                <p class="catElemsListWrap__title _mt20">Нет игр привязанных к подборке</p>
            @endif --}}


            <button class="button_1 _big _mt20" type="submit">Сохранить</button>
        </form>


    </div>
</section>



{{-- подтверждение удаления --}}
@include('includes.admin._confirmDelete', ['text' => 'Удалить подборку', 'route' => route('admin.collections.delete')])

<script>
    deleteEditImg() //удаление и редактирование изображения записи
    untyingLinkedElems() // открепление записей от категории
    sortLinkedElems('sort_elems') // сортировка записей в категории
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
