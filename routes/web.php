<?php

use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/daftar', [RegistrationController::class, 'store'])
    ->middleware('throttle:10,1')->name('registration.store');
