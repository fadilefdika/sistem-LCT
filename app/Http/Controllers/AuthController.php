<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EhsUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function decryptRSA($encrypted)
    {
        $privateKey = Storage::get('rsa_private.pem');
        if (!openssl_private_decrypt(base64_decode($encrypted), $decrypted, $privateKey)) {
            throw new \Exception("Decrypt failed.");
        }
        return $decrypted;
    }

    public function login(Request $request)
    {
        try {
            $decryptedNpk = $this->decryptRSA($request->input('encrypted_npk'));
            $decryptedPassword = $this->decryptRSA($request->input('encrypted_password'));
            $role = $request->input('role');
            $redirectTo = $request->input('redirect_to');
        } catch (\Exception $e) {
            return back()->withErrors([
                'npk_or_username' => 'Gagal mendekripsi data. Silakan refresh halaman dan coba lagi.',
            ])->withInput();
        }

        // Validasi role
        $validRoles = ['ehs', 'manajer', 'pic', 'user'];
        if (!in_array($role, $validRoles)) {
            return back()->withErrors([
                'role' => 'Role tidak valid.',
            ])->withInput();
        }

        // Validasi NPK/username format
        if ($role === 'ehs') {
            if (!is_string($decryptedNpk) || empty($decryptedNpk)) {
                return back()->withErrors([
                    'npk_or_username' => 'Username wajib diisi dan harus berupa teks.',
                ])->withInput();
            }
        } else {
            if (!is_numeric($decryptedNpk)) {
                return back()->withErrors([
                    'npk_or_username' => 'NPK harus berupa angka.',
                ])->withInput();
            }
        }

        if (empty($decryptedPassword)) {
            return back()->withErrors([
                'password' => 'Password wajib diisi.',
            ])->withInput();
        }

        /**
         * Login untuk EHS
         */
        if ($role === 'ehs') {
            $user = EhsUser::where('username', $decryptedNpk)->first();

            if (!$user || !Hash::check($decryptedPassword, $user->password_hash)) {
                return back()->withErrors([
                    'npk_or_username' => 'Login gagal. Cek kembali username dan password.',
                ])->withInput();
            }

            $roleName = $user->roles->first()->name ?? null;
            if ($roleName !== 'ehs') {
                return back()->withErrors([
                    'npk_or_username' => 'Akses ditolak.',
                ])->withInput();
            }

            Auth::guard('ehs')->login($user);
            $request->session()->regenerate();
            session(['active_role' => 'ehs']);

            return redirect()->route(
                $redirectTo === 'form' ? 'ehs.report-form' : 'ehs.dashboard'
            );
        }

        /**
         * Login untuk User/PIC/Manajer
         */
        $npk = $decryptedNpk;
        $roleMapping = [
            'user' => 1,
            'pic' => 2,
            'manajer' => 4,
        ];
        $expectedRoleId = $roleMapping[$role];

        $user = User::with('roleLct')->where('npk', $npk)->first();

        if (!$user) {
            return back()->withErrors([
                'npk_or_username' => 'NPK tidak ditemukan.',
            ])->withInput();
        }

        if ($user->roleLct->isEmpty()) {
            $user->roleLct()->attach(1); // Default ke user biasa
            $user->load('roleLct');
        }

        if (!$user->roleLct->contains('id', $expectedRoleId)) {
            return back()->withErrors([
                'npk_or_username' => 'Role Anda tidak memiliki akses.',
            ])->withInput();
        }

        if (!Hash::check($decryptedPassword, $user->password_hash)) {
            return back()->withErrors([
                'npk_or_username' => 'Login gagal. Cek kembali NPK dan password.',
            ])->withInput();
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        session(['active_role' => $role]);

        return redirect()->route(
            $redirectTo === 'form' ? 'report-form' : 'admin.dashboard'
        );
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
