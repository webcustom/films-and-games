{{-- по умолчанию false --}}
@props(['radio' => false, 'elems' => [], 'order' => null, 'collections_ids' => null, 'category_id' => null ])

{{-- {{ dd($order) }} --}}
{{-- {{ dd(isset($order)) }} --}}
{{-- {{ dd($attributes->get('first_li')) }} --}}
{{-- {{ dd($ids->toArray()) }} --}}
{{-- {{ dd($category) }} --}}
{{-- {{ dd(isset($attributes->get('ids'))) }} --}}


{{-- компонент принимает массив с категориями и массив с id категорий в которых состоит наш элемент --}}
<div class="popupBlock" data-flag="catList">
    <div class="popupConfirm popupItem">
        @if(isset($order) && count($order) > 0 )
            <p class="_fz14 _red">Прежде чем сменить категорию, <br> отвяжите все элементы от подборки</p>
        @endif
        <div class="listElems_1 _mt20 _mb20 {{ (isset($order) && count($order) > 0 ) ? '_disable' : '' }}">
            <ul>
                @if($radio)
                    <li>
                        <x-admin.checkbox type="{{ ($radio) ? 'radio' : 'checkbox' }}" name="category_id" value="" checked="{{ (!isset($elem->id)) ? true : false }}">Нет категории</x-admin.checkbox>
                    </li>
                @endif
                @foreach ($elems as $elem)
                    @php
                        if(isset($category_id)){
                            $checked = ($elem->id === (int)$category_id) ? true : false;
                        }else{
                            $checked = (isset($collections_ids) && (in_array($elem->id, $collections_ids))) ? true : false;
                        }
                    @endphp
                    <li>
                        <x-admin.checkbox type="{{ ($radio) ? 'radio' : 'checkbox' }}" name="{{ ($radio) ? 'category_id' : 'collections[]' }}" value="{{ ($radio) ? $elem->id : $elem->slug }}" checked="{{ $checked }}">{{ $elem->title }}</x-admin.checkbox>
                    </li>
                @endforeach
            </ul>
        </div>
        <div>
            <span class="button_1 _big" onclick="close_popup()">ОК</span>
        </div>
    </div>
</div>