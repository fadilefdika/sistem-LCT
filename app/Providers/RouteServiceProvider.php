<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Get the default home path based on the authenticated user's role.
     */
    public static function home(): string
    {
        // Cek jika user terautentikasi dengan guard 'web'
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $roleIds = $user->roleLct->pluck('id')->toArray();

            if (in_array(2, $roleIds)) {
                return route('admin.dashboard'); // PIC
            }

            if (in_array(4, $roleIds)) {
                return route('admin.dashboard'); // Manager
            }

            return route('admin.reporting.index'); // User biasa
        }

        // Cek jika user terautentikasi dengan guard 'ehs'
        if (Auth::guard('ehs')->check()) {
            return route('choose-destination-ehs');
        }

        // Jika tidak ada user yang terautentikasi, redirect ke halaman login
        return route('login');
    }

    /**
     * Define route model bindings, pattern filters, and other route configurations.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
