@if ($paginator->hasPages()) <nav> <ul class="pagination justify-content-end mb-0">
       
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage()) 
            <li class="page-item disabled">
                <span class="page-link bg-gray text-dark border border-gray">‹</span>
            </li>
        @else 
            <li class="page-item">
                <a class="page-link bg-light text-primary border border-gray" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled">
                    <span class="page-link bg-transparent text-secondary border border-gray">{{ $element }}</span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active">
                            <span class="page-link bg-primary text-white border border-primary">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link bg-light text-primary border border-gray" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link bg-light text-primary border border-gray" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link bg-gray text-dark border border-gray">›</span>
            </li>
        @endif
    </ul>
</nav>

@endif
