<?php

use App\Http\Controllers\Auth\ApiTokenController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'main'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/token', [ApiTokenController::class, 'update'])->name('user.tokenupdate');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', function () {
        return redirect('/servers/');
    });
    Route::resource('servers', \App\Http\Controllers\ServerController::class);

    Route::resource('vpnusersweb', \App\Http\Controllers\VpnusersController::class);
});

require __DIR__ . '/auth.php';
