<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
        function updateDarkMode() {
            if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.colorScheme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
            }
        }

        document.addEventListener('DOMContentLoaded', updateDarkMode);
    </script>
</head>
<body
    class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400"
    x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') === 'true' }"
    :class="{ 'sidebar-expanded': sidebarExpanded }"
    x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))"
>

    <!-- Page wrapper -->
    <div class="flex flex-col overflow-hidden">
        <x-app.header :variant="$attributes->get('headerVariant')" />
        <div class="flex flex-col flex-1 overflow-hidden">
            @yield('content')
        </div>
    </div>

    @livewireScriptConfig

    @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>
    @endif
    
    @if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{!! session('error') !!}",
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
    @endif
</body>
</html>
