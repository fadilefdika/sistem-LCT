<?php

namespace App\Http\Controllers;

use App\Models\EhsUser;
use App\Models\User;
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
    
    public function index()
    {
        return view('pages.admin.master-data.ehs-data.index'); 
    }
    // Store data
    public function store(Request $request)
{
    try {
        // Validasi data input
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Hash password
        $hashedPassword = Hash::make($request->password);
        // Simpan data ke database
        EhsUser::create([
            'username' => $request->username,
            'password_hash' => $hashedPassword, // Pastikan password di-hash sebelum disimpan
            'user_id' => $request->user_id,
        ]);

        return response()->json(['message' => 'EHS berhasil ditambahkan!'], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Terjadi kesalahan.',
            'message' => $e->getMessage()
        ], 500);
    }
}

    
    // Update data
    public function update(Request $request, $id)
    {
        try{
            $request->validate([
                'username' => 'required|string|max:255',
                'user_id' => 'nullable|exists:users,id',
            ]);
    
            $ehs = EhsUser::findOrFail($id);
            $ehs->update([
                'username' => $request->username,
                'user_id' => $request->user_id
            ]);
    
            return response()->json(['message' => 'Ehs berhasil diperbarui!'], 200);
        }catch(\Exception $e){
            return response()->json([
                'error' => 'Terjadi kesalahan.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Hapus data (soft delete)
    public function destroy($id)
    {
        try {
            $ehs = EhsUser::findOrFail($id);
            $ehs->delete();
            return response()->json(['message' => 'Ehs berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus EHS!'], 500);
        }
    }

    public function searchUsers(Request $request)
    {
        $users = User::where('username', 'like', '%' . $request->query('q') . '%')->take(5)->get();
        return response()->json($users);
    }

}
