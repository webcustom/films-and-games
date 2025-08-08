@extends('layouts.base')

@section('page.title', 'Создать пост')



@section('content')


<section class="sectionAdmin _section">
    <div class="contain">

        <h1 class="title_1">Создание Игры</h1>
        <a class="ref_1 _mt10" href="{{ route('admin.games.index') }}">Назад</a>

        <div class="_mt25">
            {{-- <button class="button_1 deleteElem_js" value="{{ $film->id }}" data-title="{{ $film->title }}" onclick="show_popup('confirmDelete')">Удалить</button> --}}
            <button class="button_1"onclick="show_popup('catList')">Выбрать коллекцию</button>
            <button class="button_1" type="submit" form="editForm">Сохранить</button>
        </div>

        {{-- {{ dd($collections) }} --}}
        
        <form class="form_1 _maxW800 _mt30" id="editForm" action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="on">
            @csrf


            <x-admin.categoryPopup :elems="$collections"></x-admin.categoryPopup>


            {{-- <div class="popupBlock" data-flag="collectonsList">
                <div class="popupConfirm popupItem">
                    <div class="listElems_1 _mt20 _mb20">
                        <ul>
                            @foreach ($collections as $collection)
                                <li>
                                    <x-admin.checkbox name="collections[]" value="{{ $collection->slug }}">{{ $collection->title }}</x-admin.checkbox>
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

            {{-- <p>Добавить изображение</p> --}}
            <div class="_mt30">
                <div class="additionImgsList"></div>
                @foreach ($errors->get('additional_imgs*') as $key => $error)
                    <p class="inputAlert _red">{{ $error[0] }}</p>
                @endforeach
                <p class="button_1 _mt20" id="additionalImg_js">Добавить изображение</p>
            </div>

            <x-admin.input type="text" title="iframe с видeо" name="iframe_video" class="_mt20" textarea/>

            <x-admin.input type="text" title="Содержание поста" name="description" class="_mt20">
                <x-slot name="trix"></x-slot> {{-- можно дополнять компоненту слотом --}}
            </x-admin.input>
            <x-admin.input type="text" name="release" title="Дата релиза" class="_mt20"/>
            <x-admin.input type="text" name="genre" title="Жанр" class="_mt20"/>
            <x-admin.input type="text" name="budget" title="Бюджет" class="_mt20"/>
            <x-admin.input type="text" name="maker" title="Разработчик" class="_mt20"/>
            <x-admin.input type="text" name="published_at" title="Дата публикации" class="_mt20"/>
            {{-- <x-admin.checkbox name="published" value="1" class="_mt20" checked>Опубликовано</x-admin.checkbox> --}}

            <button class="button_1 _big" type="submit">Сохранить</button>
        </form>

    </div>
</section>

<script>
    deleteEditImg()
    addImgsCreateInput()
</script>

@endsection


{{-- @stack и @push() - позволяет подгружать скрипты или стили именно на тех страницах где они нужны --}}
{{-- @once позволяет все что внутри вставить лишь один раз, иначе эти скрипты и стили подключатся столько раз сколько мы поместим редакторов trix на странице --}}
@once
    @push('js_admin')
        <script src="/admin/js/main.js" defer></script>
        <script src="/admin/js/deleteEditImg.js"></script>
        <script src="/admin/js/controlsAdditionalImgs.js"></script>
    @endpush
@endonce


