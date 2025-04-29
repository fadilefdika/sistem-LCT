<?php

namespace App\Http\Controllers;

use App\Models\EhsUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EhsController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login-ehs');
    }

    public function login(Request $request)
    {
        // Validasi input dari form login
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    
        // Mencari user berdasarkan username yang diberikan
        $user = EhsUser::where('username', $request->username)->first();
    
        if (!$user) {
            // Jika user tidak ditemukan
            Log::warning('User tidak ditemukan: ' . $request->username);
            dd('User tidak ditemukan');
        }
    
        // Debug: Menampilkan user yang ditemukan
        Log::info('User ditemukan: ' . $user->username);
    
        // Cek apakah password yang dimasukkan cocok dengan hash password yang ada
        if ($user && Hash::check($request->password, $user->password_hash)) {
            
            // Debug: Cek apakah password cocok
            Log::info('Password cocok untuk user: ' . $user->username);
    
            // Ambil role pengguna, pastikan memiliki peran 'ehs'
            $roleName = $user->roles->first()->name ?? null;
    
            // Debug: Cek role pengguna
            Log::info('Role pengguna: ' . ($roleName ? $roleName : 'Tidak ada role'));
    
            // dd($roleName);
            // Jika roleName adalah 'ehs', login sebagai admin EHS
            if ($roleName && $roleName === 'ehs') {
                Auth::guard('ehs')->login($user); // Login menggunakan guard 'ehs'
            
                // Laravel akan mengarahkan ke route yang sesuai setelah login
                return redirect()->route('choose-destination-ehs'); // Arahkan ke route choose-destination-ehs
            }
            
            
    
            return back()->withErrors([
                'username' => 'Username atau password salah qe.',
            ]);
        }
    
        // Jika password tidak cocok
        Log::warning('Password salah untuk user: ' . $request->username);
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }
    
    

    

}
