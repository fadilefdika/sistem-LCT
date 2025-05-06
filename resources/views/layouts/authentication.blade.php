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
        

    </head>
    <body class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400">

        <main class="dark:bg-gray-900"
        style="background: linear-gradient(180deg, rgba(241, 247, 250, 0.85) 67%, rgba(224, 242, 250, 1) 100%), url('/images/background.png');
               background-size: cover;
               background-position: center;">
    
            <div class="relative flex h-screen overflow-hidden">
        
                <!-- Logo kiri atas -->
                <div class="absolute top-6 left-6 z-20">
                        <img src="{{ asset('images/LOGO-AVI-OFFICIAL.png') }}" 
                            alt="Logo" 
                            class="w-28 h-auto object-contain">
                </div>
        
                <!-- Logo kanan atas -->
                <div class="absolute top-6 right-6 z-20">
                    <img src="{{ asset('images/safety.png') }}" 
                        alt="Logo" 
                        class="w-28 h-auto object-contain">
                </div>
        
                <!-- Gambar kiri -->
                <div class="hidden md:flex items-center justify-center absolute inset-y-0 left-0 w-1/2 px-8">
                    <div class="w-full h-full flex justify-center items-center">
                        <img class="object-contain max-w-[85%] max-h-[85%]" 
                            src="{{ asset('images/gambar-login.png') }}" 
                            alt="Authentication image" />
                    </div>
                </div>
        
                <!-- Form kanan -->
                <div class="w-full md:w-1/2 ml-auto flex flex-col justify-center items-center px-6 lg:px-12 z-10">
                    <div class="max-w-lg w-full text-left">
                        {{ $slot }}
                    </div>
                </div>
        
            </div>
        
        </main>
        
        @livewireScriptConfig
        </body>
        
</html>
