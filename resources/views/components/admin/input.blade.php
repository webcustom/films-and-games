{{-- так же у компоненты есть параметры, все что мы там напишем не поподет в $attributes --}}
@props(['notError' => false, 'value' => '', 'required' => false, 'type' => 'text', 'autofocus' => false, 'placeholder' => '', 'textarea' => false]) {{-- по умолчанию false --}}

@php
    $id = Str::uuid();
    $value = str_replace(['&amp;#039;', '&#039;', '&amp;'], ["'", "'", '&'], $value); //для того что бы апострофы не заменялись на &amp;#039;' или '&#039; и &amp; на &

@endphp

<div class="inputWrap_1 {{ $attributes->get('class') }}">
    @if($attributes->get('title') !== null)
        <label class="inputWrap_1__name {{ $required ? '_required' : '' }}">{{ $attributes->get('title') }}</label>    
    @endif

    {{-- {{ 
    dump($value);
    }} --}}
    

    @if(isset($trix))
        {{-- {{ $trix }} --}}
        <input id="{{ $id }}" type="hidden" name="{{ $attributes->get('name') }}" value="{!! $value !!}" placeholder="{{ $attributes->get('placeholder') }}">
        <trix-editor input="{{ $id }}" class="input_1 {{ ($errors->has($attributes->get('name')) && !$notError) ? '_error' : '' }}"></trix-editor>
    @else
        @if($textarea)
            <textarea id="{{ $attributes->get('id') }}" 
                name="{{ $attributes->get('name') }}" 
                class="input_1 {{ ($errors->has($attributes->get('name')) && !$notError) ? '_error' : '' }}" 
                placeholder="{{ $placeholder }}"
                {{ $autofocus ? 'autofocus' : '' }}>{{ (request()->old($attributes->get('name')) ?: $value) }}</textarea>
        @else
            <input id="{{ $attributes->get('id') }}" type="{{ $type }}" 
                name="{{ $attributes->get('name') }}" 
                class="input_1 {{ ($errors->has($attributes->get('name')) && !$notError) ? '_error' : '' }}" 
                value="{{ (request()->old($attributes->get('name')) ?: $value) }}" 
                placeholder="{{ $placeholder }}"
                {{ $autofocus ? 'autofocus' : '' }} >
        @endif
    @endif
    {{-- @if(!empty(trim($slot)))
        {{ $slot }}
    @else
        <input type="{{ ($type ? $attributes->get('type') : '') }}" 
            name="{{ $attributes->get('name') }}" 
            class="input_1 {{ $errors->has($attributes->get('name')) ? '_error' : '' }}" 
            value="{{ (request()->old($attributes->get('name')) ?: $value) }}" 
            autofocus>
    @endif --}}
    
    {{-- в merge можно указать атрибуты по умолчанию он их сливает с теми что в <x-input> --}}
    @if(!$notError && $errors->has($attributes->get('name')))
        <p class="inputAlert _red">{{ $errors->first($attributes->get('name')) }}</p>
    @endif
</div>




{{-- @stack и @push() - позволяет подгружать скрипты или стили именно на тех страницах где они нужны --}}
{{-- @once позволяет все что внутри вставить лишь один раз, иначе эти скрипты и стили подключатся столько раз сколько мы поместим редакторов trix на странице --}}
{{-- @if(!empty(trim($slot))) --}}
@if(isset($trix))
    @once
        @push('css_admin')
        <link rel="stylesheet" href="/admin/libs/trix/trix.css">
        @endpush
        @push('js_admin')
        <script src="/admin/libs/trix/trix.js" defer></script>
        @endpush
    @endonce
@endif