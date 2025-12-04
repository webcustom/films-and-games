@extends('layouts.base')

@section('page.title')
    Админ фильмы
@endsection


@section('content')

<section class="sectionAdmin _section">
    <div class="contain">
        
        <div class="titleItem">
            <h1 class="title_1">Все подборки</h1>
            <a href="{{ route('admin.collections.create') }}" class="button_1">Добавить подборку</a>
        </div>


        <x-admin.search class="_mt30" route="{{ route('admin.collections.index') }}"></x-admin>
 
        @if(count($categories) > 0)
            <div class="selectionFilter _mt20">
                <form id="selectionByCat__form" method="GET" action="{{ route('admin.collections.index') }}">
                    @foreach ($categories as $category)
                        @php
                            $id = Str::uuid();
                            $selectedCategory = request('selectionByCat'); //получаем наше выбранное значение
                        @endphp
                        <div class="inputButton">
                            <input id="{{ $id }}" name="selectionByCat" type="radio" value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'checked' : '' }}>
                            <label for="{{ $id }}" class="selectionByCat__button {{ $selectedCategory == $category->id ? '_active' : '' }}">{{ $category->title }}</label>
                        </div>
                    @endforeach
                </form>
                <a href="{{ route('admin.collections.index') }}">Сбросить фильтр</a>
            </div>


            <script>
                let buttonsSubmit = document.querySelectorAll('.selectionByCat__button')
                buttonsSubmit.forEach(button => {
                    button.addEventListener('click', function(){
                        requestAnimationFrame(() => { //requestAnimationFrame - гарантирует асинхронность вместо setTimeout(() => {},0)
                            document.getElementById('selectionByCat__form').submit();
                        });
                    })
                });
            </script>
        @endif
       

        @if($collections->isEmpty())
            <p class="_mt20">Нет не одной подборки</p>
        @else
            <div class="adminListTwo _mt30">
                @foreach ($collections as $collection)
                    {{-- {{ dd(count($collection->films)) }} --}}
                    {{-- @php
                        $category = $categories->find($collection->category_id);
                        // $category = $categories->where('id', $collection->category_id)->first();
                        dump($category);
                        // dump(collect($category));
                        // // dump($collection->category_id);
                        // dump(count($collection[$category['slug']]));
                        // dump(isset($category) && is_array($collection[$category['slug']]) && count($collection[$category['slug']]) > 0);
                        // dd(count($collection[$category['slug']]));
                    @endphp --}}
                    <div class="adminItemTwo {{ !$collection->published ? '_notPublished' : '' }} {{ !isset($collection->category) ? '_notCategory' : '' }}">
                        <button type="button" class="closeIcon deleteElem_js"  onclick="show_popup('confirmDelete')" data-id="{{ $collection->id }}" data-title="Удалить подборку: <span class='_bold'>{{ $collection->title }}</span>"><svg><use xlink:href="#close"/></svg></button>
                        <a href="{{ route('admin.collections.edit', $collection->id) }}">
                            <div class="adminItemTwo__img">
                                @if(isset($collection->img_thumbnail))
                                    <img class="lazyImg" data-src="/{{ $collection->img_thumbnail }}" alt="img">
                                    {{-- <img data-src="/storage/{{ $collection->img }}" alt="img"> --}}
                                @else
                                    {{-- <img class="noImg" src="/img/no_photo.svg" alt="img"> --}}
                                    <svg><use xlink:href="#noImg"/></svg>
                                @endif
                            </div>
                            <div class="adminItemTwo__content">
                                @if(!isset($collection->category))
                                    <p class="_fz12">выберите категорию</p>
                                @endif
                                <p class="adminItemTwo__title"><strong>{{ $collection->title }}</strong></p>
                                <div class="adminItemTwo__bottom">
                                    {{-- <p class="adminItemTwo__id">{{ $collection->id }}</p> --}}
                                    <div class="adminItemTwo__bottom_left">
                                        <span class="adminItemTwo__date">
                                            {{ $collection->published_at?->format('d.m.y') }}
                                            {{-- diffForHumans - позволяет выводить дату в формате: '9 месяцев назад'  --}}
                                            {{-- {{ $collections->published_at->diffForHumans() }} --}}
                                        </span>
                                        @if(count($collection->films) > 0)
                                            <p class="linkedItems">Привязано: {{ count($collection->films) }}</p>
                                        @endif
                                        @if(count($collection->games) > 0)
                                            <p class="linkedItems">Привязано: {{ count($collection->games) }}</p>
                                        @endif
                                    </div>

                                    

                                    {{-- @if(isset($category) && count($collection[$category['slug']]) > 0)
                                        <p class="_fz12">{{ count($collection[$category['slug']]) }}</p>
                                    @endif --}}
                                    @if(isset($collection->category['title']))
                                        <p class="_fz12">{{ $collection->category['title'] }}</p>
                                    @endif
                                    
                                </div>
                            </div>
                        </a>
                    </div>
        {{-- {{ dd(isset($collection->category)) }} --}}

                @endforeach
            </div>
            {{-- adminList --}}

            {{-- 
                погинация
                для того что бы править шаблон пагинации выполняем комманду php artisan vendor:publish --tag=laravel-pagination 
                которая выносит шаблоны пагинации в рабочую область и помещает их в resources/views/vendor/pagination
            --}}
            <div class="_mt30">{{ $collections->links() }}</div>
        @endif

    </div>
</section>



{{-- подтверждение удаления --}}
@if(isset($collection))
    @include('includes.admin._confirmDelete', ['route' => route('admin.collections.delete')])
@endif

@endsection



@once
    @push('js_admin')
        <script src="/admin/js/main.js" defer></script>

    @endpush
@endonce