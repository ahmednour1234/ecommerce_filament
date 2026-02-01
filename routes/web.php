<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VoucherPrintController;
use App\Http\Controllers\Finance\BranchTransactionPrintController;

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

    // Biometric API v1
    Route::prefix('v1/biometric')->group(function () {
        Route::get('/ping', [App\Http\Controllers\Api\V1\BiometricController::class, 'ping'])
            ->middleware('throttle:60,1');
        Route::post('/logs', [App\Http\Controllers\Api\V1\BiometricController::class, 'storeLogs'])
            ->middleware(['throttle:30,1', 'max_request_size']);
    });
});

// Export Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/exports/print', [App\Http\Controllers\ExportController::class, 'print'])
        ->name('filament.exports.print');

    Route::get('/admin/reports/{report}/print', [App\Http\Controllers\ExportController::class, 'reportPrint'])
        ->name('reports.print');

    Route::get('/admin/exports/branch-statement-pdf', [App\Http\Controllers\ExportController::class, 'branchStatementPdf'])
        ->name('filament.exports.branch-statement-pdf');

    Route::get('/admin/exports/test-arabic-pdf', [App\Http\Controllers\ExportController::class, 'testArabicPdf'])
        ->name('filament.exports.test-arabic-pdf');

    // Rental Contract PDF Routes
    Route::get('/rental/contracts/{contract}/print', function ($contract) {
        $contract = \App\Models\Rental\RentalContract::findOrFail($contract);
        $service = app(\App\Services\Rental\RentalContractPrintService::class);
        return $service->contractPdf($contract)->download("contract_{$contract->contract_no}.pdf");
    })->name('rental.contracts.print');

    Route::get('/rental/contracts/{contract}/invoice', function ($contract) {
        $contract = \App\Models\Rental\RentalContract::findOrFail($contract);
        $service = app(\App\Services\Rental\RentalContractPrintService::class);
        return $service->invoicePdf($contract)->download("invoice_{$contract->contract_no}.pdf");
    })->name('rental.contracts.invoice');
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

    Route::middleware(['web', 'auth'])
        ->get('/admin/finance/branch-transactions/{branchTransaction}/print', BranchTransactionPrintController::class)
        ->name('finance.branch-transactions.print');

    Route::middleware(['web', 'auth'])
        ->get('/admin/finance/import/template', [App\Http\Controllers\Finance\FinanceImportTemplateController::class, 'download'])
        ->name('finance.import.template');
