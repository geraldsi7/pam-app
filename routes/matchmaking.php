<?php

use App\Http\Controllers\MatchmakingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'completed.registration'])->group(function () {
    // Matchmaking directory
    Route::get('/matchmaking', [MatchmakingController::class, 'index'])->name('matchmaking.index');

    // Business detail view
    Route::get('/matchmaking/business/{business}', [MatchmakingController::class, 'show'])->name('matchmaking.show');

    // Request access to business details
    Route::post('/matchmaking/business/{business}/request', [MatchmakingController::class, 'requestAccess'])->name('matchmaking.request');

    // Handle match requests (approve/reject)
    Route::post('/matchmaking/requests/{matchRequest}/handle', [MatchmakingController::class, 'handleRequest'])->name('matchmaking.handle-request');

    // View user's match requests
    Route::get('/matchmaking/requests', [MatchmakingController::class, 'requests'])->name('matchmaking.requests');
});