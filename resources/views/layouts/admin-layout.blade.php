<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">


        <title>{{ config('app.name', 'Sistem LCT') }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles        

        <script>
            if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
                document.querySelector('html').classList.remove('dark');
                document.querySelector('html').style.colorScheme = 'light';
            } else {
                document.querySelector('html').classList.add('dark');
                document.querySelector('html').style.colorScheme = 'dark';
            }
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
        <!-- CSS daterangepicker -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Moment.js -->
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

        <!-- Daterangepicker -->
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    </head>
    <body
        class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400"
        :class="{ 'sidebar-expanded': sidebarExpanded }"
        x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }"
        x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))">

        <script>
            if (localStorage.getItem('sidebar-expanded') == 'true') {
                document.querySelector('body').classList.add('sidebar-expanded');
            } else {
                document.querySelector('body').classList.remove('sidebar-expanded');
            }
        </script>

        <!-- Page wrapper -->
        <div class="flex h-[100dvh] overflow-hidden">

            <x-app.sidebar :variant="$attributes['sidebarVariant']" />

            <!-- Content area -->
            <div class="relative flex flex-col flex-1" 
                x-data="{ page: '' }"
                x-init="page = '{{ Route::currentRouteName() }}'"
                x-ref="contentarea"
                :class="{ 
                    'overflow-hidden': ['admin.reporting.show.new','admin.manajemen-lct.show',
                            'ehs.reporting.show.new','admin.reporting.show','admin.finding-followup.show','ehs.reporting.show'].includes(page), 
                    'overflow-y-auto': [
                            'admin.dashboard',
                            'admin.reporting.index',
                            'admin.finding-followup.index',
                            'admin.reporting.history',
                            'admin.manajemen-lct.history',
                            'admin.budget-approval.history',
                            'admin.master-data.role-data.index',
                            'admin.master-data.area-data.index',
                            'admin.master-data.department-data.index',
                            'admin.master-data.category-data.index',
                            'admin.master-data.ehs-data.index',
                            'admin.manajemen-lct.index',
                            'admin.laporan-lct.index',
                            'admin.riwayat-lct.index',
                            'admin.budget-approval.index',
                            'admin.budget-approval.show',
                            'admin.budget-approval-history.show',
                            'admin.budget-approval-history.index',

                            // Menambahkan route untuk ehs
                            'ehs.dashboard',
                            'ehs.reporting.index',
                            'ehs.reporting.history',

                            'ehs.laporan-lct.index',

                            'ehs.master-data.department-data.index',
                            'ehs.master-data.role-data.index',
                            'ehs.master-data.category-data.index',
                            'ehs.master-data.area-data.index'
                        ].includes(page)

                }">

                <x-app.header :variant="$attributes['headerVariant']" />

                <main class="grow">
                    {{ $slot }}
                </main>

            </div>



        </div>

        @include('components.notif')

        @stack('scripts')
        @livewireScriptConfig

        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>
        @endif
        @if (session('approve'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '{{ session('approve') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>
        @endif
        @if (session('closed'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success', // Bisa diganti ke 'success' jika ingin warna hijau
                        title: 'Report Closed!',
                        text: '{{ session('closed') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>
        @endif
        @if (session('reject'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Revision!',
                        text: '{{ session('reject') }}',
                        showConfirmButton: false,
                        timer: 2500
                    });
                });
            </script>
        @endif

        @livewireScripts
    </body>
</html>
