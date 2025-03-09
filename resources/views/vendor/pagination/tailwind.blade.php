@if ($paginator->hasPages())
    <div class="flex flex-wrap items-center justify-between gap-4">
        <nav class="flex items-center space-x-2" role="navigation" aria-label="{!! __('Pagination Navigation') !!}">
            
            {{-- Tombol "Previous" --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                    </svg>
                </a>
            @endif

            {{-- Nomor Halaman --}}
            <ul class="flex items-center space-x-1 text-sm font-medium">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="px-3 py-2 text-gray-500 bg-gray-100 border border-gray-300 rounded-md">{{ $element }}</li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="px-3 py-2 font-semibold text-white bg-blue-500 border border-blue-500 rounded-md">
                                    {{ $page }}
                                </li>
                            @else
                                <li>
                                    <a href="{{ $url }}" class="px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-200">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>

            {{-- Tombol "Next" --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                    </svg>
                </a>
            @else
                <span class="px-3 py-2 text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                    </svg>
                </span>
            @endif
        </nav>
    </div>
@endif
