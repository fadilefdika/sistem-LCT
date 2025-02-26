<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;

class AdminController extends Controller
{
    public function index()
    {

        return view('pages/admin/dashboard')->with('layout', 'layouts.admin');
    }

}
