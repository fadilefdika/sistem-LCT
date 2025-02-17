<div class="min-w-fit">
    <!-- Sidebar backdrop (mobile only) -->
    <div
        class="fixed inset-0 bg-gray-900/30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'"
        aria-hidden="true"
        x-cloak
    ></div>

    <!-- Sidebar -->
    <div
        id="sidebar"
        class="flex lg:flex! flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-[100dvh] overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:w-64! shrink-0 bg-white dark:bg-gray-800 p-4 transition-all duration-200 ease-in-out {{ $variant === 'v2' ? 'border-r border-gray-200 dark:border-gray-700/60' : 'rounded-r-2xl shadow-xs' }}"
        :class="sidebarOpen ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-64'"
        @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false"
    >

        <!-- Sidebar header -->
        <div class="flex justify-between mb-10 pr-3 sm:px-2">
            <!-- Close button -->
            <button class="lg:hidden text-gray-500 hover:text-gray-400" @click.stop="sidebarOpen = !sidebarOpen" aria-controls="sidebar" :aria-expanded="sidebarOpen">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
                </svg>
            </button>
            <!-- Logo -->
            <a class="block" href="{{ route('admin.dashboard') }}">
                <svg class="fill-violet-500" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
                    <path d="M31.956 14.8C31.372 6.92 25.08.628 17.2.044V5.76a9.04 9.04 0 0 0 9.04 9.04h5.716ZM14.8 26.24v5.716C6.92 31.372.63 25.08.044 17.2H5.76a9.04 9.04 0 0 1 9.04 9.04Zm11.44-9.04h5.716c-.584 7.88-6.876 14.172-14.756 14.756V26.24a9.04 9.04 0 0 1 9.04-9.04ZM.044 14.8C.63 6.92 6.92.628 14.8.044V5.76a9.04 9.04 0 0 1-9.04 9.04H.044Z" />
                </svg>                
            </a>
        </div>

        <!-- Links -->
        <div class="space-y-8">
            <!-- Pages group -->
            <div>
                <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
                    <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
                    <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Pages</span>
                </h3>
                <ul class="mt-3 flex flex-col gap-2">
                    <!-- Dashboard -->
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('dashboard')){{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }}@endif">
                        <a class="block text-gray-800 dark:text-gray-100 truncate transition @if(!Request::is('dashboard')){{ 'hover:text-gray-900 dark:hover:text-white' }}@endif" href="{{ route('admin.dashboard') }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="shrink-0 fill-current @if(Request::is('dashboard')){{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                        <path d="M5.936.278A7.983 7.983 0 0 1 8 0a8 8 0 1 1-8 8c0-.722.104-1.413.278-2.064a1 1 0 1 1 1.932.516A5.99 5.99 0 0 0 2 8a6 6 0 1 0 6-6c-.53 0-1.045.076-1.548.21A1 1 0 1 1 5.936.278Z" />
                                        <path d="M6.068 7.482A2.003 2.003 0 0 0 8 10a2 2 0 1 0-.518-3.932L3.707 2.293a1 1 0 0 0-1.414 1.414l3.775 3.775Z" />
                                    </svg>
                                    <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Dashboard</span>
                                </div>
                            </div>
                        </a>
                    </li>

                    <!-- Laporan LCT -->
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('laporan-lct')){{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }}@endif">
                        <a class="block text-gray-800 dark:text-gray-100 truncate transition @if(!Request::is('laporan-lct')){{ 'hover:text-gray-900 dark:hover:text-white' }}@endif" href="{{ route('admin.laporan-lct') }}">
                            <div class="flex items-center">
                                <svg class="shrink-0 fill-current @if(Request::is('laporan-lct')){{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8 11h8v2H8v-2zm0 4h8v2H8v-2z"/>
                                </svg>
                                
                                <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Laporan LCT</span>
                            </div>
                        </a>
                    </li>

                    <!-- Progress Perbaikan -->
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('progress-perbaikan')){{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }}@endif">
                        <a class="block text-gray-800 dark:text-gray-100 truncate transition @if(!Request::is('progress-perbaikan')){{ 'hover:text-gray-900 dark:hover:text-white' }}@endif" href="{{ route('admin.progress-perbaikan') }}">
                            <div class="flex items-center">
                                <svg class="shrink-0 fill-current @if(Request::is('progress-perbaikan')){{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M22.7 19.3l-3.3-3.3c1.2-2.4.8-5.4-1.2-7.4-2.2-2.2-5.5-2.5-8-1l3 3c.4.4.4 1 0 1.4l-3.2 3.2c-.4.4-1 .4-1.4 0l-3-3c-1.5 2.5-1.2 5.8 1 8 2 2 5 2.4 7.4 1l3.3 3.3c.4.4 1 .4 1.4 0l3.1-3.1c.4-.4.4-1 0-1.4z"/>
                                </svg>                                
                                <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Progress Perbaikan</span>
                            </div>
                        </a>
                    </li>

                    <!-- Riwayat LCT -->
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('riwayat-lct')){{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }}@endif">
                        <a class="block text-gray-800 dark:text-gray-100 truncate transition @if(!Request::is('riwayat-lct')){{ 'hover:text-gray-900 dark:hover:text-white' }}@endif" href="{{ route('admin.riwayat-lct') }}">
                            <div class="flex items-center">
                                <svg class="shrink-0 fill-current @if(Request::is('riwayat-lct')){{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M12 8v5h5v-2h-3V8h-2zM12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/>
                                </svg>                                
                                <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Riwayat LCT</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
