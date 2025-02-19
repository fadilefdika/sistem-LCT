<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = Auth::user();

        // Ambil semua role yang dimiliki user
        $userRoles = $user->roleLct->pluck('nama_role')->toArray();

        // dd($userRoles);
        // Jika user tidak memiliki salah satu role yang diizinkan, redirect
        if (!array_intersect($roles, $userRoles)) {
            return redirect('/unauthorized')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
