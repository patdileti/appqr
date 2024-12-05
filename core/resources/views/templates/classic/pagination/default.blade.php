@if ($paginator->hasPages())
    <div class="pagination-container margin-top-40 margin-bottom-60">
        <nav class="pagination">
            <ul>
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="pagination-arrow"><a href="#" class="ripple-effect" aria-label="@lang('pagination.previous')"><i class="icon-material-outline-keyboard-arrow-left"></i></a></li>
                @else
                    <li class="pagination-arrow"><a href="{{ $paginator->previousPageUrl() }}" class="ripple-effect" aria-label="@lang('pagination.previous')"><i class="icon-material-outline-keyboard-arrow-left"></i></a></li>
                @endif
                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li><span>{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li><a href="#" class="current-page ripple-effect">{{ $page }}</a></li>
                            @else
                                <li><a href="{{ $url }}" class="ripple-effect">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="pagination-arrow"><a href="{{ $paginator->nextPageUrl() }}" class="ripple-effect" aria-label="@lang('pagination.next')"><i class="icon-material-outline-keyboard-arrow-right"></i></a></li>
                @else
                    <li class="pagination-arrow"><a href="#" class="ripple-effect" aria-label="@lang('pagination.next')"><i class="icon-material-outline-keyboard-arrow-right"></i></a></li>

                @endif
            </ul>
        </nav>
    </div>
@endif
