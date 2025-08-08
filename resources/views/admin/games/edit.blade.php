@extends('layouts.base')

@section('page.title')
    {{ $game->title }}
@endsection


@section('content')

<section class="sectionAdmin _section">
    <div class="contain">

        <h1 class="title_1">Редактирование игры: {{$game->title}}</h1>
        <a class="ref_1 _mt10" href="{{ route('admin.games.index') }}">Назад</a>

       
        <div class="_mt25">
            <button class="button_1 deleteElem_js" data-id="{{ $game->id }}" data-title="Удалить: <span class='_bold'>{{ $game->title }}</span>?" onclick="show_popup('confirmDelete')">Удалить</button>
            <button class="button_1 {{ (!count($collections) > 0) ? '_disable' : '' }}" onclick="show_popup('catList')">Выбрать коллекцию</button>
            <button class="button_1" type="submit" form="editForm">Сохранить</button>
        </div>




        <form id="editForm" class="form_1 _maxW800 _mt20" action="{{ route('admin.games.update', $game) }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
            @csrf
            @method('PUT')


            @php
                // получаем slugs категорий в которых состоит пост
                $slugsList = $game->collections->pluck('slug')->toArray();
                // находим id категории у которых параметр slug совпадает с тем что приходит в массиве $slugsList
                $collections_ids = $collections->whereIn('slug', $slugsList)->pluck('id')->toArray();
            @endphp
            
            
            <x-admin.categoryPopup :elems="$collections" :collections_ids="$collections_ids"></x-admin.categoryPopup>
            {{-- <div class="popupBlock" data-flag="catList">
                <div class="popupConfirm popupItem">
                    <div class="listElems_1 _mt20 _mb20">
                        <ul>
                            @foreach ($collections as $collection)
                                <li>
                                    <x-admin.checkbox name="collections[]" value="{{ $collection->slug }}" checked="{{ (in_array($collection->id, $ids->toArray())) ? true : false }}">{{ $collection->title }}</x-admin.checkbox>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <span class="button_1 _big" onclick="close_popup(this)">ОК</span>
                    </div>
                </div>
            </div> --}}


            <x-admin.input type="text" name="title" title="Заголовок" required value="{{ $game->title }}"/>
            <x-admin.input class="_mt20" type="text" name="slug" title="Слаг" value="{{ $game->slug }}"/>
            
            @if(isset($game->img_medium))
                <div class="uploadImg _mt20">
                    <img src="/{{ $game->img_medium }}"/>
                    <span class="closeIcon" onclick="deleteInputImg('{{ $game->img_medium }}')"><svg><use xlink:href="#close"/></svg></span>
                </div>
            @endif
            <x-admin.input id="add_img" type="file" name="img" title="Загрузить изображение" class="_mt20"/>
            <input type="hidden" name="delete_img" readonly value="">


            {{-- дополнительные изображения --}}
            <div class="_mt30">
                @php
                    $additional_imgs_arr = json_decode($game->additional_imgs, true);
                @endphp
                <div class="additionImgsList">
                @if(isset($additional_imgs_arr) && count($additional_imgs_arr) > 0)
                    <input type="hidden" name="delete_additional_img" readonly value="">
                        @foreach ($additional_imgs_arr as $key => $imgArr)
                            @php
                                $addImgId = uniqid();
                            @endphp
                            <div class="addImg _uploaded _oldLoad">
                                <div class="addImg__imgWrap">
                                    <div class="addImg__img">
                                        <img src="/{{ $imgArr['image']['thumbnail'] }}"/>
                                    </div>
                                </div>
                                <div class="addImg__content">
                                    <input class="additionalImgInput_js" type="file" name="additional_imgs[]" id="id-{{ $addImgId }}" onchange="showThumbnail(this)">
                                    <input type="text" class="input_1" placeholder="Описание к изображению" name="additional_imgs_text[]" value="{{ $imgArr['text'] }}">
                                    <div class="addImg__bottom">
                                        <label class="button_1" for="id-{{ $addImgId }}">Выбрать файл</label>
                                        <input type="text" class="input_1" placeholder="Сорт-ка" name="additional_imgs_sort[]" value="{{ isset($imgArr['sort']) ? $imgArr['sort'] : null }}">
                                    </div>
                                </div>
                                <span class="closeIcon" data-index="{{ $key }}" onclick="deleteAditionalImg(this)"><svg><use xlink:href="#close"/></svg></span>
                            </div>
                        @endforeach

                    @foreach ($errors->get('additional_imgs*') as $key => $error)
                        <p class="inputAlert _red">{{ $error[0] }}</p>
                    @endforeach
                @endif
                </div>

                <p class="button_1 _mt20" id="additionalImg_js">Добавить изображение</p>
            </div>

            <x-admin.input type="text" title="iframe с видeо" name="iframe_video" class="_mt20" textarea value="{{ $game->iframe_video }}"/>

            <x-admin.input type="text" title="Содержание поста" name="description" class="_mt20" value="{{ strip_tags($game->description) ?? '' }}">
                <x-slot name="trix"></x-slot> {{-- можно дополнять компоненту слотом --}}
            </x-admin.input>
            <x-admin.input type="text" name="release" title="Дата релиза" class="_mt20" value="{{ $game->release }}"/>
            <x-admin.input type="text" name="genre" title="Жанр" class="_mt20" value="{{ $game->genre }}"/>
            <x-admin.input type="text" name="budget" title="Бюджет" class="_mt20" value="{{ $game->budget }}"/>
            <x-admin.input type="text" name="maker" title="Разработчик" class="_mt20" value="{{ $game->maker }}"/>
            @php
                if(!is_null($game->cast)){
                    $cast = implode(', ', $game->cast);
                }else{
                    $cast = null;
                }
            @endphp
            <x-admin.input type="text" name="published_at" title="Дата публикации" class="_mt20" value="{{ $game->published_at }}"/>
            {{-- <x-admin.checkbox name="published" value="1" class="_mt20" checked="{{ $game->published ? 'checked' : ''}}">Опубликовано</x-admin.checkbox> --}}
            <button class="button_1 _big _mt20" type="submit">Сохранить</button>
        </form>

    </div>
</section>



{{-- подтверждение удаления --}}
{{-- @include('includes.admin._confirmDelete', ['text' => 'Вы точно хотите удалить данный фильм?', 'route' => route('admin.films.delete', $film->id)]) --}}

{{-- подтверждение удаления --}}
@include('includes.admin._confirmDelete', [/*'text' => 'Удалить фильм', */'route' => route('admin.games.delete')])



<script>
    deleteEditImg() // скрипты для удаления и редактирования основного изображения
    addImgsCreateInput() // скрипты для управления дополниьельными изображениями
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
