<?php

use App\Http\Controllers\Api\ServersApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [\App\Http\Controllers\Api\AuthController::class, 'loginUser']);

Route::apiResource('vpnservers', ServersApiController::class)->middleware('auth:sanctum');
Route::get('/vpnservers/{tg_id}/getLink', [ServersApiController::class, 'getLink'])->middleware('auth:sanctum');
