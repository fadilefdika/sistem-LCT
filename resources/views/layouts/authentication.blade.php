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
        style="background: linear-gradient(129deg, rgba(242,255,152,0.5655054258031338) 3%, rgba(4,140,251,0.5627043053549545) 64%);">
    
        <div class="relative flex h-screen">
            <!-- Logo di kanan atas -->
            <div class="absolute top-0 right-0 bg-white p-8 rounded-bl-full shadow-lg">
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('images/LOGO-AVI-OFFICIAL.png') }}" 
                        alt="Logo" 
                        class="w-32 h-auto object-contain">
                </a>
            </div>

            <!-- Kontainer Kiri (Form + Logo) -->
            <div class="w-full md:w-1/2 flex flex-col justify-center items-center px-6 lg:px-12 relative z-10">
                
        
                <!-- Form -->
                <div class="max-w-lg w-full text-center">
                    {{ $slot }}
                </div>
                
            </div>
        
            <!-- Gambar Kanan -->
            <div class="hidden md:flex items-center justify-center absolute top-0 bottom-0 right-0 md:w-1/2 px-6">
                <div class="relative w-[80%] h-[85vh] overflow-hidden rounded-[50%]">
                    <img class="object-cover w-full h-full" 
                        src="{{ asset('images/gambar-login.jpg') }}" 
                        width="760" 
                        height="1024" 
                        alt="Authentication image" />
                </div>
            </div>
        
        </div>
        

        </main> 

        @livewireScriptConfig
    </body>
</html>
