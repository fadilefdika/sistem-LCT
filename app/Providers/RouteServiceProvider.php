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
        $user = Auth::user();

        if (!$user) {
            return '/login';
        }

        // Load ulang user dari database agar trait HasRoles dapat dikenali
        $user = User::find($user->id);

        if ($user->hasRole('ehs') || $user->hasRole('pic')) {
            return '/dashboard';
        } elseif ($user->hasRole('user')) {
            return '/users';
        }

        return '/login';
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
