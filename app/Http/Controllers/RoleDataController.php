<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\User;
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

            // Cari data relasi PIC dan Departemen berdasarkan ID
            $picRelation = LctDepartemenPic::find($id);

            if (!$picRelation) {
                return response()->json(['message' => 'Data PIC tidak ditemukan!'], 404);
            }

            // Cari PIC lama yang akan digantikan
            $oldPic = Pic::find($picRelation->pic_id);

            if ($oldPic) {
                // Pastikan hanya melakukan soft delete jika PIC lama ada
                if ($oldPic->deleted_at === null) {
                    $oldPic->update([
                        'deleted_at' => now(),
                        'updated_at' => now()
                    ]);
                    return response()->json(['message' => 'PIC lama berhasil diperbarui dan dihapus secara sementara.'], 200);
                } else {
                    return response()->json(['message' => 'PIC lama sudah dihapus sebelumnya.'], 200);
                }
            } else {
                return response()->json(['message' => 'PIC yang terhubung dengan ID ini tidak ditemukan.'], 404);
            }            
            
            // Periksa apakah user_id yang baru sudah terdaftar sebagai PIC
            $existingPic = Pic::where('user_id', $request->user_id)->first();
            Log::info("PIC baru ditemukan atau belum:", ['existingPic' => $existingPic]);

            // Jika belum ada, buat PIC baru
            if (!$existingPic) {
                $newPic = Pic::create([
                    'user_id' => $request->user_id
                ]);
                Log::info("PIC baru berhasil dibuat dengan ID: " . $newPic->id);
            } else {
                $newPic = $existingPic;
                Log::info("Menggunakan PIC yang sudah ada dengan ID: " . $newPic->id);
            }

            // Update hubungan di pivot table dengan PIC baru
            $picRelation->update([
                'pic_id' => $newPic->id,
                'departemen_id' => $request->department_id
            ]);
            Log::info("Relasi LctDepartemenPic berhasil diperbarui dengan pic_id: " . $newPic->id);

            return response()->json(['message' => 'PIC berhasil diperbarui!'], 200);
        } catch (\Exception $e) {
            Log::error("Error updating PIC: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
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
