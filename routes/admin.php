<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AgentManagementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentOperationsController;
use App\Http\Controllers\Admin\RegistrationManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::prefix('registrations')->name('registrations.')->group(function () {
        Route::get('/', [RegistrationManagementController::class, 'index'])->name('index');
        Route::get('/{registration}', [RegistrationManagementController::class, 'show'])->name('show');
        Route::post('/{registration}/approve', [RegistrationManagementController::class, 'approve'])->name('approve');
        Route::post('/{registration}/reject', [RegistrationManagementController::class, 'reject'])->name('reject');
    });

    Route::prefix('agents')->name('agents.')->group(function () {
        Route::get('/', [AgentManagementController::class, 'index'])->name('index');
        Route::post('/', [AgentManagementController::class, 'store'])->name('store');
        Route::put('/{agent}', [AgentManagementController::class, 'update'])->name('update');
        Route::patch('/{agent}/toggle', [AgentManagementController::class, 'toggle'])->name('toggle');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::patch('/{user}/toggle', [UserManagementController::class, 'toggle'])->name('toggle');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentOperationsController::class, 'index'])->name('index');
        Route::post('/{payment}/verify', [PaymentOperationsController::class, 'verify'])->name('verify');
        Route::post('/{payment}/reject', [PaymentOperationsController::class, 'reject'])->name('reject');
    });

    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
});
