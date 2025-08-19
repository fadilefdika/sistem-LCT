<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRoleLct;
use Illuminate\Http\Request;
use App\Models\LctDepartement;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class DepartmentDataController extends Controller
{
    public function index()
    {
        return view('pages.admin.master-data.department-data.index'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Tambah departemen
        $departemen = LctDepartement::create([
            'nama_departemen' => $request->nama_departemen,
            'user_id' => $request->user_id
        ]);

        if ($request->user_id) {
            $managerRoleId = 4; // Sesuaikan dengan ID role manager di database

            $hasRole = UserRoleLct::where('model_id', $request->user_id)
                ->where('model_type', User::class)
                ->where('role_id', $managerRoleId)
                ->exists();

            if (!$hasRole) {
                // Tambahkan role manager jika belum ada
                UserRoleLct::create([
                    'model_id' => $request->user_id,
                    'model_type' => User::class,
                    'role_id' => $managerRoleId,
                ]);

                Log::info('Role Manager ditambahkan untuk user', ['user_id' => $request->user_id]);
            } else {
                Log::info('User sudah memiliki role Manager', ['user_id' => $request->user_id]);
            }
        }

        return response()->json(['message' => 'Departemen berhasil ditambahkan!'], 200);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $department = LctDepartement::findOrFail($id);

        $oldUserId = $department->user_id; // Manager lama sebelum update
        $newUserId = $request->user_id;
        $managerRoleId = 4; // Sesuaikan dengan ID role manager

        // Update departemen
        $department->update([
            'nama_departemen' => $request->nama_departemen,
            'user_id' => $newUserId
        ]);

        // Jika ada manager lama dan berbeda dari yang baru, hapus role manager lama
        if ($oldUserId && $oldUserId != $newUserId) {
            UserRoleLct::where('model_id', $oldUserId)
                ->where('model_type', User::class)
                ->where('role_id', $managerRoleId)
                ->delete();

            Log::info('Role Manager dihapus untuk user lama', ['user_id' => $oldUserId]);
        }

        // Tambahkan role manager untuk user baru jika belum ada
        if ($newUserId) {
            $hasRole = UserRoleLct::where('model_id', $newUserId)
                ->where('model_type', User::class)
                ->where('role_id', $managerRoleId)
                ->exists();

            if (!$hasRole) {
                UserRoleLct::create([
                    'model_id' => $newUserId,
                    'model_type' => User::class,
                    'role_id' => $managerRoleId,
                ]);

                Log::info('Role Manager ditambahkan untuk user baru', ['user_id' => $newUserId]);
            } else {
                Log::info('User baru sudah memiliki role Manager', ['user_id' => $newUserId]);
            }
        }

        return response()->json(['message' => 'Departemen berhasil diperbarui!'], 200);
    }


    public function destroy($id)
    {
        try {
            $department = LctDepartement::findOrFail($id);
            $managerRoleId = 4; // Sesuaikan dengan ID role manager

            // Hapus role manager jika ada
            if ($department->user_id) {
                UserRoleLct::where('model_id', $department->user_id)
                    ->where('model_type', User::class)
                    ->where('role_id', $managerRoleId)
                    ->delete();

                Log::info('Role Manager dihapus karena departemen dihapus', ['user_id' => $department->user_id]);
            }

            // Hapus departemen
            $department->delete();

            return response()->json(['message' => 'Departemen berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus departemen', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal menghapus departemen!'], 500);
        }
    }


    public function searchUsers(Request $request)
    {
        $users = User::where('fullname', 'like', '%' . $request->query('q') . '%')->take(5)->get();
        return response()->json($users);
    }
}
