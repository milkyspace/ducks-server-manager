<?php

use App\Http\Controllers\Api\VpnusersApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('vpnusers', VpnusersApiController::class)->middleware('auth:sanctum');
Route::get('/vpnusers/{tg_id}/getLink', [VpnusersApiController::class, 'getLink'])->middleware('auth:sanctum');

//Route::get('/token', function (Request $request) {
//    $token = csrf_token();
//    return response()->json([
//        'token' => $token,
//    ], 200);
//});
