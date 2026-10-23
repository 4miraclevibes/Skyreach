@if ($paginator->hasPages())
    <nav class="pages" aria-label="Paginasi">
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page dots">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page is-active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="page" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
    </nav>
@endif
