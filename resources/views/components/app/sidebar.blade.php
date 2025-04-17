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
        class="flex lg:flex! flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-[100dvh] overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-auto lg:sidebar-expanded:!w-72 2xl:w-72! shrink-0 bg-white dark:bg-gray-800 p-4 transition-all duration-200 ease-in-out shadow-md {{ $variant === 'v2' ? 'border-r border-gray-200 dark:border-gray-700/60' : 'rounded-r-2xl shadow-xs' }}"
        :class="sidebarOpen ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-64'"
        @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false">

        <!-- Sidebar header -->
        <div class="flex justify-between items-center mb-7 pr-3 sm:px-2">
            <!-- Close button -->
            <button class="lg:hidden text-gray-900 hover:text-gray-400" 
                @click.stop="sidebarOpen = !sidebarOpen" 
                aria-controls="sidebar" 
                :aria-expanded="sidebarOpen">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
                </svg>
            </button>

            <!-- Logo & Judul -->
            <div class="flex flex-col items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="block">
                    <img src="{{ asset('images/LOGO-AVI-OFFICIAL.png') }}" 
                        alt="Logo" 
                        class="w-32 h-10 object-contain">
                </a>
                {{-- <h2 class="text-lg font-semibold text-gray-700">LCT System</h2> --}}
            </div>
        </div>

        <!-- Links -->
        <div class="space-y-8 mb-6">
            <!-- Pages group -->
            <div>
                <h3 class="text-xs uppercase text-gray-400 dark:text-gray-900 font-semibold pl-3">
                    <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
                    <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Pages</span>
                </h3>
                <ul class="mt-3 flex flex-col gap-2">
                    <!-- Dashboard -->
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('dashboard')){{ 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' }}@endif">
                        <a class="block text-gray-700 dark:text-gray-100 truncate transition @if(!Request::is('dashboard')){{ 'hover:text-gray-900 ' }}@endif" href="{{ route('admin.dashboard') }}">
                                <div class="flex items-center">
                                    <svg class="shrink-0 fill-current @if(Request::is('dashboard')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                        <path d="M5.936.278A7.983 7.983 0 0 1 8 0a8 8 0 1 1-8 8c0-.722.104-1.413.278-2.064a1 1 0 1 1 1.932.516A5.99 5.99 0 0 0 2 8a6 6 0 1 0 6-6c-.53 0-1.045.076-1.548.21A1 1 0 1 1 5.936.278Z" />
                                        <path d="M6.068 7.482A2.003 2.003 0 0 0 8 10a2 2 0 1 0-.518-3.932L3.707 2.293a1 1 0 0 0-1.414 1.414l3.775 3.775Z" />
                                    </svg>
                                    <span class="text-xs font-light ml-4 lg:opacity-100 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200 @if(Request::is('dashboard')){{ 'text-gray-900' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif">Dashboard</span>
                                </div>
                        </a>
                    </li>

                    <!-- Laporan LCT -->
                    @hasanyrole(['ehs', 'manajer'])
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('laporan-lct') || Request::is('laporan-lct/*')){{ 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' }}@endif">
                        <a class="block text-gray-700 dark:text-gray-100 truncate transition @if(!Request::is('laporan-lct') || Request::is('laporan-lct/*')){{ 'hover:text-gray-900 ' }} @endif" href="{{ route('admin.laporan-lct.index') }}">
                            <div class="flex items-center">
                                <svg class="shrink-0 fill-current @if(Request::is('laporan-lct') || Request::is('laporan-lct/*')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8 11h8v2H8v-2zm0 4h8v2H8v-2z"/>
                                </svg>
                                
                                <span class="text-xs font-light ml-4 lg:opacity-100 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200 @if(Request::is('laporan-lct') || Request::is('laporan-lct/*')){{ 'text-gray-900' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif">LCT Reports</span>
                            </div>
                        </a>
                    </li>
                    @endrole

                    <!-- Manajemen LCT -->
                    @role('pic')
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('manajemen-lct') || Request::is('manajemen-lct/*')){{ 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' }}@endif">
                        <a class="block text-gray-700 dark:text-gray-100 truncate transition @if(!Request::is('manajemen-lct') || Request::is('manajemen-lct/*')){{ 'hover:text-gray-900 ' }}@endif" href="{{ route('admin.manajemen-lct.index') }}">
                            <div class="flex items-center">
                                <svg class="shrink-0 fill-current @if(Request::is('manajemen-lct') || Request::is('manajemen-lct/*')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8 11h8v2H8v-2zm0 4h8v2H8v-2z"/>
                                </svg>
                                
                                <span class="text-xs font-light ml-4 lg:opacity-100 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200 @if(Request::is('manajemen-lct') || Request::is('manajemen-lct/*')){{ 'text-gray-900' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif">LCT Management</span>
                            </div>
                        </a>
                    </li>
                    @endrole

                    <!-- Pengajuan Anggaran -->
                    @role('manajer')
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('budget-approval') || Request::is('budget-approval/*')){{ 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' }}@endif">
                        <a class="block text-gray-700 dark:text-gray-100 truncate transition @if(!Request::is('budget-approval') || Request::is('budget-approval/*')){{ 'hover:text-gray-900 ' }}@endif" href="{{ route('admin.budget-approval.index') }}">
                            <div class="flex items-center">
                                <svg class="shrink-0 fill-current @if(Request::is('budget-approval') || Request::is('budget-approval/*')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8 11h8v2H8v-2zm0 4h8v2H8v-2z"/>
                                </svg>
                                <span class="text-xs font-light ml-4 lg:opacity-100 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200 @if(Request::is('budget-approval') || Request::is('budget-approval/*')){{ 'text-gray-900' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif">Activity Approval</span>
                            </div>
                        </a>
                    </li>
                    @endrole

                    <!-- Riwayat Anggaran -->
                    @role('manajer')
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('budget-approval-history') || Request::is('budget-approval-history/*')){{ 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' }}@endif">
                        <a class="block text-gray-700 dark:text-gray-100 truncate transition @if(!Request::is('budget-approval-history') || Request::is('budget-approval-history/*')){{ 'hover:text-gray-900 ' }}@endif" href="{{ route('admin.budget-approval-history.index') }}">
                            <div class="flex items-center">
                                <svg class="shrink-0 fill-current @if(Request::is('budget-approval-history') || Request::is('budget-approval-history/*')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8 11h8v2H8v-2zm0 4h8v2H8v-2z"/>
                                </svg>
                                <span class="text-xs font-light ml-4 lg:opacity-100 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200 @if(Request::is('budget-approval-history') || Request::is('budget-approval-history/*')){{ 'text-gray-900' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif">Activity Approval History</span>
                            </div>
                        </a>
                    </li>
                    @endrole

                    <!-- Progress Perbaikan -->
                    @hasanyrole(['ehs', 'manajer','user'])
                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if(Request::is('progress-perbaikan') || Request::is('progress-perbaikan/*')){{ 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' }}@endif">
                        <a class="block text-gray-700 dark:text-gray-100 truncate transition @if(!Request::is('progress-perbaikan') || Request::is('progress-perbaikan/*')){{ 'hover:text-gray-900 ' }}@endif" href="{{ route('admin.progress-perbaikan.index') }}">
                            <div class="flex items-center">
                                <svg class="shrink-0 fill-current @if(Request::is('progress-perbaikan')|| Request::is('progress-perbaikan/*')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M22.7 19.3l-3.3-3.3c1.2-2.4.8-5.4-1.2-7.4-2.2-2.2-5.5-2.5-8-1l3 3c.4.4.4 1 0 1.4l-3.2 3.2c-.4.4-1 .4-1.4 0l-3-3c-1.5 2.5-1.2 5.8 1 8 2 2 5 2.4 7.4 1l3.3 3.3c.4.4 1 .4 1.4 0l3.1-3.1c.4-.4.4-1 0-1.4z"/>
                                </svg>                                
                                <span class="text-xs font-light ml-4 lg:opacity-100 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200 @if(Request::is('progress-perbaikan')|| Request::is('progress-perbaikan/*')){{ 'text-gray-900' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif">Activity Progress</span>
                            </div>
                        </a>
                    </li>
                    @endrole

                    <!-- Master Data -->
                    @hasanyrole('ehs|manajer')
                    @php
                        $isMasterDataActive = Request::is('master-data') || Request::is('master-data/*');
                    @endphp

                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r">
                        <!-- Tombol Dropdown -->
                        <button class="w-full flex justify-between items-center text-gray-700 dark:text-gray-100 
                                hover:text-gray-900 dark:hover:text-gray-300 transition focus:outline-none cursor-pointer"
                                onclick="toggleDropdown(event, 'masterDataMenu', 'iconMasterData')">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="16" height="16" class="shrink-0 fill-current @if(Request::is('master-data')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M144 160A80 80 0 1 0 144 0a80 80 0 1 0 0 160zm368 0A80 80 0 1 0 512 0a80 80 0 1 0 0 160zM0 298.7C0 310.4 9.6 320 21.3 320l213.3 0c.2 0 .4 0 .7 0c-26.6-23.5-43.3-57.8-43.3-96c0-7.6 .7-15 1.9-22.3c-13.6-6.3-28.7-9.7-44.6-9.7l-42.7 0C47.8 192 0 239.8 0 298.7zM320 320c24 0 45.9-8.8 62.7-23.3c2.5-3.7 5.2-7.3 8-10.7c2.7-3.3 5.7-6.1 9-8.3C410 262.3 416 243.9 416 224c0-53-43-96-96-96s-96 43-96 96s43 96 96 96zm65.4 60.2c-10.3-5.9-18.1-16.2-20.8-28.2l-103.2 0C187.7 352 128 411.7 128 485.3c0 14.7 11.9 26.7 26.7 26.7l300.6 0c-2.1-5.2-3.2-10.9-3.2-16.4l0-3c-1.3-.7-2.7-1.5-4-2.3l-2.6 1.5c-16.8 9.7-40.5 8-54.7-9.7c-4.5-5.6-8.6-11.5-12.4-17.6l-.1-.2-.1-.2-2.4-4.1-.1-.2-.1-.2c-3.4-6.2-6.4-12.6-9-19.3c-8.2-21.2 2.2-42.6 19-52.3l2.7-1.5c0-.8 0-1.5 0-2.3s0-1.5 0-2.3l-2.7-1.5zM533.3 192l-42.7 0c-15.9 0-31 3.5-44.6 9.7c1.3 7.2 1.9 14.7 1.9 22.3c0 17.4-3.5 33.9-9.7 49c2.5 .9 4.9 2 7.1 3.3l2.6 1.5c1.3-.8 2.6-1.6 4-2.3l0-3c0-19.4 13.3-39.1 35.8-42.6c7.9-1.2 16-1.9 24.2-1.9s16.3 .6 24.2 1.9c22.5 3.5 35.8 23.2 35.8 42.6l0 3c1.3 .7 2.7 1.5 4 2.3l2.6-1.5c16.8-9.7 40.5-8 54.7 9.7c2.3 2.8 4.5 5.8 6.6 8.7c-2.1-57.1-49-102.7-106.6-102.7zm91.3 163.9c6.3-3.6 9.5-11.1 6.8-18c-2.1-5.5-4.6-10.8-7.4-15.9l-2.3-4c-3.1-5.1-6.5-9.9-10.2-14.5c-4.6-5.7-12.7-6.7-19-3l-2.9 1.7c-9.2 5.3-20.4 4-29.6-1.3s-16.1-14.5-16.1-25.1l0-3.4c0-7.3-4.9-13.8-12.1-14.9c-6.5-1-13.1-1.5-19.9-1.5s-13.4 .5-19.9 1.5c-7.2 1.1-12.1 7.6-12.1 14.9l0 3.4c0 10.6-6.9 19.8-16.1 25.1s-20.4 6.6-29.6 1.3l-2.9-1.7c-6.3-3.6-14.4-2.6-19 3c-3.7 4.6-7.1 9.5-10.2 14.6l-2.3 3.9c-2.8 5.1-5.3 10.4-7.4 15.9c-2.6 6.8 .5 14.3 6.8 17.9l2.9 1.7c9.2 5.3 13.7 15.8 13.7 26.4s-4.5 21.1-13.7 26.4l-3 1.7c-6.3 3.6-9.5 11.1-6.8 17.9c2.1 5.5 4.6 10.7 7.4 15.8l2.4 4.1c3 5.1 6.4 9.9 10.1 14.5c4.6 5.7 12.7 6.7 19 3l2.9-1.7c9.2-5.3 20.4-4 29.6 1.3s16.1 14.5 16.1 25.1l0 3.4c0 7.3 4.9 13.8 12.1 14.9c6.5 1 13.1 1.5 19.9 1.5s13.4-.5 19.9-1.5c7.2-1.1 12.1-7.6 12.1-14.9l0-3.4c0-10.6 6.9-19.8 16.1-25.1s20.4-6.6 29.6-1.3l2.9 1.7c6.3 3.6 14.4 2.6 19-3c3.7-4.6 7.1-9.4 10.1-14.5l2.4-4.2c2.8-5.1 5.3-10.3 7.4-15.8c2.6-6.8-.5-14.3-6.8-17.9l-3-1.7c-9.2-5.3-13.7-15.8-13.7-26.4s4.5-21.1 13.7-26.4l3-1.7zM472 384a40 40 0 1 1 80 0 40 40 0 1 1 -80 0z"/>
                                </svg>
                                <span class="text-xs font-light ml-4 lg:opacity-100 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200 
                                    {{ $isMasterDataActive ? 'text-gray-400' : 'text-gray-400 dark:text-gray-900' }}">
                                    Master Data
                                </span>
                            </div>
                            <svg id="iconMasterData" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="16" height="16" 
                                class="fill-current text-gray-400 transition-transform duration-200 {{ $isMasterDataActive ? 'rotate-180' : '' }}">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <!-- Sub-menu -->
                        <ul id="masterDataMenu" class="pl-6 mt-1 space-y-2 transition-all duration-300 {{ $isMasterDataActive ? '' : 'hidden' }}">
                            <li class="pl-1 pr-3 py-1 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r text-[5px] text-gray-400 
                                {{ Request::is('master-data/role-data') || Request::is('master-data/role-data/*') ? 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' : '' }}">
                                <a href="{{ route('admin.master-data.role-data.index') }}" class="px-3 py-1 text-xs rounded-lg text-gray-600 flex items-center">
                                    <i class="fas fa-users-cog mr-2 shrink-0 fill-current @if(Request::is('master-data/role-data')|| Request::is('master-data/role-data/*')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif"></i> Role Data
                                </a>
                            </li>
                            <li class="pl-1 pr-3 py-1 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r text-[5px] text-gray-400 
                                {{ Request::is('master-data/department-data') || Request::is('master-data/department-data/*') ? 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' : '' }}">
                                <a href="{{ route('admin.master-data.department-data.index') }}" class="px-3 py-1 text-xs rounded-lg text-gray-600 flex items-center">
                                    <i class="fas fa-building mr-2 shrink-0 fill-current @if(Request::is('master-data/department-data')|| Request::is('master-data/department-data/*')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif"></i> Department Data
                                </a>
                            </li>
                            <li class="pl-1 pr-3 py-1 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r text-[5px] text-gray-400 
                                {{ Request::is('master-data/category-data') || Request::is('master-data/category-data/*') ? 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' : '' }}">
                                <a href="{{ route('admin.master-data.category-data.index') }}" class="px-3 py-1 text-xs rounded-lg text-gray-600 flex items-center">
                                    <i class="fas fa-tags mr-2 shrink-0 fill-current @if(Request::is('master-data/category-data')|| Request::is('master-data/category-data/*')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif""></i> Category Data
                                </a>
                            </li>
                            <li class="pl-1 pr-3 py-1 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r text-[5px] text-gray-400 
                                {{ Request::is('master-data/area-data') || Request::is('master-data/area-data/*') ? 'from-[#048cfb]/[0.12] to-[#048cfb]/[0.04]' : '' }}">
                                <a href="{{ route('admin.master-data.area-data.index') }}" class="px-3 py-1 text-xs rounded-lg text-gray-600 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 shrink-0 fill-current @if(Request::is('master-data/area-data')|| Request::is('master-data/area-data/*')){{ 'text-[#048cfb]' }}@else{{ 'text-gray-400 dark:text-gray-900' }}@endif""></i> Area Data
                                </a>
                            </li>
                        </ul>
                        
                    </li>

                    <script>
                        function toggleDropdown(event, menuId, iconId) {
                            event.stopPropagation();
                            const menu = document.getElementById(menuId);
                            const icon = document.getElementById(iconId);

                            menu.classList.toggle('hidden');
                            icon.classList.toggle('rotate-180');
                        }

                        // Pastikan dropdown tetap terbuka saat berada di halaman terkait
                        document.addEventListener("DOMContentLoaded", function () {
                            const isActive = @json($isMasterDataActive);
                            if (isActive) {
                                document.getElementById("masterDataMenu").classList.remove("hidden");
                                document.getElementById("iconMasterData").classList.add("rotate-180");
                            }
                        });
                    </script>
                    @endhasanyrole

                </ul>
            </div>
        </div>

        <div class="mt-7 w-full">
            <a href="{{ route('report-form') }}" 
                class="block w-full px-4 py-2 text-center text-white bg-blue-400 rounded-md shadow-md hover:bg-blue-500 transition duration-300"
                aria-label="Go to Report Form LCT">
                Report Form LCT
            </a>
        </div>               
    </div>
</div>

