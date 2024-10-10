<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VpnusersApiController;

Route::middleware('auth:api')->prefix('api')->group(function () {
    Route::apiResource('vpnusers', VpnusersApiController::class);
    Route::get('/token', function (Request $request) {
        $token = csrf_token();
        return response()->json([
            'token' => $token,
        ], 200);
    });
    Route::get('/vpnusers/{tg_id}/getLink', [VpnusersApiController::class, 'getLink']);
});
