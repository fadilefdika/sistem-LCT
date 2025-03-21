<x-authentication-layout>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">{{ __('LCT SYSTEM') }}</h1>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif   
    <!-- Form -->
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="space-y-6">
        <!-- NPK Input -->
        <div class="md:flex md:items-center md:space-x-4 border-2 border-white/50 rounded-full py-4 px-6 bg-white/30 backdrop-blur-md w-full max-w-2xl mx-auto">
            <x-label for="npk" value="{{ __('NPK') }}" 
                class="md:w-40 bg-white text-black font-bold text-2xl rounded-full px-6 py-3 flex items-center shadow-md tracking-wide" />
             
            <x-input id="npk" type="text" name="npk" :value="old('npk')" required autofocus 
                placeholder="Masukkan NPK Anda"
                class="w-full bg-transparent border-none focus:ring-white/50 focus:border-white placeholder-gray-600 text-black font-bold text-xl tracking-wide" />
        </div>

        <!-- Password Input -->
        <div class="md:flex md:items-center md:space-x-4 border-2 border-white/50 rounded-full py-4 px-6 bg-white/30 backdrop-blur-md w-full max-w-2xl mx-auto">
            <x-label for="password" value="{{ __('Password') }}" 
                class="md:w-40 bg-white text-black font-bold text-2xl rounded-full px-6 py-3 flex items-center shadow-md tracking-wide" />
            
            <x-input id="password" type="password" name="password" required autocomplete="current-password" 
                placeholder="Masukkan Password Anda"
                class="w-full bg-transparent border-none focus:ring-white/50 focus:border-white placeholder-gray-600 text-black font-bold text-xl tracking-wide" />
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center justify-between mt-6">
        <div></div> <!-- Elemen kosong agar tombol tetap di kanan -->
        <x-button class="ml-3 text-lg cursor-pointer px-5 py-4">
            {{ __('Sign in') }}
        </x-button>
    </div>
</form>


    <x-validation-errors class="mt-4" />   
    
</x-authentication-layout>
