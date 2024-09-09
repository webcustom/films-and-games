{{-- так же у компоненты есть параметры, все что мы там напишем не поподет в $attributes --}}
@props(['name', 'value' => '1', 'required' => false, 'checked' => false, 'type' => 'checkbox']) {{-- по умолчанию false --}}


@php($id = Str::uuid())

<div class="checkbox_1 {{ $attributes->get('class') }}">
    <label for="{{ $id }}" class="checkbox_1__text {{ $required ? '_required' : '' }}">{{ $slot }}</label>
    <div class="checkboxInput">
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" hidden {{ $checked ? 'checked' : false }}>
        <svg><use xlink:href="#check"/></svg>
        <label for="{{ $id }}"></label>
    </div>
</div>
<!-- checkbox_1 -->