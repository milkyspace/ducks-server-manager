<?php

use App\Http\Controllers\Api\VpnusersApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [\App\Http\Controllers\Api\AuthController::class, 'loginUser']);

Route::apiResource('vpnusers', VpnusersApiController::class)->middleware('auth:sanctum');
Route::get('/vpnusers/{tg_id}/getLink', [VpnusersApiController::class, 'getLink'])->middleware('auth:sanctum');
Route::post('/tokens/create', function (Request $request) {
    dd($request->user());
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

//Route::get('/token', function (Request $request) {
//    $token = csrf_token();
//    return response()->json([
//        'token' => $token,
//    ], 200);
//});
