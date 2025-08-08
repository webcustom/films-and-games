@extends('layouts.base')

@section('page.title', 'Создать подборку')



@section('content')


<section class="sectionAdmin _section">
    <div class="contain">

        <h1 class="title_1">Создание подборки</h1>
        <a class="ref_1 _mt10" href="{{ route('admin.collections.index') }}">Назад</a>

        <div class="_mt25">
            {{-- <button class="button_1 deleteElem_js" value="{{ $film->id }}" data-title="{{ $film->title }}" onclick="show_popup('confirmDelete')">Удалить</button> --}}
            <button class="button_1" onclick="show_popup('catList')">Выбрать категорию</button>
            <button class="button_1" type="submit" form="editForm">Сохранить</button>
        </div>

        <form id="editForm" class="form_1 _maxW800 _mt30" action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="on">
            @csrf

            {{-- @php
                $category = $categories->find($collection->category_id);
            @endphp --}}
            
            <x-admin.categoryPopup radio :elems="$categories"></x-admin.categoryPopup>


            {{-- <div class="popupBlock" data-flag="catList">
                <div class="popupConfirm popupItem">
                    <div class="listElems_1 _mt20 _mb20">
                        <ul>
                            <li>
                                <x-admin.checkbox type="radio" name="category_id" value="" checked="{{ (!isset($collection->category_id)) ? true : false }}">Нет категории</x-admin.checkbox>
                            </li>
                            @foreach ($categories as $category)

                                <li>
                                    <x-admin.checkbox type="radio" name="category_id" value="{{ $category->id }}">{{ $category->title }}</x-admin.checkbox>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <span class="button_1 _big" onclick="close_popup(this)">ОК</span>
                    </div>
                </div>
            </div> --}}

            <x-admin.input type="text" name="title" title="Заголовок" required autofocus/>
            <x-admin.input class="_mt20" type="text" name="slug" title="Слаг"/>

            <x-admin.input id="add_img" type="file" name="img" title="Загрузить изображение" class="_mt20"/>
            <x-admin.input type="text" title="Содержание поста" name="description" class="_mt20">
                <x-slot name="trix"></x-slot> {{-- можно дополнять компоненту слотом --}}
            </x-admin.input>
            {{-- <x-admin.input type="text" name="resource_id" title="id фильмов или других элементов (через запятую)" class="_mt20"/> --}}
            <x-admin.input type="text" name="published_at" title="Дата публикации" class="_mt20"/>
            <x-admin.checkbox name="published" value="1" class="_mt20" checked>Опубликовано</x-admin.checkbox>

            <button class="button_1 _big _mt20" type="submit">Сохранить</button>
        </form>


    </div>
</section>

<script>
    deleteEditImg()
</script>
@endsection


{{-- @stack и @push() - позволяет подгружать скрипты или стили именно на тех страницах где они нужны --}}
{{-- @once позволяет все что внутри вставить лишь один раз, иначе эти скрипты и стили подключатся столько раз сколько мы поместим редакторов trix на странице --}}
@once
    @push('js_admin')
        <script src="/admin/js/main.js" defer></script>
        <script src="/admin/js/deleteEditImg.js"></script>
    @endpush
@endonce
