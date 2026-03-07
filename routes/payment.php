<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Payment Routes
Route::prefix('payment')->controller(PaymentController::class)->name('payment.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/process', 'process')->name('process');


    Route::get('/paystack', 'paystack')->name('paystack');
    Route::post('/paystack/process', 'processPaystack')->name('paystack.process');

    Route::get('/bank-deposit', 'bankDeposit')->name('bank');
    Route::post('/bank-deposit/process', 'processBankDeposit')->name('bank.process');

    Route::get('/success', 'success')->name('success');
    Route::get('/pending', 'pending')->name('pending');
});
