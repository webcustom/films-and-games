@extends('layouts.base')

@section('page.title')
    Админ фильмы
@endsection


@section('content')

<section class="sectionAdmin _section">
    <div class="contain">
        

        <div class="titleItem">
            <h1 class="title_1">Все фильмы</h1>
            <a href="{{ route('admin.films.create') }}" class="button_1">Добавить фильм</a>
        </div>


        <x-admin.search class="_mt30" route="{{ route('admin.films.index') }}"></x-admin>



        <input type="hidden" readonly id="deleteElemsInput_js">
        <button class="button_1 _mt20 deleteCheckedButton_js _disable" onclick="show_popup('confirmDelete')" data-title="Удалить отмеченные фильмы?">Удалить отмеченные фильмы</button>
        
        
        @if($films->isEmpty())
            <p class="_mt20">Нет не одного фильма</p>
        @else


            <div class="adminList _mt30">
                @foreach ($films as $film)
                    <x-admin.elemList :elem="$film" route="{{ route('admin.films.edit', $film->id) }}"></x-admin.elemList>
                @endforeach
            </div>
            {{-- adminList --}}

            {{-- 
                погинация
                для того что бы править шаблон пагинации выполняем комманду php artisan vendor:publish --tag=laravel-pagination 
                которая выносит шаблоны пагинации в рабочую область и помещает их в resources/views/vendor/pagination
            --}}
            <div class="_mt30">{{ $films->links() }}</div>
        @endif



    </div>
</section>

<script>
    // // собираем id всех checked элементов в массив и выводим значения этого массива в input
    // const input = document.getElementById('deleteElemsInput_js')
    // const elems = document.querySelectorAll('.itemCheckbox_js input')
    // const arrChecked = []
    // const buttonDel = document.querySelector('.deleteCheckedButton_js')

    // elems.forEach(element => {
    //     element.addEventListener('change', function(){
    //         const elemId = +this.name
    //         if (this.checked) {
    //             arrChecked.push(elemId)
    //         }else{
    //             let index = arrChecked.indexOf(elemId)
    //             // если элемент найден в массиве, удаляем
    //             if (index !== -1) {
    //                 arrChecked.splice(index, 1);
    //             }else{
    //                 console.error('Элемент с таким id не найден в массиве')
    //             }
    //         }
    //         buttonHide()
           
    //         // записываем значения из массива в input
    //         let values = arrChecked.join(',')
    //         input.value = values
    //     })
    // });

    // function buttonHide(){
    //     if(arrChecked.length === 0){
    //         console.log(arrChecked)
    //         buttonDel.classList.add('_disable')
    //     }else{
    //         buttonDel.classList.remove('_disable')
    //     }
    // }
    // buttonHide()
    checkedListElems()
</script>

{{-- подтверждение удаления --}}
@if(isset($film))
    @include('includes.admin._confirmDelete', [/*'title_1' => 'Удалить фильм?', 'title_2' => 'Удалить отмеченные фильмы?',*/ 'route' => route('admin.films.delete')])
@endif

@endsection



@once
    @push('js_admin')
        <script src="/admin/js/main.js" defer></script>
        <script src="/admin/js/checkedListElems.js"></script>

    @endpush
@endonce