<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\RateLimiter;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Mengubah login dari email ke NPK
        Fortify::authenticateUsing(function (Request $request) {
            $npk = $request->npk;
            $password = $request->password;
        
            Log::info('Attempting login for NPK: ' . $npk);
        
            $user = User::where('npk', $npk)->first();
        
            if (!$user) {
                Log::warning('Login failed: User not found for NPK ' . $npk);
                return null;
            }
        
            if (!Hash::check($password, $user->password_hash)) {
                Log::warning('Login failed: Password mismatch for NPK ' . $npk);
                return null;
            }
        
            Log::info('Login successful for NPK: ' . $npk);
        
            return $user;
        });

        // Menetapkan 'npk' sebagai username
        Fortify::username(fn () => 'npk');

        // Rate limiter untuk login
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('npk')).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        // Rate limiter untuk 2FA
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
