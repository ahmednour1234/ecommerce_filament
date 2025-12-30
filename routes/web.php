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

// API Routes
Route::prefix('api')->middleware(['web'])->group(function () {
    Route::get('/exchange-rate', [App\Http\Controllers\Api\ExchangeRateController::class, 'getRate']);
    Route::post('/exchange-rates/batch', [App\Http\Controllers\Api\ExchangeRateController::class, 'getBatchRates']);
});

// Export Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/exports/print', [App\Http\Controllers\ExportController::class, 'print'])
        ->name('filament.exports.print');
    
    Route::get('/admin/reports/{report}/print', [App\Http\Controllers\ExportController::class, 'reportPrint'])
        ->name('reports.print');
});
