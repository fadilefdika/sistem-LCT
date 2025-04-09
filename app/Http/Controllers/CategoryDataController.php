<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryDataController extends Controller
{
    public function index()
    {
        return view('pages.admin.master-data.category-data.index'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]); 

        return response()->json([
            'message' => 'Category created successfully.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $category = Kategori::findOrFail($id);
        $category->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json([
            'message' => 'Category updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        try {
            $category = Kategori::findOrFail($id);
            $category->delete();
            return response()->json(['message' => 'Category deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete category!'], 500);
        }
    }
}
