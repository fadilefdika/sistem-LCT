<?php

namespace App\Http\Controllers;

use App\Models\AreaLct;
use Illuminate\Http\Request;

class AreaLctController extends Controller
{
    public function index()
    {
        return view('pages.admin.master-data.area-data.index'); 
    }
    // Store data
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_area' => 'required|string|max:100',
            ]);
    
            $area = AreaLct::create([
                'nama_area' => $request->nama_area,
            ]);
    
            return response()->json([
                'message' => 'Area berhasil ditambahkan!',
                'status' => 'success',
                'area' => $area
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_area' => 'required|string|max:100',
        ]);

        $area = AreaLct::findOrFail($id);
        $area->nama_area = $request->nama_area;
        $area->save();

        return response()->json([
            'message' => 'Area berhasil diperbarui!',
            'status' => 'success',
            'area' => $area
        ]);
    }

    // Hapus data (soft delete)
    public function destroy($id)
    {
        $area = AreaLct::findOrFail($id);
        $area->delete();

        return response()->json([
            'message' => 'Area berhasil dihapus!',
            'status' => 'success'
        ]);
    }

    // Restore data yang dihapus
    public function restore($id)
    {
        $area = AreaLct::withTrashed()->findOrFail($id);
        $area->restore();

        return response()->json([
            'message' => 'Area berhasil dikembalikan!',
            'status' => 'success'
        ]);
    }
}
