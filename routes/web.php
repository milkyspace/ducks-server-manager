<?php

use App\Http\Controllers\Business\Auth\ApiTokenController;
use App\Http\Controllers\Business\Pages\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/token', [ApiTokenController::class, 'update'])->name('user.tokenupdate');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/', function () {
        return redirect('/servers/');
    });
    Route::resource('servers', \App\Http\Controllers\Business\Pages\ServerController::class);
    Route::resource('vpnusersweb', \App\Http\Controllers\Business\Pages\VpnusersController::class);
});

require __DIR__ . '/auth.php';
