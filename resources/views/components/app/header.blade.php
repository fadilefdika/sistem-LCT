<header class="sticky top-0 before:absolute before:inset-0 before:backdrop-blur-md max-lg:before:bg-white/90 dark:max-lg:before:bg-gray-800/90 before:-z-10 z-55 {{ $variant === 'v2' || $variant === 'v3' ? 'before:bg-white after:absolute after:h-px after:inset-x-0 after:top-full after:bg-gray-200 dark:after:bg-gray-700/60 after:-z-10' : 'max-lg:shadow-xs lg:before:bg-gray-100/90 dark:lg:before:bg-gray-900/90' }} {{ $variant === 'v2' ? 'dark:before:bg-gray-800' : '' }} {{ $variant === 'v3' ? 'dark:before:bg-gray-900' : '' }}">
    <div class="px-4 sm:px-6">
        <div class="flex items-center justify-between h-16 {{ $variant === 'v2' || $variant === 'v3' ? '' : 'lg:border-b border-gray-200 dark:border-gray-700/60' }}">
            <div class="flex">               
                    <!-- Hamburger button untuk role admin, EHS, PIC, Manajer -->
                    @if(Request::is('report-form'))
                        <a href="{{ route('admin.dashboard') }}" 
                        class="px-4 py-2 text-white bg-blue-400 rounded-md shadow-md hover:bg-blue-500 transition duration-300">
                            Masuk ke Dashboard
                        </a>
                    @else
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
                    @endif


                <!-- Breadcrumbs -->
                <div class="block">
                    @if (Route::currentRouteName() === 'admin.dashboard')
                        {{ Breadcrumbs::render('dashboard') }}
                    @elseif (Route::currentRouteName() === 'ehs.dashboard')
                        {{ Breadcrumbs::render('ehs dashboard') }}
                    @elseif (Route::currentRouteName() === 'admin.laporan-lct.index')
                        {{ Breadcrumbs::render('laporan-lct') }}
                    @elseif (Route::currentRouteName() === 'admin.reporting.show.new')
                        {{ Breadcrumbs::render('laporan-lct.show', $laporan) }}
                    @elseif (Route::currentRouteName() === 'ehs.laporan-lct.index')
                        {{ Breadcrumbs::render('ehs laporan-lct') }}
                    {{-- @elseif (Route::currentRouteName() === 'ehs.reporting.show.new')
                        {{ Breadcrumbs::render('ehs laporan-lct.show', $laporan) }} --}}
                    @elseif (Route::currentRouteName() === 'admin.manajemen-lct.index')
                        {{ Breadcrumbs::render('manajemen-lct') }}
                    @elseif (Route::currentRouteName() === 'admin.manajemen-lct.show')
                        {{ Breadcrumbs::render('manajemen-lct.show', $laporan) }}
                    @elseif (Route::currentRouteName() === 'admin.finding-followup.index')
                        {{ Breadcrumbs::render('finding-followup') }}
                    @elseif (Route::currentRouteName() === 'admin.finding-followup.show')
                        {{ Breadcrumbs::render('finding-followup.show', $laporan) }}
                    @elseif(Route::currentRouteName() === 'admin.reporting.index')
                        {{ Breadcrumbs::render('progress-perbaikan') }}
                    @elseif(Route::currentRouteName() === 'admin.reporting.show')
                        {{ Breadcrumbs::render('progress-perbaikan.show', $laporan) }}
                    @elseif(Route::currentRouteName() === 'ehs.reporting.index')
                        {{ Breadcrumbs::render('ehs progress-perbaikan') }}
                    @elseif(Route::currentRouteName() === 'ehs.reporting.show')
                        {{ Breadcrumbs::render('ehs progress-perbaikan.show', $laporan) }}
                    @elseif(Route::currentRouteName() === 'admin.budget-approval.index')
                        {{ Breadcrumbs::render('budget-approval') }}
                    @elseif(Route::currentRouteName() === 'admin.budget-approval.show')
                        {{ Breadcrumbs::render('budget-approval.show', $laporan) }}
                    @elseif(Route::currentRouteName() === 'admin.budget-approval-history.index')
                        {{ Breadcrumbs::render('budget-approval-history') }}
                    @elseif(Route::currentRouteName() === 'admin.budget-approval-history.show')
                        {{ Breadcrumbs::render('budget-approval-history.show', $laporan) }}
                    @elseif(Route::currentRouteName() === 'admin.riwayat-lct.index')
                        {{ Breadcrumbs::render('riwayat-lct') }}
                    @elseif(Route::currentRouteName() === 'admin.riwayat-lct.show')
                        {{ Breadcrumbs::render('riwayat-lct.show', $laporan) }}
                    @elseif(Route::currentRouteName() === 'admin.master-data.role-data.index')
                        {{ Breadcrumbs::render('master-data.role-data') }}
                    @elseif(Route::currentRouteName() === 'admin.master-data.department-data.index')
                        {{ Breadcrumbs::render('master-data.department-data') }}
                    @elseif(Route::currentRouteName() === 'admin.master-data.category-data.index')
                        {{ Breadcrumbs::render('master-data.category-data') }}
                    @elseif(Route::currentRouteName() === 'admin.master-data.area-data.index')
                        {{ Breadcrumbs::render('master-data.area-data') }}
                    @elseif(Route::currentRouteName() === 'ehs.master-data.role-data.index')
                        {{ Breadcrumbs::render('ehs.master-data.role-data') }}
                    @elseif(Route::currentRouteName() === 'ehs.master-data.department-data.index')
                        {{ Breadcrumbs::render('ehs.master-data.department-data') }}
                    @elseif(Route::currentRouteName() === 'ehs.master-data.category-data.index')
                        {{ Breadcrumbs::render('ehs.master-data.category-data') }}
                    @elseif(Route::currentRouteName() === 'ehs.master-data.area-data.index')
                        {{ Breadcrumbs::render('ehs.master-data.area-data') }}
                    @endif

                </div>
            </div>


            <!-- Header: Right side -->
            <div class="flex items-center space-x-3">

                <!-- Tombol Notifikasi -->
                @if (!request()->is('report-form'))
                    @if($roleName !== 'user')
                        <x-dropdown-notifications align="right" :roleName="$roleName" :notifikasiLCT="$notifikasiLCT" />
                    @endif
                @endif

            
                <!-- Divider -->
                <hr class="w-px h-6 bg-gray-200 dark:bg-gray-700/60 border-none" />

                <!-- User button -->
                <x-dropdown-profile align="right" />

            </div>

        </div>
    </div>
</header>