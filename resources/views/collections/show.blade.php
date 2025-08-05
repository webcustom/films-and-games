@extends('layouts.base')

@section('page.title', $collection->title)




@section('content')


<section class="sectionPage _section">
    <div class="contain">
        <div class="itemDetail _mw800">
            

            {{-- @if(isset($collection->img_medium)) --}}
                <div class="itemDetail__img">
                    <img class="lazyImg" data-src="/{{ $collection->img_medium }}" alt="img">
                    <h1 class="titleAbsolute"><span>{{ $collection->title }}</span></h1>
                </div>
            {{-- @endif --}}
            <div class="itemDetail__middle">
                <a class="ref_1" href="{{ url()->previous() }}">Назад</a>
                <p class="dateItem">дата публикации: {{ $formattedDate }}</p>
            </div>


            {{-- <div class="socialList">
                <a href="https://t.me/share/url?url=https://example.com" class="socialElem _seTel" target="_blank"><svg><use xlink:href="#telegram"/></svg></a>
                <a href="#" class="socialElem" target="_blank"><svg><use xlink:href="#odnoklassniki"/></svg></a>
                <a href="https://vk.com/share.php?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="socialElem _seVk"><svg><use xlink:href="#vk"/></svg></a>
            </div> --}}


            {{-- шеринг в соцсети --}}
            <x-share/>
            

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


        </div>
    </div>

    @if(isset($order) && count($order) > 0 )

        <div class="sectionsDetailWrap _mt20">
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
                <section class="sectionDetail">
                    {{-- <div class="sectionDetail__inner"> --}}
                    <div class="contain">
                        <div class="itemDetail _mw800">

                            <div class="doubleTop">
                                <h1 class="title_2">{{ $elem['title'] }} <span class="date">({{ $elem->release }})</span></h1>
                                {{-- <p class="sectionDetail__date">{{ $elem->release }}</p> --}}
                            </div>
                            @if(isset($elem->img_medium))
                                <div class="itemDetail__img">
                                    <img class="lazyImg" data-src="/{{ $elem->img_medium }}" alt="img">
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

                                    {{-- @if(isset($elem->release))
                                        <li class="itemDetail__param">
                                            <p>релиз:</p>
                                            <p>{{ $elem->release }}</p>
                                        </li>
                                    @endif --}}

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

                                    @if(isset($elem->maker))
                                        <li class="itemDetail__param">
                                            <p>Разработчик:</p>
                                            <p>{{ $elem->maker }}</p>
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
                                @if(isset($elem->cast) && count($elem->cast) > 0)
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
                                    <p>{{ $elem->description }}</p>
                                </div>
                            @endif

                        </div>
                        {{-- itemDetail --}}

                    </div>
                </section>


            @endforeach


        </div>
        
    @else
        <p class="notElems _mt20">Нет элементов привязанных к подборке</p>
    @endif


</section>



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
