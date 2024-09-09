

<div class="adminItem {{ !$elem->published ? '_notPublished' : '' }}">
    <button type="button" class="closeIcon deleteElem_js" onclick="show_popup('confirmDelete')" data-id="{{ $elem->id }}" data-title="Удалить: <span class='_bold'>{{ $elem->title }}</span>?"><svg><use xlink:href="#close"/></svg></button>
    <x-admin.checkbox name="{{ $elem->id }}" value="1" class="_t2 itemCheckbox_js"></x-admin.checkbox>
    <a href="{{ $attributes->get('route') }}">
        <div class="adminItem__img">
            @if(isset($elem->img))
                <img class="lazyImg" data-src="/{{ $elem->img_thumbnail }}" alt="img">
            @else
                {{-- <img class="noImg" src="/img/no_photo.svg" alt="img"> --}}
                <svg><use xlink:href="#noImg"/></svg>
            @endif
        </div>
        <div class="adminItem__content">
            <p class="adminItem__title"><strong>{{ $elem->title }}</strong><span>{{ $elem->release ? "(".$elem->release.")" : '' }}</span></p>
            <div class="adminItem__bottom">
                <p class="adminItem__info">{{ $elem->id }}</p>
                <span class="adminItem__date">
                    {{ $elem->published_at?->format('d.m.y') }}
                    {{-- diffForHumans - позволяет выводить дату в формате: '9 месяцев назад'  --}}
                    {{-- {{ $post->published_at->diffForHumans() }} --}}
                </span>
                        
                {{-- @foreach ($elem->collections as $collection) --}}
                    {{-- {{ dd($elem) }} --}}
                    {{-- <p>{{ $collection['title'] }}</p> --}}
                {{-- @endforeach --}}
                {{-- {{ dump($elem->collection) }} --}}
                {{-- @if(isset($elem->collection))
                    <p>{{ $elem->collection['title'] }}</p>
                @endif --}}
            </div>
        </div>
    </a>
</div>


    