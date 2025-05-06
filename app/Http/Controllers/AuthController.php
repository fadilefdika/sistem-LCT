<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EhsUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $rules = [
            'role' => 'required|in:ehs,manajer,pic,pelapor',
            'password' => 'required|string',
            'npk_or_username' => ['required'],
        ];

        // Tambahkan validasi tergantung role
        if ($request->role === 'ehs') {
            $rules['npk_or_username'][] = 'string';
        } else {
            $rules['npk_or_username'][] = 'numeric';
        }

        $messages = [
            'npk_or_username.required' => 'Kolom NPK atau Username wajib diisi.',
            'npk_or_username.numeric' => 'NPK harus berupa angka.',
            'npk_or_username.string' => 'Username harus berupa teks.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // === Login untuk EHS ===
        if ($request->role === 'ehs') {
            $username = $request->npk_or_username;
            $user = EhsUser::where('username', $username)->first();

            if (!$user || !Hash::check($request->password, $user->password_hash)) {
                return back()->withErrors([
                    'npk_or_username' => 'Login failed. Please check your username and password.',
                ])->withInput();
            }

            // Cek apakah memiliki role ehs
            $roleName = $user->roles->first()->name ?? null;
            if ($roleName !== 'ehs') {
                return back()->withErrors([
                    'npk_or_username' => 'Permission denied',
                ])->withInput();
            }

            Auth::guard('ehs')->login($user);
            $request->session()->regenerate();
            return redirect()->route('choose-destination-ehs'); // ✅ Ini

        }

        // === Login untuk role selain EHS (web guard) ===
        $npk = $request->npk_or_username;

        $roleMapping = [
            'pelapor' => 1,
            'pic' => 2,
            'manajer' => 4,
        ];

        $expectedRoleId = $roleMapping[$request->role] ?? null;

        // Ambil user berdasarkan NPK
        $user = User::with('roleLct')->where('npk', $npk)->first();

        if (!$user) {
            return back()->withErrors([
                'npk_or_username' => 'NPK not found.',
            ])->withInput();
        }

        $actualRoleId = $user->roleLct->first()->id ?? null;

        if ($actualRoleId != $expectedRoleId) {
            return back()->withErrors([
                'npk_or_username' => 'Permission denied.',
            ])->withInput();
        }

        // Login menggunakan Auth Laravel
        $credentials = [
            'npk' => $npk,
            'password' => $request->password,
        ];

        if ($user && Hash::check($credentials['password'], $user->password_hash)) {
            // Password cocok
            Auth::guard('web')->login($user, $request->filled('remember'));
            
            $request->session()->regenerate();
    
            return redirect()->route('choose-destination-user');
        }
        

        return back()->withErrors([
            'npk_or_username' => 'Login failed. Please check your NPK and password.',
        ])->withInput();
    }


    public function logout(Request $request)
    {
        // Tidak perlu hapus token kalau tidak pakai Sanctum
        // Logout dari guard 'web'
        Auth::guard('web')->logout();

        // Logout dari guard 'ehs'
        Auth::guard('ehs')->logout();

        // Invalidasi sesi
        $request->session()->invalidate();

        // Regenerasi token CSRF
        $request->session()->regenerateToken();

        return redirect('login');
    }


    
}
