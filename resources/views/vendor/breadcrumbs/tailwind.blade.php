@unless ($breadcrumbs->isEmpty())
    <div class="bg-white lg:bg-gray-100 px-4 mt-0 lg:mt-3 rounded-lg ">
        <h2 class="text-md md:text-lg font-semibold text-gray-800 dark:text-white">
            {{ $breadcrumbs->last()->title }}
        </h2>

        <nav class="flex items-center" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="inline-flex items-center">
                        @if ($breadcrumb->url && !$loop->last)
                            <a href="{{ $breadcrumb->url }}" class="text-[9px] md:text-xs font-medium text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                {{ $breadcrumb->title }}
                            </a>
                        @else
                            <span class="text-[9px] md:text-xs font-medium text-gray-500 dark:text-gray-400">
                                {{ $breadcrumb->title }}
                            </span>
                        @endif
                    </li>

                    @unless($loop->last)
                        <li>
                            <svg class="w-2 h-2 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                        </li>
                    @endunless
                @endforeach
            </ol>
        </nav>
    </div>
@endunless
