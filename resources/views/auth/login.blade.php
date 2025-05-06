<x-authentication-layout>
    <div class="text-left mb-6">
        <h1 class="text-4xl text-black dark:text-gray-100 font-black mb-2" style="font-family: 'Poppins', sans-serif;">
            {{ __('EHSight') }}
        </h1>        
        <h6 class="text-lg text-gray-800 dark:text-gray-100 font-normal">{{ __('EHS Insight') }}</h6>
    </div>
    

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="flex flex-col gap-2">

            {{-- NPK/Username Input --}}
            <div>
                <div class="flex items-center bg-[#C1dBEA] text-black rounded-lg shadow-sm px-4 py-3">
                    <i class="fa fa-user text-black mr-3 text-lg"></i>
                    <input id="npk_or_username" name="npk_or_username" type="number" required autofocus
                        placeholder="Enter your NPK here..."
                        class="w-full bg-transparent outline-none text-base md:text-lg font-medium border-[#C1dBEA]" />
                </div>
            </div>

            {{-- Password Input --}}
            <div>
                <div class="flex items-center bg-[#C1dBEA] text-black rounded-lg shadow-sm px-4 py-3">
                    <i class="fa fa-lock text-black mr-3 text-lg"></i>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="Enter your password here..."
                        class="w-full bg-transparent border-4 border-[#C1dBEA] focus:border-[#C1dBEA] focus:outline-none text-base md:text-lg font-medium" />
                </div>
            </div>
            

            {{-- Role Dropdown --}}
          
                <div class="flex items-center bg-[#C1dBEA] text-black rounded-lg shadow-sm px-4 py-3">
                    <i class="fa fa-user-tag text-black mr-3 text-lg"></i>
                    <select name="role" id="role"
                        class="w-full bg-transparent border-0 outline-none text-base md:text-lg font-medium appearance-none"
                        required>
                        <option value="">-- Select Role --</option>
                        <option value="ehs">EHS</option>
                        <option value="manajer">Manajer</option>
                        <option value="pic">PIC</option>
                        <option value="pelapor">Pelapor</option>
                    </select>
                </div>
           

            {{-- Submit --}}
            <div class="flex items-start justify-start mt-6">
                <x-button class="text-base md:text-lg cursor-pointer px-8 py-3 md:px-10 md:py-4 bg-[#0067A9] text-white font-bold rounded-sm">
                    {{ __('Sign in') }}
                </x-button>
            </div>
        </div>

        <x-validation-errors class="mt-4" />
    </form>

    {{-- JS Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roleSelect = document.getElementById('role');
            const npkInput = document.getElementById('npk_or_username');

            roleSelect.addEventListener('change', function () {
                if (this.value === 'ehs') {
                    npkInput.type = 'text';
                    npkInput.placeholder = 'Enter your username here...';
                } else {
                    npkInput.type = 'number';
                    npkInput.placeholder = 'Enter your NPK here...';
                }
            });
        });
    </script>
</x-authentication-layout>
