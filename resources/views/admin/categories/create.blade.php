@extends('layouts.base')

@section('page.title', 'Создать категорию')



@section('content')


<section class="sectionAdmin _section">
    <div class="contain">

        <h1 class="title_1">Создание категории</h1>
        <a class="ref_1 _mt10" href="{{ route('admin.categories.index') }}">Назад</a>

        <div class="_mt25">
            {{-- <button class="button_1 deleteElem_js" value="{{ $film->id }}" data-title="{{ $film->title }}" onclick="show_popup('confirmDelete')">Удалить</button> --}}
            <button class="button_1" type="submit" form="editForm">Сохранить</button>
        </div>

        <form id="editForm" class="form_1 _maxW700 _mt30" action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="on">
            @csrf
            <x-admin.input type="text" name="title" title="Название" required autofocus/>
            <x-admin.input class="_mt20" type="text" name="slug" title="Слаг" required/>

            {{-- <x-admin.input id="add_img" type="file" name="img" title="Загрузить изображение" class="_mt20"/> --}}
            {{-- <x-admin.input type="text" title="Содержание поста" name="description" class="_mt20"> --}}
                {{-- <x-slot name="trix"></x-slot> можно дополнять компоненту слотом --}}
            {{-- </x-admin.input> --}}
            {{-- <x-admin.input type="text" name="resource_id" title="id фильмов или других элементов (через запятую)" class="_mt20"/> --}}
            <x-admin.input type="text" name="published_at" title="Дата публикации" class="_mt20"/>
            {{-- <x-admin.checkbox name="published" value="1" class="_mt20" checked>Опубликовано</x-admin.checkbox> --}}

            <button class="button_1 _big" type="submit">Сохранить</button>
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
