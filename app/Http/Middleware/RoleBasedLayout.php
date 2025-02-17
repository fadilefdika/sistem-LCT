<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class RoleBasedLayout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            // Cek role user
            $role = $user->roles->first()->name ?? 'user';
            
            // Simpan dalam session untuk digunakan di layout
            session(['layout' => ($role === 'user') ? 'layouts.user' : 'layouts.admin']);
        }
        return $next($request);
    }
}
