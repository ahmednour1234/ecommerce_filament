<?php

namespace App\Services\Rental;

use App\Models\Rental\RentalContract;
use Barryvdh\DomPDF\Facade\Pdf;

class RentalContractPrintService
{
    public function contractPdf(RentalContract $contract)
    {
        $contract->load(['customer', 'worker', 'package', 'branch', 'country', 'profession', 'payments']);
        
        $isRtl = app()->getLocale() === 'ar';
        
        return Pdf::loadView('print.rental.contract', [
            'contract' => $contract,
            'is_rtl' => $isRtl,
        ])
        ->setPaper('a4')
        ->setOption('enable-local-file-access', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true)
        ->setOption('defaultFont', $isRtl ? 'Cairo' : 'DejaVu Sans')
        ->setOption('fontDir', [
            public_path('fonts'),
            resource_path('fonts'),
            storage_path('fonts'),
        ])
        ->setOption('fontCache', storage_path('fonts'));
    }

    public function invoicePdf(RentalContract $contract)
    {
        $contract->load(['customer', 'worker', 'package', 'branch', 'country', 'profession', 'payments']);
        
        $isRtl = app()->getLocale() === 'ar';
        
        return Pdf::loadView('print.rental.invoice', [
            'contract' => $contract,
            'is_rtl' => $isRtl,
        ])
        ->setPaper('a4')
        ->setOption('enable-local-file-access', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true)
        ->setOption('defaultFont', $isRtl ? 'Cairo' : 'DejaVu Sans')
        ->setOption('fontDir', [
            public_path('fonts'),
            resource_path('fonts'),
            storage_path('fonts'),
        ])
        ->setOption('fontCache', storage_path('fonts'));
    }
}
