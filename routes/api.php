<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/logout', function (Request $request) {
    
    $request->user()->currentAccessToken()->delete();
    
    return response()->json([]);

})->middleware('auth:api');
