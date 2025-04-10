@unless ($breadcrumbs->isEmpty())
    <div class="bg-white lg:bg-gray-100 px-4 mt-0 lg:mt-3 rounded-lg">
        <h2 class="text-[10px] sm:text-sm md:text-lg font-semibold text-gray-800 dark:text-white whitespace-nowrap">
            {{ $breadcrumbs->last()->title }}
        </h2>

        <nav class="flex items-center overflow-x-auto whitespace-nowrap" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                @foreach ($breadcrumbs as $index => $breadcrumb)
                    @php
                        $isFirst = $loop->first;
                        $isLast = $loop->last;
                        $showOnMobile = $isFirst || $isLast;
                    @endphp

                    {{-- Middle breadcrumb (collapsed on mobile) --}}
                    @if (!$showOnMobile)
                        {{-- Only show on medium and above --}}
                        <li class="hidden md:inline-flex items-center">
                            @if ($breadcrumb->url)
                                <a href="{{ $breadcrumb->url }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                    {{ $breadcrumb->title }}
                                </a>
                            @else
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ $breadcrumb->title }}
                                </span>
                            @endif
                        </li>

                        {{-- Divider --}}
                        <li class="hidden md:block">
                            <svg class="w-2 h-2 text-gray-400 mx-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                        </li>
                    @endif

                    {{-- First and last breadcrumb shown on all screens --}}
                    @if ($showOnMobile)
                        <li class="inline-flex items-center">
                            @if ($breadcrumb->url && !$isLast)
                                <a href="{{ $breadcrumb->url }}" class="text-[7px] md:text-xs font-medium text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                    {{ $breadcrumb->title }}
                                </a>
                            @else
                                <span class="text-[7px] md:text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ $breadcrumb->title }}
                                </span>
                            @endif
                        </li>

                        @unless ($isLast)
                            <li>
                                <svg class="w-2 h-2 text-gray-400 mx-[2px] md:mx-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                            </li>
                        @endunless
                    @endif

                    {{-- Mobile: Show ellipsis if current is the second item (after first) --}}
                    @if ($index === 1 && count($breadcrumbs) > 2)
                        {{-- Ellipsis untuk mobile --}}
                        <li class="inline-flex items-center md:hidden">
                            <span class="text-[7px] text-gray-500 dark:text-gray-400">...</span>
                            {{-- Tambahkan separator di mobile setelah ... --}}
                            <svg class="w-2 h-2 text-gray-400 mx-[2px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
@endunless
