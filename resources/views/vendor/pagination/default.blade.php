<!-- Custom pagination view using Tailwind -->
@if ($paginator->hasPages())
    <nav class="blog-pagination">
        <div class="previous-container">
            @if ($paginator->onFirstPage())
                <span class="if-not-previous previous">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="if-previous previous">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif 
        </div>

        <div class="currentPage-container">
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="ifCurrentPage page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <div class="next-container">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="ifNotNextPage nextPage">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span class="ifNextPage nextPage">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>

    <div class="pagination-info">
        <div>
            <p class="text-sm text-gray-700">
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            </p>
        </div>
    </div>
@endif