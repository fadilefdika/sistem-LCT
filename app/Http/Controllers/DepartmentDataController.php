<?php

namespace App\Http\Controllers;

use App\Models\LctDepartement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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

        LctDepartement::create([
            'nama_departemen' => $request->nama_departemen,
            'user_id' => $request->user_id
        ]);

        return response()->json(['message' => 'Departemen berhasil ditambahkan!'], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $department = LctDepartement::findOrFail($id);
        $department->update([
            'nama_departemen' => $request->nama_departemen,
            'user_id' => $request->user_id
        ]);

        return response()->json(['message' => 'Departemen berhasil diperbarui!'], 200);
    }

    public function destroy($id)
    {
        try {
            $department = LctDepartement::findOrFail($id);
            $department->delete();
            return response()->json(['message' => 'Departemen berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus departemen!'], 500);
        }
    }

    public function searchUsers(Request $request)
    {
        $users = User::where('fullname', 'like', '%' . $request->query('q') . '%')->take(5)->get();
        return response()->json($users);
    }
}
