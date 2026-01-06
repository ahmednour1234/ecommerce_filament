<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Voucher;
use App\Models\Accounting\VoucherSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class VoucherPrintService
{
    /**
     * Generate PDF stream/preview for voucher with selected signatures.
     */
    public function streamPdf(Voucher $voucher, array $signatureIds = [])
    {
        return $this->buildPdf($voucher, $signatureIds)
            ->stream($this->fileName($voucher));
    }

    /**
     * Download PDF for voucher with selected signatures.
     */
    public function downloadPdf(Voucher $voucher, array $signatureIds = [])
    {
        return $this->buildPdf($voucher, $signatureIds)
            ->download($this->fileName($voucher));
    }

    /**
     * Core: build dompdf instance.
     */
    protected function buildPdf(Voucher $voucher, array $signatureIds = [])
    {
        $voucher->loadMissing(['account', 'branch', 'costCenter', 'creator']);

        $isRtl = app()->getLocale() === 'ar';

        $signatures = $this->getSignatures($voucher, $signatureIds);

        // IMPORTANT: sanitize everything to valid UTF-8
        $payload = [
            'voucher'         => $this->sanitizeVoucher($voucher),
            'is_rtl'          => $isRtl,
            'amount_in_words' => $this->cleanUtf8($this->amountToWords($voucher->amount, $isRtl)),
            'signatures'      => $this->sanitizeSignatures($signatures),
        ];

        // view path must exist: resources/views/print/vouchers/pdf.blade.php
        return Pdf::loadView('print.vouchers.pdf', $payload)
            ->setPaper('a4')
            ->setOption('enable-local-file-access', true)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', $isRtl ? 'Tajawal' : 'DejaVu Sans')
            ->setOption('fontDir', [
                public_path('fonts'),
                resource_path('fonts'),
            ]);
    }

    protected function fileName(Voucher $voucher): string
    {
        $type = $voucher->type === 'payment' ? 'payment' : 'receipt';
        return "voucher_{$type}_{$voucher->voucher_number}.pdf";
    }

    protected function getSignatures(Voucher $voucher, array $signatureIds): Collection
    {
        if (empty($signatureIds)) {
            return collect();
        }

        // you can also validate type here if you want
        return VoucherSignature::query()
            ->whereIn('id', $signatureIds)
            ->get();
    }

    protected function sanitizeVoucher(Voucher $voucher): Voucher
    {
        // Clone-like sanitize fields used in view to avoid bad bytes from DB
        $voucher->voucher_number = $this->cleanUtf8($voucher->voucher_number);
        $voucher->description    = $this->cleanUtf8($voucher->description);
        $voucher->reference      = $this->cleanUtf8($voucher->reference);

        if ($voucher->relationLoaded('account') && $voucher->account) {
            $voucher->account->code = $this->cleanUtf8($voucher->account->code);
            $voucher->account->name = $this->cleanUtf8($voucher->account->name);
        }

        if ($voucher->relationLoaded('branch') && $voucher->branch) {
            $voucher->branch->name = $this->cleanUtf8($voucher->branch->name);
        }

        if ($voucher->relationLoaded('costCenter') && $voucher->costCenter) {
            $voucher->costCenter->name = $this->cleanUtf8($voucher->costCenter->name);
        }

        if ($voucher->relationLoaded('creator') && $voucher->creator) {
            $voucher->creator->name = $this->cleanUtf8($voucher->creator->name);
        }

        return $voucher;
    }

    protected function sanitizeSignatures(Collection $signatures): Collection
    {
        return $signatures->map(function ($sig) {
            $sig->name  = $this->cleanUtf8($sig->name);
            $sig->title = $this->cleanUtf8($sig->title);

            // IMPORTANT: dompdf prefers local absolute path
            $sig->image_url = $sig->image_path
                ? public_path('storage/' . ltrim($sig->image_path, '/'))
                : null;

            return $sig;
        });
    }

    /**
     * Convert amount to words.
     * Replace this with your existing helper if you have one.
     */
    protected function amountToWords($amount, bool $isRtl): string
    {
        // Simple fallback (you can plug your own converter)
        $amount = number_format((float) $amount, 2, '.', '');
        return $isRtl
            ? "فقط {$amount} رقمًا"
            : "Only {$amount}";
    }

    /**
     * The main UTF-8 cleaner.
     */
    protected function cleanUtf8($value): string
    {
        if ($value === null) {
            return '';
        }

        $value = (string) $value;

        // If not valid UTF-8, convert
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'auto');
        }

        // Remove invalid bytes
        return iconv('UTF-8', 'UTF-8//IGNORE', $value) ?: '';
    }
}
