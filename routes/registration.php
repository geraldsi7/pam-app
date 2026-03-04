<?php

use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// Registration Routes
Route::get('/register', [RegistrationController::class, 'index'])->name('registration.index');
Route::get('/register/step1', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/register/step1', [RegistrationController::class, 'storePersonalInfo'])->name('registration.storePersonalInfo');

Route::get('/register/step2', [RegistrationController::class, 'step2'])->name('registration.step2');
Route::post('/register/step2', [RegistrationController::class, 'storeBusinessInfo'])->name('registration.storeBusinessInfo');

Route::get('/register/step3', [RegistrationController::class, 'step3'])->name('registration.step3');
Route::post('/register/step3', [RegistrationController::class, 'storeAttendees'])->name('registration.storeAttendees');

Route::get('/register/step4', [RegistrationController::class, 'step4'])->name('registration.step4');
Route::post('/register/step4', [RegistrationController::class, 'storeAddons'])->name('registration.storeAddons');

Route::get('/register/step5', [RegistrationController::class, 'step5'])->name('registration.step5');
Route::post('/register/step5', [RegistrationController::class, 'storeCheckout'])->name('registration.storeCheckout');
