<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/daftar', [RegistrationController::class, 'store'])
    ->middleware('throttle:10,1')->name('registration.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:20,1')->name('login.store');

    Route::middleware(['auth', EnsureAdmin::class])->group(function () {
        Route::get('/', [AdminRegistrationController::class, 'index'])->name('index');
        Route::get('/export', [AdminRegistrationController::class, 'export'])->name('export');
        Route::get('/pendaftar/{registration}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    });
});
