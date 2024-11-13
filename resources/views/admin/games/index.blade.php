@extends('layouts.base')

@section('page.title')
    Админ фильмы
@endsection


@section('content')

<section class="sectionAdmin _section">
    <div class="contain">
        
        <div class="titleItem">
            <h1 class="title_1">Все игры</h1>
            <a href="{{ route('admin.games.create') }}" class="button_1">Добавить Игру</a>
        </div>


        <x-admin.search class="_mt30" route="{{ route('admin.games.index') }}"></x-admin>



        <input type="hidden" readonly id="deleteElemsInput_js">
        <button class="button_1 _mt20 deleteCheckedButton_js _disable" onclick="show_popup('confirmDelete')" data-title="Удалить отмеченные игры?">Удалить отмеченные игры</button>
        
        
        @if($games->isEmpty())
            <p class="_mt20">Нет не одной игры</p>
        @else
            <div class="adminList _mt30">
                @foreach ($games as $game)
                    <x-admin.elemList :elem="$game" route="{{ route('admin.games.edit', $game->id) }}" collections="{{ count($game->collections) }}"></x-admin.elemList>
                @endforeach
            </div>
            {{-- adminList --}}

            {{-- 
                погинация
                для того что бы править шаблон пагинации выполняем комманду php artisan vendor:publish --tag=laravel-pagination 
                которая выносит шаблоны пагинации в рабочую область и помещает их в resources/views/vendor/pagination
            --}}
            <div class="_mt30">{{ $games->links() }}</div>
        @endif



    </div>
</section>

<script>
    checkedListElems()
</script>

{{-- подтверждение удаления --}}
@if(isset($game))
    @include('includes.admin._confirmDelete', [/*'title_1' => 'Удалить фильм?', 'title_2' => 'Удалить отмеченные фильмы?',*/ 'route' => route('admin.games.delete')])
@endif

@endsection



@once
    @push('js_admin')
        <script src="/admin/js/main.js" defer></script>
        <script src="/admin/js/checkedListElems.js"></script>

    @endpush
@endonce