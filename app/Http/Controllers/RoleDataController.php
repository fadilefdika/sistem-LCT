<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\User;
use App\Models\UserRoleLct;
use Illuminate\Http\Request;
use App\Models\LctDepartement;
use App\Models\LctDepartemenPic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class RoleDataController extends Controller
{
    public function index()
    {
        return view('pages.admin.master-data.role-data.index');
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'department_id' => 'required|exists:lct_departement,id',
                'user_id' => 'required|exists:users,id',
            ]);

            // Cek apakah user_id sudah ada di tabel lct_pic
            $pic = Pic::where('user_id', $request->user_id)->first();

            // Jika user belum terdaftar di lct_pic, tambahkan terlebih dahulu
            if (!$pic) {
                $pic = Pic::create(['user_id' => $request->user_id]);
            }
            
            $hasRole = UserRoleLct::where('model_id', $request->user_id)
                ->where('model_type', User::class)
                ->where('role_id', 2)
                ->exists();

            if (!$hasRole) {
                // Jika belum, tambahkan role PIC
                UserRoleLct::create([
                    'model_id' => $request->user_id,
                    'model_type' => User::class,
                    'role_id' => 2,
                ]);

                Log::info('Role PIC ditambahkan untuk user', ['user_id' => $request->user_id]);
            } else {
                Log::info('User sudah memiliki role PIC', ['user_id' => $request->user_id]);
            }

            // Cek apakah PIC sudah ada di departemen tersebut
            $existingPic = LctDepartemenPic::where('departemen_id', $request->department_id)
                                        ->where('pic_id', $pic->id)
                                        ->first();

            if ($existingPic) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Duplicate Entry',
                    'message' => 'The PIC is already assigned to this department.'
                ], 400);
            }

            // Soft delete PIC lama jika berpindah departemen
            LctDepartemenPic::where('pic_id', $pic->id)->delete();

            // Tambahkan relasi baru antara PIC dan Departemen
            $departemenPic = LctDepartemenPic::create([
                'departemen_id' => $request->department_id,
                'pic_id' => $pic->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'PIC successfully assigned to the department!',
                'data' => $departemenPic
            ], 201);
            

        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validasi gagal', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Error storing PIC: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function update(Request $request, $id)
{
    try {
        $request->validate([
            'department_id' => 'required|exists:lct_departement,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $picRelation = LctDepartemenPic::find($id);
        if (!$picRelation) {
            return response()->json(['message' => 'Data PIC tidak ditemukan!'], 404);
        }

        $newUserId = $request->user_id;

        // Cari PIC lama dan lakukan soft delete
        $oldPic = Pic::find($picRelation->pic_id);
        if ($oldPic && $oldPic->deleted_at === null) {
            $oldPic->update([
                'deleted_at' => now(),
                'updated_at' => now()
            ]);
            Log::info("PIC lama di-soft delete", ['pic_id' => $oldPic->id]);
        }

        // Cek apakah user baru sudah ada sebagai PIC
        $existingPic = Pic::where('user_id', $newUserId)->first();
        if (!$existingPic) {
            $newPic = Pic::create([
                'user_id' => $newUserId
            ]);
            Log::info("PIC baru dibuat", ['pic_id' => $newPic->id]);
        } else {
            $newPic = $existingPic;
            Log::info("Menggunakan PIC yang sudah ada", ['pic_id' => $newPic->id]);
        }

        // Update pivot table dengan PIC baru
        $picRelation->update([
            'pic_id'        => $newPic->id,
            'departemen_id' => $request->department_id
        ]);
        Log::info("Pivot LctDepartemenPic diperbarui", [
            'departemen_id' => $request->department_id,
            'pic_id'        => $newPic->id
        ]);

        // Tambahkan role Manager untuk user baru
        $hasRole = UserRoleLct::where('model_id', $newUserId)
            ->where('model_type', User::class)
            ->where('role_id', 2)
            ->exists();

        if (!$hasRole) {
            UserRoleLct::create([
                'model_id' => $newUserId,
                'model_type' => User::class,
                'role_id' => 2,
            ]);
            Log::info('Role Manager ditambahkan', ['user_id' => $newUserId]);
        } else {
            Log::info('User sudah punya role Manager', ['user_id' => $newUserId]);
        }

        return response()->json(['message' => 'PIC berhasil diperbarui!'], 200);

    } catch (\Exception $e) {
        Log::error("Error updating PIC: " . $e->getMessage());
        return response()->json([
            'error'   => 'Internal Server Error',
            'message' => $e->getMessage()
        ], 500);
    }
}


    
    public function destroy($id)
    {
        Log::info("Menghapus PIC dengan ID: " . $id);
        try {
            // Cari PIC yang akan dihapus
            $pic = LctDepartemenPic::findOrFail($id);
            
            // Soft delete departemen pic
            $pic->delete();

            return response()->json(['message' => 'PIC berhasil dihapus dari departemen!'], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting PIC: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menghapus PIC!'], 500);
        }
    }

    public function searchUsers(Request $request)
    {
        $users = User::where('fullname', 'like', '%' . $request->query('q') . '%')->take(5)->get();
        return response()->json($users);
    }

    public function searchDepartment(Request $request)
    {
        $departments = LctDepartement::where('nama_departemen', 'like', '%' . $request->query('q') . '%')->take(5)->get();
        return response()->json($departments);
    }
}
