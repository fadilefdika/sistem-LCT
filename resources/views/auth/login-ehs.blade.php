<x-authentication-layout>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">
        {{ __('LCT System - EHS Login') }}
    </h1>
     @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif   
    <!-- Form -->
    <form method="POST" action="{{ route('login-ehs') }}">
        @csrf
        <div class="space-y-4 md:space-y-6">
            <!-- username Input -->
            <div class="flex flex-col md:flex-row md:items-start gap-3 md:gap-4 border-2 border-white/50 rounded-xl p-3 md:py-4 md:px-6 bg-white/30 backdrop-blur-md w-full max-w-2xl mx-auto">
                <x-label for="username" value="{{ __('Username') }}" 
                    class="w-full sm:w-full md:w-40 lg:w-48 bg-white text-black font-bold text-lg md:text-2xl rounded-xl px-4 py-2 md:px-4 md:py-3 flex items-center justify-center md:justify-start shadow-md tracking-wide" />

                <x-input id="username" type="text" name="username" :value="old('username')" required autofocus 
                    placeholder="Enter your username here..."
                    class="w-full bg-transparent border focus:ring-white/50 focus:border-white placeholder-gray-600 text-black font-medium md:font-bold text-base md:text-xl tracking-wide" />
            </div>
    
            <!-- Password Input -->
            <div class="flex flex-col md:flex-row md:items-start gap-3 md:gap-4 border-2 border-white/50 rounded-xl p-3 md:py-4 md:px-6 bg-white/30 backdrop-blur-md w-full max-w-2xl mx-auto">
                <x-label for="password" value="{{ __('Password') }}" 
                    class="w-full sm:w-full md:w-40 lg:w-48 bg-white text-black font-bold text-lg md:text-2xl rounded-xl px-4 py-2 md:px-4 md:py-3 flex items-center justify-center md:justify-start shadow-md tracking-wide" />

                <x-input id="password" type="password" name="password" required autocomplete="current-password" 
                    placeholder="Enter your password here..."
                    class="w-full bg-transparent border focus:ring-white/50 focus:border-white placeholder-gray-600 text-black font-medium md:font-bold text-base md:text-lg tracking-wide" />
            </div>
        </div>
    
        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-6">
            <x-button class="text-base md:text-lg cursor-pointer px-4 py-2 md:px-5 md:py-4">
                {{ __('Sign in') }}
            </x-button>
        </div>
    </form>


    <x-validation-errors class="mt-4" />   
    
</x-authentication-layout>
