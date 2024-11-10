@extends('layouts.base')

@section('page.title', 'Создать пост')



@section('content')


<section class="sectionPage _section">
    <div class="contain">
        {{-- {{ dd($collection) }} --}}
        <div class="itemDetail _mw800">
            
            @if(isset($collection->img))
                <div class="itemDetail__img">
                    <img class="lazyImg" data-src="/{{ $collection->img }}" alt="img">
                    <h1 class="titleAbsolute"><span>{{ $collection->title }}</span></h1>
                </div>
            @endif
            <div class="itemDetail__middle">
                <a class="ref_1" href="{{ url()->previous() }}">Назад</a>
                <p class="dateItem">дата публикации: {{ $collection->published_at }}</p>
            </div>
            

            @if(isset($collection->description))
                <div class="itemDetail__text">
                    <p>{!! $collection->description !!}</p>
                </div>
            @endif


            @php


                // $category = $categories->find($collection->category_id);
                // dd($collection->category_id);
                
                if($collection->category_id === $category->id){
                    // $order = $collection->films;
                    $order = $collection[$category['slug']]; //так получаем $collection->films или $collection->games
                    // $title = 'Фильмы';
                }

                // dd($category['slug']);
                // dd($order);
            @endphp


            @if(isset($order) && count($order) > 0 )

                <div class=" _mt20">
                        @php
                            // Преобразуем массив в коллекцию
                            $sortOrder = collect($collection->sort_elems);
                            // Сортируем фильмы по индексу сортировки
                            $sortedElems = $order->sortBy(function ($elem) use ($sortOrder) {
                                // Если id нет в массиве или индекс сортировки пустой, ставим самый большой индекс
                                return (!empty($sortOrder[$elem->id])) ? $sortOrder[$elem->id] : PHP_INT_MAX;
                            }); 
                        @endphp

                        {{-- Выводим отсортированные фильмы --}}
                        @foreach ($sortedElems as $elem)
                        {{-- {{ dd($elem) }} --}}
                            <section class="sectionDetail _mt50">
                                <div class="sectionDetail__inner">
                                    <div class="doubleTop">
                                        <h1 class="title_2">{{ $elem['title'] }} <span class="date">({{ $elem->release }})</span></h1>
                                        {{-- <p class="sectionDetail__date">{{ $elem->release }}</p> --}}
                                    </div>
                                    @if(isset($elem->img))
                                        <div class="itemDetail__img">
                                            <img class="lazyImg" data-src="/{{ $elem->img }}" alt="img">
                                        </div>
                                    @endif

                                    <div class="paramslistWrap">
                                        <ul class="parmsList">      
                                            @if(isset($elem->rating_imdb))
                                                <li class="itemDetail__param">
                                                    <p>рейтинг Imdb:</p>
                                                    <p>{{ $elem->rating_imdb }}</p>
                                                </li>
                                            @endif
                                            @if(isset($elem->rating_kinopoisk))
                                                <li class="itemDetail__param">
                                                    <p>рейтинг Кинопоиск:</p>
                                                    <p>{{ $elem->rating_kinopoisk }}</p>
                                                </li>
                                            @endif

                                            @if(isset($elem->release))
                                                <li class="itemDetail__param">
                                                    <p>релиз:</p>
                                                    <p>{{ $elem->release }}</p>
                                                </li>
                                            @endif

                                            @if(isset($elem->duration))
                                                <li class="itemDetail__param">
                                                    <p>продолжительность:</p>
                                                    <p>{{ $elem->duration }}</p>
                                                </li>
                                            @endif

                                           

                                            @if(isset($elem->country))
                                                <li class="itemDetail__param">
                                                    <p>страна:</p>
                                                    <p>{{ $elem->country }}</p>
                                                </li>
                                            @endif

                                            @if(isset($elem->budget))
                                                <li class="itemDetail__param">
                                                    <p>бюджет:</p>
                                                    <p>{{ $elem->budget }}</p>
                                                </li>
                                            @endif

                                            @if(isset($elem->fees_usa))
                                                <li class="itemDetail__param">
                                                    <p>сборы в США:</p>
                                                    <p>{{ $elem->fees_usa }}</p>
                                                </li>
                                            @endif

                                            @if(isset($elem->fees_world))
                                                <li class="itemDetail__param">
                                                    <p>сборы в мире:</p>
                                                    <p>{{ $elem->fees_world }}</p>
                                                </li>
                                            @endif

                                            

                                        </ul>

                                        @if(isset($elem->director))
                                            <div class="itemDetail__param _mt5">
                                                <p>режиссер:</p>
                                                <p>{{ $elem->director }}</p>
                                            </div>
                                        @endif
                                        @if(isset($elem->genre))
                                            <div class="itemDetail__param _mt5">
                                                <p>жанр:</p>
                                                <p>{{ $elem->genre }}</p>
                                            </div>
                                        @endif
                                        @if(count($elem->cast) > 0)
                                            <div class="itemDetail__param _mt5 _flexWrap">
                                                <p>в&nbsp;ролях:</p>
                                                <ul>
                                                    @foreach ($elem->cast as $name)
                                                        <li>{{ $name }},</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>

                                    

                                    @if(isset($elem->description))
                                        <div class="itemDetail__text">
                                            <p>{!! $elem->description !!}</p>
                                        </div>
                                    @endif

                                </div>
                            </section>
                        @endforeach


                </div>
                
            @else
                <p class="catElemsListWrap__title _mt20">Нет элементов привязанных к подборке</p>
            @endif
        </div>
{{-- 

        <div class="_mt25">
            <button class="button_1 deleteElem_js" data-id="{{ $collection->id }}" data-title="Удалить: <span class='_bold'>{{ $collection->title }}</span>" onclick="show_popup('confirmDelete')">Удалить</button>
            <button class="button_1" onclick="show_popup('catList')">Выбрать категорию</button>
            <button class="button_1" type="submit" form="editForm">Сохранить</button>
        </div>

        <form id="editForm" class="form_1 _maxW700 _mt30" action="{{ route('admin.collections.update', $collection) }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
            @csrf
            @method('PUT')

--}}
            @php
                // $category = $categories->find($collection->category_id);

                // if(isset($category)){
                //     if($category->slug === 'films'){
                //         $order = $collection->films;
                //         $title = 'Фильмы';
                //     }else if($category->slug === 'games') {
                //         $order = $collection->games;
                //         $title = 'Игры';
                //     }
                // }

                // $order = $collection->games;
                // $order = $collection['games']->games;

                // dd($collection);
                // dd($collection->relations);

                // $order = 0;
                // foreach ($collection->getRelations() as $val) {
                //     // dd($elem[0]->title);
                //     // $order = $elem[0];
                //     // dd($val[0]);
                //     // $i++;
                // }

                // for($i = 0; $collection->getRelations() > $i; $i++){
                //     $val = $collection->getRelations();
                //     dd($val[$i]);
                // }
                // dd($category);

                // $order = [1,2];

            @endphp
            
            
            
            {{-- <div class="popupBlock" data-flag="catList">
                <div class="popupConfirm popupItem">
                    <p>Прежде чем сменить категорию, отвяжите все элементы от подборки</p>
                    <div>
                        <span class="button_1 _big" onclick="close_popup(this)">ОК</span>
                    </div>
                </div>
            </div> --}}
            


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
