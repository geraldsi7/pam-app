<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__.'/registration.php';
require __DIR__.'/payment.php';
require __DIR__.'/auth.php';
require __DIR__.'/system.php';
require __DIR__.'/matchmaking.php';
require __DIR__.'/admin.php';

Route::get('/', function () {
    return redirect()->route('registration.index');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
