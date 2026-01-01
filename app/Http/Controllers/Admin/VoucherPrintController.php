<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Voucher;
use App\Services\Accounting\VoucherPrintService;

class VoucherPrintController extends Controller
{
    private function getSignatureIds(Voucher $voucher): array
    {
        // pull = تستخدم مرة واحدة (تختفي بعد الفتح)
        return session()->pull("voucher_signatures_{$voucher->id}", []);
    }

    public function print(Voucher $voucher, VoucherPrintService $service)
    {
        return $service->streamPdf($voucher, $this->getSignatureIds($voucher));
    }

    public function pdf(Voucher $voucher, VoucherPrintService $service)
    {
        return $service->downloadPdf($voucher, $this->getSignatureIds($voucher));
    }

    public function csv(Voucher $voucher, VoucherPrintService $service)
    {
        return $service->downloadCsv($voucher);
    }
}
