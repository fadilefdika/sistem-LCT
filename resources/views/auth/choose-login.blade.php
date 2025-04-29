<x-authentication-layout>
    <div class="min-h-screen flex flex-col justify-center items-center px-4">
        <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-lg p-8 max-w-md w-full text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ __('Welcome to LCT System') }}</h1>
            <p class="text-gray-700 mb-8">{{ __('Please choose your login method below:') }}</p>

            <div class="space-y-4">
                <a href="{{ route('login') }}" class="block">
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition-all duration-200">
                        {{ __('Login as Non-EHS') }}
                    </button>
                </a>

                <a href="{{ route('login-ehs') }}" class="block">
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg shadow-md transition-all duration-200">
                        {{ __('Login as EHS') }}
                    </button>
                </a>
            </div>
        </div>
    </div>
</x-authentication-layout>
