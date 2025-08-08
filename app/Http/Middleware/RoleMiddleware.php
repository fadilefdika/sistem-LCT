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
        $user = null;
        $guard = null;

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $guard = 'web';
            $allowedRoles = ['pic', 'manajer', 'user'];
        } elseif (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $guard = 'ehs';
            $allowedRoles = ['ehs'];
        }

        // Jika belum login sama sekali
        if (!$user) {
            // Redirect ke login dengan menyimpan URL tujuan agar bisa redirect balik
            return redirect()->guest(route('login') . '?redirect_to=' . urlencode($request->getRequestUri()))
                            ->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Admin bypass
        if ($user->username === "admin ehs") {
            return $next($request);
        }

        $activeRole = session('active_role');

        if (!$activeRole || !in_array($activeRole, $roles)) {
            return redirect('/unauthorized')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini. (active role tidak sesuai)');
        }

        $roles = !empty($roles) ? array_intersect($roles, $allowedRoles) : $allowedRoles;

        $userRoles = $user->roleLct->pluck('name')->toArray();
        if (!in_array($activeRole, $userRoles)) {
            return redirect('/unauthorized')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini. (role aktif tidak ada di user)');
        }

        return $next($request);
    }

    

}