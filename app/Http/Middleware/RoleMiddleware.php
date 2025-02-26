<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            // Log::info('Middleware: User belum login');
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = Auth::user();
        $userRoles = $user->roleLct->pluck('name')->toArray();

        if (!array_intersect($roles, $userRoles)) {
            // Log::info('Middleware: Akses ditolak untuk user ini');
            return redirect('/unauthorized')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Log::info('Middleware: Akses diberikan ke user');
        return $next($request);
    }

}
