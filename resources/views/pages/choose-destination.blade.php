<x-authentication-layout>
    @php
        $user = auth()->user();

        // Pengecekan apakah menggunakan guard 'ehs'
        $isEhsGuard = Auth::guard('ehs')->check();

        // Menentukan apakah user bisa mengakses dashboard berdasarkan guard dan role
        $canAccessDashboard = $user && (
            ($isEhsGuard && ($user->hasRole('ehs') || $user->hasRole('pic') || $user->hasRole('manajer'))) ||
            (!$isEhsGuard && ($user->hasRole('ehs') || $user->hasRole('pic') || $user->hasRole('manajer') || $user->hasRole('user')))
        );

        // Menentukan apakah user bisa mengakses LCT Report Form
        $canAccessLctReportForm = $user && (
            ($isEhsGuard && ($user->hasRole('ehs') || $user->hasRole('pic') || $user->hasRole('manajer'))) ||
            (!$isEhsGuard && ($user->hasRole('ehs') || $user->hasRole('pic') || $user->hasRole('manajer') || $user->hasRole('user')))
        );
    @endphp

    <div class="container mx-auto flex flex-col items-center justify-center min-h-screen text-center">
        <h2 class="text-2xl font-bold text-gray-900">Select Your Destination</h2>
        <p class="text-gray-500 mt-3">Please choose where you would like to proceed after logging in.</p>

        <div class="mt-8 flex flex-wrap justify-center gap-4">
            @if($isEhsGuard)
                <!-- Jika guard EHS, arahkan ke dashboard EHS -->
                @if($canAccessDashboard)
                    <a href="{{ route('ehs.dashboard') }}" 
                       class="px-6 py-3 text-lg font-medium bg-gray-900 text-white rounded-lg shadow-md hover:bg-gray-700 transition duration-300 ease-in-out">
                        🏠 EHS Dashboard
                    </a>
                @endif

                <!-- Jika guard EHS, arahkan ke report form EHS -->
                @if($canAccessLctReportForm)
                    <a href="{{ route('ehs.report-form') }}" 
                       class="px-6 py-3 text-lg font-medium bg-white text-gray-900 border border-gray-300 rounded-lg shadow-md hover:bg-gray-200 transition duration-300 ease-in-out">
                        📋 EHS LCT Report Form
                    </a>
                @endif
            @else
                <!-- Jika guard bukan EHS, akses ke dashboard biasa -->
                @if($canAccessDashboard)
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-6 py-3 text-lg font-medium bg-gray-900 text-white rounded-lg shadow-md hover:bg-gray-700 transition duration-300 ease-in-out">
                        🏠 Dashboard
                    </a>
                @endif

                <!-- Jika guard bukan EHS, akses ke report form biasa -->
                @if($canAccessLctReportForm)
                    <a href="{{ route('report-form') }}" 
                       class="px-6 py-3 text-lg font-medium bg-white text-gray-900 border border-gray-300 rounded-lg shadow-md hover:bg-gray-200 transition duration-300 ease-in-out">
                        📋 LCT Report Form
                    </a>
                @endif
            @endif
        </div>
    </div>
</x-authentication-layout>
