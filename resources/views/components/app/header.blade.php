<header class="sticky top-0 before:absolute before:inset-0 before:backdrop-blur-md max-lg:before:bg-white/90 dark:max-lg:before:bg-gray-800/90 before:-z-10 z-30 {{ $variant === 'v2' || $variant === 'v3' ? 'before:bg-white after:absolute after:h-px after:inset-x-0 after:top-full after:bg-gray-200 dark:after:bg-gray-700/60 after:-z-10' : 'max-lg:shadow-xs lg:before:bg-gray-100/90 dark:lg:before:bg-gray-900/90' }} {{ $variant === 'v2' ? 'dark:before:bg-gray-800' : '' }} {{ $variant === 'v3' ? 'dark:before:bg-gray-900' : '' }}">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 {{ $variant === 'v2' || $variant === 'v3' ? '' : 'lg:border-b border-gray-200 dark:border-gray-700/60' }}">
            <div class="flex">               
                    <!-- Hamburger button untuk role admin, EHS, PIC, Manajer -->
                    <button
                        class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 lg:hidden"
                        @click.stop="sidebarOpen = !sidebarOpen"
                        aria-controls="sidebar"
                        :aria-expanded="sidebarOpen"
                    >
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <rect x="4" y="5" width="16" height="2" />
                            <rect x="4" y="11" width="16" height="2" />
                            <rect x="4" y="17" width="16" height="2" />
                        </svg>
                    </button>

                    <!-- Breadcrumbs -->
                <div class="block">
                    @if (Route::currentRouteName() === 'admin.dashboard')
                        {{ Breadcrumbs::render('dashboard') }}
                    @elseif (Route::currentRouteName() === 'admin.laporan-lct')
                        {{ Breadcrumbs::render('laporan-lct') }}
                    @elseif (Route::currentRouteName() === 'admin.laporan-lct.show')
                        {{ Breadcrumbs::render('laporan-lct.show', $laporan) }}
                    @elseif (Route::currentRouteName() === 'admin.manajemen-lct')
                        {{ Breadcrumbs::render('manajemen-lct') }}
                    @elseif (Route::currentRouteName() === 'admin.manajemen-lct.show')
                        {{ Breadcrumbs::render('manajemen-lct.show', $laporan) }}
                    @elseif(Route::currentRouteName() === 'admin.progress-perbaikan')
                        {{ Breadcrumbs::render('progress-perbaikan') }}
                    @elseif(Route::currentRouteName() === 'admin.progress-perbaikan.detail')
                        {{ Breadcrumbs::render('progress-perbaikan.detail') }}
                    @elseif(Route::currentRouteName() === 'admin.riwayat-lct')
                        {{ Breadcrumbs::render('riwayat-lct') }}
                    @endif

                </div>
            </div>


            <!-- Header: Right side -->
            <div class="flex items-center space-x-3">

                <!-- Notifications button -->
                <x-dropdown-notifications align="right" />            

                <!-- Divider -->
                <hr class="w-px h-6 bg-gray-200 dark:bg-gray-700/60 border-none" />

                <!-- User button -->
                <x-dropdown-profile align="right" />

            </div>

        </div>
    </div>
</header>