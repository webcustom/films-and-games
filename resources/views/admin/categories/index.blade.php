@extends('layouts.base')

@section('page.title')
    Админ фильмы
@endsection


@section('content')

<section class="sectionAdmin _section">
    <div class="contain">
        
        <div class="titleItem">
            <h1 class="title_1">Все категории</h1>
            <a href="{{ route('admin.categories.create') }}" class="button_1">Добавить категорию</a>
        </div>




        <x-admin.search class="_mt30" route="{{ route('admin.categories.index') }}"></x-admin>



        @if($categories->isEmpty())
            <p class="_mt20">Нет не одной категории</p>
        @else
            <div class="adminListThree _mt30">
                @foreach ($categories as $category)
                {{-- @php
                    dump(count($category->collections));
                @endphp --}}
                    <div class="adminItemThree">
                        <button type="button" class="closeIcon deleteElem_js"  onclick="show_popup('confirmDelete')" 
                            data-id="{{ $category->id }}" 
                            data-title="Удалить категорию: <span class='_bold'>{{ $category->title }}</span>" 
                            data-error="{{ (count($category->collections) > 0) ? 'Перед удалением отвяжите от категории подборки' : '' }}">
                            <svg><use xlink:href="#close"/></svg>
                        </button>
                        <a href="{{ route('admin.categories.edit', $category->id) }}">
                            {{-- <div class="adminItemTwo__img">
                                @if(isset($category->img))
                                    <img class="lazyImg" data-src="/{{ $category->img_thumbnail }}" alt="img">
                                @else
                                    <svg><use xlink:href="#noImg"/></svg>
                                @endif
                            </div> --}}
                            <div class="adminItemTwo__content">
                                <p class="adminItemTwo__title"><strong>{{ $category->title }}</strong></p>
                                <div class="adminItemTwo__bottom">
                                    <p class="adminItem__info">Количество привязанных подборок: {{ count($category->collections) }}</p>
                                    <span class="adminItemTwo__date">
                                        {{ $category->published_at?->format('d.m.y') }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            
            <div class="_mt30">{{ $categories->links() }}</div>
        @endif

    </div>
</section>



{{-- подтверждение удаления --}}
@if(isset($category))
    @include('includes.admin._confirmDelete', [/*'text' => 'Удалить категорию',*/ 'route' => route('admin.categories.delete')])
@endif

@endsection



@once
    @push('js_admin')
        <script src="/admin/js/main.js" defer></script>
    @endpush
@endonce