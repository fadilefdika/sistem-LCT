<x-authentication-layout>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">{{ __('Welcome back!') }}</h1>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif   
    <!-- Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="npk" value="{{ __('NPK') }}" />
                <x-input id="npk" type="text" name="npk" :value="old('npk')" required autofocus />
            </div>            
            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" type="password" name="password" required autocomplete="current-password" />                
            </div>
        </div>
        <div class="flex items-center justify-between mt-6">
            <div></div> <!-- Elemen kosong agar tombol tetap di kanan -->
            <x-button class="ml-3 cursor-pointer">
                {{ __('Sign in') }}
            </x-button>
        </div>
        
    </form>
    <x-validation-errors class="mt-4" />   
    <!-- Footer -->
    {{-- <div class="pt-5 mt-6 border-t border-gray-100 dark:border-gray-700/60">
        <div class="text-sm">
            {{ __('Don\'t you have an account?') }} <a class="font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="{{ route('register') }}">{{ __('Sign Up') }}</a>
        </div>
       
    </div> --}}
</x-authentication-layout>
