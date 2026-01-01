<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Voucher;
use App\Models\Accounting\VoucherSignature;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VoucherPrintService
{
    public function streamPdf(Voucher $voucher, array $signatureIds = [])
    {
        return $this->makePdf($voucher, $signatureIds)->stream($this->fileName($voucher));
    }

    public function downloadPdf(Voucher $voucher, array $signatureIds = [])
    {
        return $this->makePdf($voucher, $signatureIds)->download($this->fileName($voucher));
    }

    public function downloadCsv(Voucher $voucher): StreamedResponse
    {
        $fileName = "voucher-{$voucher->voucher_number}.csv";

        return response()->streamDownload(function () use ($voucher) {
            $rows = [
                ['Voucher Number', $voucher->voucher_number],
                ['Type', $voucher->type],
                ['Date', optional($voucher->voucher_date)->format('Y-m-d')],
                ['Amount', $voucher->amount],
                ['Account', ($voucher->account?->code ?? '') . ' - ' . ($voucher->account?->name ?? '')],
                ['Branch', $voucher->branch?->name ?? ''],
                ['Cost Center', $voucher->costCenter?->name ?? ''],
                ['Reference', $voucher->reference ?? ''],
                ['Description', $voucher->description ?? ''],
            ];

            $out = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function makePdf(Voucher $voucher, array $signatureIds = [])
    {
        // ✅ load relations
        $voucher->loadMissing(['account', 'branch', 'costCenter', 'creator']);

        $isRtl = in_array(app()->getLocale(), ['ar', 'fa', 'ur']);

        $signatures = collect();
        if (!empty($signatureIds)) {
            $signatures = VoucherSignature::whereIn('id', $signatureIds)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($sig) {
                    $sig->image_url = $sig->image_path
                        ? Storage::disk('public')->url($sig->image_path)
                        : null;
                    return $sig;
                });
        }

        // amount in words (put your own helper)
        $amountInWords = $this->amountInWords($voucher->amount);

        // ✅ dompdf
        return app('dompdf.wrapper')->loadView('accounting.vouchers.pdf', [
            'voucher' => $voucher,
            'signatures' => $signatures,
            'is_rtl' => $isRtl,
            'amount_in_words' => $amountInWords,
        ]);
    }

    protected function fileName(Voucher $voucher): string
    {
        return "voucher-{$voucher->voucher_number}.pdf";
    }

    protected function amountInWords($amount): string
    {
        // replace with your real implementation
        return (string) $amount;
    }
}
