{{-- так же у компоненты есть параметры, все что мы там напишем не поподет в $attributes --}}
{{-- @props(['notError' => false, 'value' => '', 'required' => false, 'type' => 'text', 'autofocus' => false, 'placeholder' => '']) по умолчанию false --}}

@php
    // dd(request()->fullUrl())
    // dd(urlencode(request()->fullUrl()))
@endphp

<div class="socialList">
    <a href="https://t.me/share/url?url=https://example.com" class="socialElem _seTel" target="_blank"><svg><use xlink:href="#telegram"/></svg></a>
    <a href="#" class="socialElem" target="_blank"><svg><use xlink:href="#odnoklassniki"/></svg></a>
    <a href="https://vk.com/share.php?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="socialElem _seVk"><svg><use xlink:href="#vk"/></svg></a>
</div>