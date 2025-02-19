<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'npk' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('npk', $request->npk)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'NPK atau password salah!');
        }

        Auth::login($user);

        // 🔥 Debug: Cek apakah user memiliki role
        // dd(Auth::user()->roleLct);

        if ($user->roleLct->nama_role === 'ehs') {
            return redirect('/dashboard')->with('success', 'Selamat datang Admin EHS!');
        } else {
            return redirect('/users')->with('success', 'Selamat datang User!');
        }
    }

    


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
}
