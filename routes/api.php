<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/report-form', function (Request $request) {
            return response()->json($request->user());
        });
    });
});

Route::get('/public-key', function () {
    return response()->file(storage_path('app/rsa_public.pem'));
});

