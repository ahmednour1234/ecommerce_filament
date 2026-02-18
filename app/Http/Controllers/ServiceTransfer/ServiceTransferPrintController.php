<?php

namespace App\Http\Controllers\ServiceTransfer;

use App\Http\Controllers\Controller;
use App\Models\ServiceTransfer;
use App\Models\ServiceTransferPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ServiceTransferPrintController extends Controller
{
    public function printInvoice($id)
    {
        $transfer = ServiceTransfer::with(['customer', 'worker', 'package', 'branch', 'nationality', 'payments', 'createdBy'])
            ->findOrFail($id);

        $isRtl = app()->getLocale() === 'ar';

        return Pdf::loadView('print.service_transfer_invoice', [
            'transfer' => $transfer,
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
        ->setOption('fontCache', storage_path('fonts'))
        ->download("invoice_{$transfer->request_no}.pdf");
    }

    public function printReceipt($paymentId)
    {
        $payment = ServiceTransferPayment::with(['transfer.customer', 'transfer.branch', 'paymentMethod', 'fromAccount', 'toAccount', 'createdBy'])
            ->findOrFail($paymentId);

        $isRtl = app()->getLocale() === 'ar';

        return Pdf::loadView('print.service_transfer_receipt', [
            'payment' => $payment,
            'transfer' => $payment->transfer,
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
        ->setOption('fontCache', storage_path('fonts'))
        ->download("receipt_{$payment->payment_no}.pdf");
    }
}
