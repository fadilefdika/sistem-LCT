<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;

class AdminController extends Controller
{
    public function index()
    {
        $dataFeed = new DataFeed();

        return view('pages/admin/dashboard', compact('dataFeed'))->with('layout', 'layouts.admin');
    }

}
