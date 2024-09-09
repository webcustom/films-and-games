{{-- так же у компоненты есть параметры, все что мы там напишем не поподет в $attributes --}}
@props(['notError' => false, 'value' => '', 'required' => false, 'type' => 'text', 'autofocus' => false, 'placeholder' => '']) {{-- по умолчанию false --}}



<div class="searchBlock {{ $attributes->get('class') }}">
    <form class="inputWrap" method="GET" {{ $attributes->get('route') }}>
        <input type="text" class="input_1" name="search" placeholder="Поиск" value="{{ request()->input('search') }}">
        <button class="button_1" type="submit">Искать</button>
    </form>
</div>