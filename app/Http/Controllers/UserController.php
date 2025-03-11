<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all(); // Ambil semua kategori dari tabel
    
        return view('pages.user.form', [
            'attributes' => collect(['headerVariant' => 'blue']),
            'layout' => 'layouts.user',
            'kategori' => $kategori, // Pastikan kategori dikirim ke view
        ]);
    }
    
}
