<?php

use Illuminate\Support\Facades\Route;

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

require __DIR__.'/registration.php';
require __DIR__.'/payment.php';
// require __DIR__.'/auth.php';
require __DIR__.'/system.php';

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);
