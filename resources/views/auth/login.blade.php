<x-authentication-layout>
    <div class="text-left mb-6">
        <h1 class="text-4xl text-black dark:text-gray-100 font-black mb-2" style="font-family: 'Poppins', sans-serif;">
            {{ __('EHSight') }}
        </h1>        
        <h6 class="text-lg text-gray-800 dark:text-gray-100 font-normal">{{ __('EHS Insight') }}</h6>
    </div>

    {{-- Tab Redirect Selector --}}
    <div class="mb-6 border-b border-gray-300">
        <nav class="flex space-x-4" aria-label="Tabs">
            <button type="button"
                id="tab-dashboard"
                onclick="setRedirectTo('dashboard')"
                class="px-4 py-2 text-sm font-medium text-blue-700 border-b-2 border-blue-700 focus:outline-none cursor-pointer"
            >
                🏠 Dashboard
            </button>

            <button type="button"
                id="tab-form"
                onclick="setRedirectTo('form')"
                class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent focus:outline-none cursor-pointer"
            >
                📋 Form Laporan
            </button>
        </nav>
    </div>


    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="redirect_to" id="redirect_to" value="{{ request('redirect_to', 'dashboard') }}">
    
        {{-- Hidden Encrypted Fields --}}
        <input type="hidden" name="encrypted_npk" id="encrypted_npk">
        <input type="hidden" name="encrypted_password" id="encrypted_password">
    
        <div class="flex flex-col gap-2">
            {{-- Role Dropdown --}}
            <div class="flex items-center bg-[#C1dBEA] text-black rounded-lg shadow-sm px-4 py-3">
                <i class="fa fa-user-tag text-black mr-3 text-lg"></i>
                <select name="role" id="role"
                    class="w-full bg-transparent border-0 outline-none text-base md:text-lg font-medium appearance-none"
                    required>
                    <option value="">-- Select Role --</option>
                    <option value="ehs">EHS</option>
                    <option value="manajer">Manager</option>
                    <option value="pic">PIC</option>
                    <option value="user">Employee</option>
                </select>
            </div>
            
            {{-- NPK/Username Input --}}
            <div>
                <div class="flex items-center bg-[#C1dBEA] text-black rounded-lg shadow-sm px-4 py-3">
                    <i class="fa fa-user text-black mr-3 text-lg"></i>
                    <input id="npk_or_username" name="npk_or_username" type="number" required autofocus
                        placeholder="Enter your NPK here..."
                        class="w-full bg-transparent outline-none text-base md:text-lg font-medium border-[#C1dBEA]"
                        min="0" step="1" />
                </div>
            </div>         
    
            {{-- Password Input --}}
            <div>
                <div class="flex items-center bg-[#C1dBEA] text-black rounded-lg shadow-sm px-4 py-3 relative">
                    <i class="fa fa-lock text-black mr-3 text-lg"></i>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="Enter your password here..."
                        class="w-full bg-transparent border-4 border-[#C1dBEA] focus:border-[#C1dBEA] focus:outline-none text-base md:text-lg font-medium pr-10" />
                    <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-black focus:outline-none">
                        <i id="toggle-icon" class="fa fa-eye"></i>
                    </button>
                </div>
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

    <style>
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }

        .tab-active {
            background-color: #0067A9 !important;
            color: white !important;
        }
        .tab-inactive {
            background-color: #e5e7eb !important;
            color: black !important;
        }
    </style>

    <!-- Tambahkan ini di atas semua script JS -->
    <script src="https://cdn.jsdelivr.net/npm/jsencrypt/bin/jsencrypt.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roleSelect = document.getElementById('role');
            const npkInput = document.getElementById('npk_or_username');
            const tabForm = document.getElementById('tab-form');
            const form = document.querySelector('form');

            function setRedirectTo(value) {
                document.getElementById('redirect_to').value = value;
            }

            // Role logic
            function handleRoleChange(role) {
                if (role === 'ehs') {
                    npkInput.type = 'text';
                    npkInput.placeholder = 'Enter your username here...';
                    tabForm.style.display = 'none';
                    setRedirectTo('dashboard');
                } else {
                    npkInput.type = 'number';
                    npkInput.placeholder = 'Enter your NPK here...';
                    tabForm.style.display = 'inline-block';
                }
            }

            // Initial state
            handleRoleChange(roleSelect.value);
            // Ambil dari URL query string jika ada
            const params = new URLSearchParams(window.location.search);
            const redirectTo = params.get('redirect_to');
            if (redirectTo) {
                setRedirectTo(redirectTo);
            } else {
                setRedirectTo('dashboard'); // fallback default
            }

            // Event when role changes
            roleSelect.addEventListener('change', function () {
                handleRoleChange(this.value);
            });

            // Submit handler
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const npk = npkInput.value;
                const password = document.getElementById('password').value;

                try {
                    const res = await fetch('/api/public-key');
                    const publicKey = await res.text();

                    const encryptor = new JSEncrypt();
                    encryptor.setPublicKey(publicKey);

                    const encryptedNpk = encryptor.encrypt(npk);
                    const encryptedPassword = encryptor.encrypt(password);

                    document.getElementById('encrypted_npk').value = encryptedNpk;
                    document.getElementById('encrypted_password').value = encryptedPassword;

                    // Kosongkan input asli agar tidak ikut terkirim
                    npkInput.value = '';
                    document.getElementById('password').value = '';

                    form.submit();
                } catch (err) {
                    alert("Gagal mengenkripsi data. Silakan coba lagi.");
                    console.error("RSA Encryption Error:", err);
                }
            });
        });
        
        function setRedirectTo(tabName) {
            document.getElementById('redirect_to').value = tabName;
        
            const dashboardTab = document.getElementById('tab-dashboard');
            const formTab = document.getElementById('tab-form');
        
            if (tabName === 'dashboard') {
                dashboardTab.classList.add('text-blue-700', 'border-blue-700');
                dashboardTab.classList.remove('text-gray-500', 'border-transparent');
        
                formTab.classList.add('text-gray-500', 'border-transparent');
                formTab.classList.remove('text-blue-700', 'border-blue-700');
            } else {
                formTab.classList.add('text-blue-700', 'border-blue-700');
                formTab.classList.remove('text-gray-500', 'border-transparent');
        
                dashboardTab.classList.add('text-gray-500', 'border-transparent');
                dashboardTab.classList.remove('text-blue-700', 'border-blue-700');
            }
        }
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-icon');
            const isHidden = passwordInput.type === 'password';
        
            passwordInput.type = isHidden ? 'text' : 'password';
            toggleIcon.classList.toggle('fa-eye', !isHidden);
            toggleIcon.classList.toggle('fa-eye-slash', isHidden);
        }
    </script>

</x-authentication-layout>
