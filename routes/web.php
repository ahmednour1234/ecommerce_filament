<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

// Debug route - remove after testing
Route::get('/debug/session', function () {
    return [
        'session_driver' => config('session.driver'),
        'db_connection' => config('database.default'),
        'db_database' => config('database.connections.'.config('database.default').'.database'),
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'livewire_assets' => file_exists(public_path('vendor/livewire/livewire.js')),
        'filament_assets' => file_exists(public_path('vendor/filament')),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
    ];
});
