<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'npk' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     // Ambil user berdasarkan NPK
    //     $user = User::where('npk', $request->npk)->first();

    //     // Jika user tidak ditemukan atau password salah, kembalikan error
    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return back()->withInput()->withErrors(['npk' => 'NPK atau password salah!']);
    //     } 

    //     // Login user
    //     Auth::login($user);
    //     dd(Auth::user());
    //     // Cek apakah user memiliki role
    //     $role = $user->roleLct->pluck('name')->first();
    //     dd($role);

    //     if (!$role) {
    //         Auth::logout(); // Logout user jika tidak ada role
    //         return back()->withErrors(['npk' => 'Akun Anda belum memiliki role yang valid. Hubungi admin.']);
    //     }

    //     dd($role);

    //     // Redirect berdasarkan role
    //     if ($role === 'ehs') {
    //         return redirect()->intended('/dashboard')->with('success', 'Selamat datang EHS!');
    //     } elseif ($role === 'pic') {
    //         return redirect()->intended('/dashboard')->with('success', 'Selamat datang PIC!');
    //     } else {
    //         return redirect()->intended('/users')->with('success', 'Selamat datang User!');
    //     }
    // }


    


    public function logout(Request $request)
    {
        // Jika menggunakan token (untuk API misalnya), hapus semua token yang terhubung dengan user
        $request->user()->tokens()->delete();
    
        // Logout dari guard 'web' jika ada
        Auth::guard('web')->logout();
    
        // Logout dari guard 'ehs' jika ada
        Auth::guard('ehs')->logout();
    
        // Invalidasi sesi untuk memastikan data sesi tidak tertinggal
        $request->session()->invalidate();
    
        // Regenerasi token CSRF untuk keamanan setelah logout
        $request->session()->regenerateToken();
    
        // Jika menggunakan API, kembalikan response JSON
        return response()->json(['message' => 'Logout berhasil']);
        
        // Jika menggunakan tampilan web, redirect ke halaman login
        // return redirect('/login');
    }
    
}
