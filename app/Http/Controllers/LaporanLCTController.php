<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class LaporanLCTController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $users = User::select(['id', 'fullname', 'email', 'created_at']);

            // dd($users);

            return DataTables::of($users)
                ->addIndexColumn() // Tambahkan nomor urut otomatis
                ->make(true);
        }
        return view('pages.admin.laporan-lct.index');
    }

    public function detail()
    {
        return view('pages.admin.laporan-lct.detail');
    }
}
