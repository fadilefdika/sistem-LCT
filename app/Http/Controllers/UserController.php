<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('pages/user/form',['attributes' => collect(['headerVariant' => 'blue'])])->with('layout', 'layouts.user');
    }
}
