<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = Auth::user();
        $roles = $user->roleLct->pluck('nama_role');

        // 🔀 Redirect berdasarkan role jika belum di halaman yang benar
        if ($roles->contains('user') && !$request->is('users')) {
            return redirect('/users');
        } elseif ($roles->contains('ehs') && !$request->is('dashboard')) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
