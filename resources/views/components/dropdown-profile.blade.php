@props([
    'align' => 'right'
])

@php
    $user = auth()->user();    
@endphp

<div class="relative inline-flex cursor-pointer" x-data="{ open: false }">
    <button
        class="inline-flex justify-center items-center group"
        aria-haspopup="true"
        @click.prevent="open = !open"
        :aria-expanded="open"                        
    >
        <div class="flex items-center truncate">
            <span class="truncate ml-2 text-xs md:text-sm font-medium text-gray-600 dark:text-gray-100 group-hover:text-gray-800 cursor-pointer dark:group-hover:text-white">{{ $user->fullname ?? 'Admin EHS' }}</span>
            <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500" viewBox="0 0 12 12">
                <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
            </svg>
        </div>
    </button>
    <div
        class="origin-top-right z-10 absolute top-full min-w-44 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 py-1.5 rounded-lg shadow-lg overflow-hidden mt-1 {{$align === 'right' ? 'right-0' : 'left-0'}}"                
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        x-show="open"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak                    
    >
        <div class="pt-0.5 pb-2 px-3 mb-1 border-b border-gray-200 dark:border-gray-700/60">
            <div class="font-medium text-gray-800 dark:text-gray-100">
                {{ $user->fullname ?? 'Admin Ehs' }}
            </div>            
            @php
                $activeRole = session('active_role');
                
                $roleMapping = [
                    'manajer' => 'Manager',
                    'pic' => 'PIC',
                    'user' => 'Employee',
                    'employee' => 'Employee',
                    'ehs' => 'EHS',
                ];

                $displayRole = $roleMapping[strtolower($activeRole)] ?? 'Tidak Ada Role';
            @endphp

            <div class="text-xs text-gray-500 dark:text-gray-400 italic uppercase mt-2">
                {{ $displayRole }}
            </div>
                
            </div>
        <ul>
            <li>
                <form method="POST" action="{{ route('logoutAll') }}" x-data>
                    @csrf

                    <a class="font-medium text-sm text-[#048cfb] flex items-center py-1 px-3"
                        href="{{ route('logoutAll') }}"
                        @click.prevent="$root.submit();"
                        @focus="open = true"
                        @focusout="open = false"
                    >
                        {{ __('Sign Out') }}
                    </a>
                </form>                                
            </li>
        </ul>                
    </div>
</div>