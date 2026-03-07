<?php

use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// Registration Routes
Route::prefix('register')->controller(RegistrationController::class)->name('registration.')->group(function () {   
    Route::match(['get', 'post'], '/', 'index')->name('index');

    Route::get('/step1', 'create')->name('step1');
    Route::post('/step1', 'storePersonalInfo')->name('storePersonalInfo');

    Route::get('/step2', 'step2')->name('step2');
    Route::post('/step2', 'storeBusinessInfo')->name('storeBusinessInfo');

    Route::get('/step3', 'step3')->name('step3');
    Route::post('/step3', 'storeAttendees')->name('storeAttendees');

    Route::get('/step4', 'step4')->name('step4');
    Route::post('/step4', 'storeAddons')->name('storeAddons');

    Route::get('/step5', 'step5')->name('step5');
    Route::post('/apply-referral', 'applyReferralCode')->name('applyReferralCode');
    Route::post('/step5', 'storeCheckout')->name('storeCheckout');
});