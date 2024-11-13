@extends('layouts.base')

@section('page.title')
    Админ фильмы
@endsection


@section('content')

<section class="sectionAdmin _section">
    <div class="contain">
        
        <div class="titleItem">
            <h1 class="title_1">Редактировать данные <span class="_color">{{ $user->name }}</span></h1>
            <button class="button_1" type="submit" form="formAdminSetting">Сохранить</button>
        </div>

        <a class="ref_1 _mt10" href="{{ route('admin.index') }}">Назад</a>

        <form id="formAdminSetting" class="formAdminSetting" action="{{ route('admin.settings.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="editItem">
                <div class="editItem__inner">
                    <p>{{ $user->name }}</p>
                    {{-- <input class="input_1" type="text" name="name" value="{{ $user->name }}"> --}}
                    <x-admin.input type="text" name="name" value="{{ $user->name }}"/>

                </div>
                <span class="editItem__button_js button_1" data-active="false">изменить</span>
            </div>
            <div class="editItem">
                <div class="editItem__inner">
                    <p>{{ $user->email }}</p>
                    <x-admin.input type="text" name="email" value="{{ $user->email }}"/>
                    {{-- <input class="input_1" type="text" name="email" value="{{ $user->email }}"> --}}
                </div>
                <span class="editItem__button_js button_1" data-active="false">изменить</span>
            </div>

            
            @php
                $error = session()->pull('error');
                $errorInput = ($error !== null);
                // dump(old('password_new'));
                // dump(bcrypt(123123123));

            @endphp
            <div class="passwordEditItem">
                <p class=""><span class="_bold">Изменить пароль:</span></p>
                <x-admin.input type="text" name="password_old" placeholder="Ведите текущий пароль" class="{{ $errorInput ? '_error' : '' }}"/>
                @if($error)
                    <p class="inputText _red">{{ $error }}</p>
                @endif
                <div class="passwordEditItem__new">
                    <x-admin.input type="text" name="password_new" placeholder="Ведите новый пароль" />
                    <x-admin.input type="text" name="password_new_confirmation" placeholder="Повторите новый пароль" />
                </div>

            </div>

            {{-- <p>{{ $user->password }}</p> --}}
            {{-- <p>{{ $user->email }}</p> --}}
        </form>


    </div>
</section>



<script>
    const buttons = document.querySelectorAll('.editItem__button_js')

    console.log(buttons)

    buttons.forEach(button => {
        button.addEventListener('click', function(){
            const ths = this
            const input = ths.previousElementSibling.querySelector('input')
            const text = ths.previousElementSibling.querySelector('p')
            console.log(ths.dataset.active)
            let active = ths.dataset.active
            console.log(input.value.length)

            // console.log(p.textContent)
            if(active === 'false'){
                ths.dataset.active = 'true'
                ths.textContent = 'ОК'
                text.style.display = 'none'
                input.style.display = 'block'
                input.focus()
            }else{
                ths.dataset.active = 'false'
                ths.textContent = 'Изменить'
                if(input.value.length <= 0){
                    input.value = '{{ $user->email }}'
                }
                text.textContent = input.value
                text.style.display = 'block'
                input.style.display = 'none'
                // ths.previousElementSibling.querySelector('input').focus()
            }
        })
    });
</script>


@endsection
