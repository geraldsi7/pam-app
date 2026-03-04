<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Payment Routes
Route::get('/payment/paystack', [PaymentController::class, 'paystack'])->name('payment.paystack');
Route::post('/payment/paystack/process', [PaymentController::class, 'processPaystack'])->name('payment.paystack.process');

Route::get('/payment/bank-deposit', [PaymentController::class, 'bankDeposit'])->name('payment.bank');
Route::post('/payment/bank-deposit/process', [PaymentController::class, 'processBankDeposit'])->name('payment.bank.process');

Route::get('/payment/success', [PaymentController::class, 'success'])->name('registration.success');
Route::get('/payment/pending', [PaymentController::class, 'pending'])->name('registration.pending');
