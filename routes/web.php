<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VoucherPrintController;

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
    
    // HR Attendance Device Log API (no CSRF for device push)
    Route::post('/hr/attendance/device-log', [App\Http\Controllers\Api\Hr\DeviceLogController::class, 'store'])
        ->middleware('throttle:60,1');
});

// Export Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/exports/print', [App\Http\Controllers\ExportController::class, 'print'])
        ->name('filament.exports.print');

    Route::get('/admin/reports/{report}/print', [App\Http\Controllers\ExportController::class, 'reportPrint'])
        ->name('reports.print');
});

Route::middleware(['web', 'auth'])
    ->prefix('admin')
    ->group(function () {
        Route::get('print/vouchers/{voucher}', [VoucherPrintController::class, 'print'])
            ->name('admin.vouchers.print');

        Route::get('print/vouchers/{voucher}/pdf', [VoucherPrintController::class, 'pdf'])
            ->name('admin.vouchers.pdf');

        Route::get('print/vouchers/{voucher}/csv', [VoucherPrintController::class, 'csv'])
            ->name('admin.vouchers.csv');

        Route::get('hr/holidays-calendar/json', [App\Http\Controllers\HR\HolidaysCalendarController::class, 'getHolidaysJson'])
            ->name('filament.admin.pages.hr.holidays-calendar.json');
    });
