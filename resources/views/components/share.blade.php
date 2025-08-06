{{-- так же у компоненты есть параметры, все что мы там напишем не поподет в $attributes --}}
@props(['link' => false])

{{-- urlencode() -  кодирует строку так, чтобы её безопасно можно было использовать в URL --}}
<div class="socialList">
    <a href="https://t.me/share/url?url={{ urlencode($link) }}" class="socialElem _seTel" target="_blank"><svg><use xlink:href="#telegram"/></svg></a>
    <a href="https://connect.ok.ru/offer?url={{ urlencode($link) }}" class="socialElem" target="_blank"><svg><use xlink:href="#odnoklassniki"/></svg></a>
    <a href="https://vk.com/share.php?url={{ urlencode($link) }}" target="_blank" class="socialElem _seVk"><svg><use xlink:href="#vk"/></svg></a>
</div>