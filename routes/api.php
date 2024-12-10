<?php

use App\Http\Controllers\Business\Api\AuthController;
use App\Http\Controllers\Business\Api\ServersApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [AuthController::class, 'loginUser']);

Route::post('vpnservers', [ServersApiController::class, 'store'])->name('vpnservers.store')->middleware('auth:sanctum');
Route::put('vpnservers/{userId}', [ServersApiController::class, 'update'])->name('vpnservers.update')->middleware('auth:sanctum');
Route::delete('vpnservers/{userId}', [ServersApiController::class, 'destroy'])->name('vpnservers.destroy')->middleware('auth:sanctum');

Route::get('/vpnservers/{tg_id}/getLink/{key_type}', [ServersApiController::class, 'getLink'])->middleware('auth:sanctum');
Route::get('/vpnservers/{tg_id}/getAmneziaFile', [ServersApiController::class, 'getFile'])->middleware('auth:sanctum');
Route::get('/vpnservers/getUsersList', [ServersApiController::class, 'getUsersList'])->middleware('auth:sanctum');
Route::get('/vpnservers/{tg_id}/getUser', [ServersApiController::class, 'getUser'])->middleware('auth:sanctum');

