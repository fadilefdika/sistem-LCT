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
        $guard = null;
        $user = null;
        $allowedRoles = [];

        // Cek pengguna login dengan guard mana
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $guard = 'web';
            $allowedRoles = ['pic', 'manajer', 'user']; // Role-role khusus user biasa
        } elseif (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $guard = 'ehs';
            $allowedRoles = ['ehs']; // Role-role khusus EHS
        }

        // Jika tidak login
        if (!$user) {
            return redirect('/unauthorized')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // dd($user);
        // Bypass khusus untuk username "admin ehs"
        if ($user->username == "admin ehs") {
            return $next($request);
        }

        // Pastikan hanya role yang diperbolehkan berdasarkan guard
        if (!empty($roles)) {
            // Hanya ijinkan role yang ada di $allowedRoles
            $roles = array_intersect($roles, $allowedRoles);
        } else {
            // Jika tidak dikasih parameter role di route, pakai semua allowedRoles
            $roles = $allowedRoles;
        }

        // Ambil role user dari relasi roleLct
        $userRoles = $user->roleLct->pluck('name')->toArray();

        // Cek apakah user punya salah satu role yang diizinkan
        if (!array_intersect($roles, $userRoles)) {
            return redirect('/unauthorized')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}