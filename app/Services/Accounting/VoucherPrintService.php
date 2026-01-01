<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Voucher;
use App\Models\Accounting\VoucherSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class VoucherPrintService
{
    /**
     * Generate PDF for voucher with selected signatures
     */
    public function generatePdf(Voucher $voucher, array $signatureIds = []): Response
    {
        $signatures = $this->getSignatures($signatureIds);
        
        // Save signature usage for audit trail
        $this->saveSignatureUsage($voucher, $signatureIds);
        
        $data = $this->getPrintViewData($voucher, $signatures);
        
        $pdf = Pdf::loadView('print.vouchers.show', $data);
        $pdf->setPaper('a4', 'portrait');
        
        $filename = $this->getFilename($voucher);
        
        return $pdf->download($filename);
    }

    /**
     * Stream PDF instead of downloading
     */
    public function streamPdf(Voucher $voucher, array $signatureIds = []): Response
    {
        $signatures = $this->getSignatures($signatureIds);
        
        // Save signature usage for audit trail
        $this->saveSignatureUsage($voucher, $signatureIds);
        
        $data = $this->getPrintViewData($voucher, $signatures);
        
        $pdf = Pdf::loadView('print.vouchers.show', $data);
        $pdf->setPaper('a4', 'portrait');
        
        $filename = $this->getFilename($voucher);
        
        return $pdf->stream($filename);
    }

    /**
     * Get print view data
     */
    public function getPrintViewData(Voucher $voucher, array $signatures = []): array
    {
        // Load relationships
        $voucher->load(['account', 'branch', 'costCenter', 'creator']);
        
        return [
            'voucher' => $voucher,
            'signatures' => $signatures,
            'amount_in_words' => $this->convertAmountToWords($voucher->amount),
            'is_rtl' => app()->getLocale() === 'ar',
        ];
    }

    /**
     * Get signatures by IDs in order
     */
    protected function getSignatures(array $signatureIds): array
    {
        if (empty($signatureIds)) {
            return [];
        }

        $signatures = VoucherSignature::whereIn('id', $signatureIds)
            ->get()
            ->keyBy('id');

        // Return in the order of IDs provided
        $ordered = [];
        foreach ($signatureIds as $id) {
            if (isset($signatures[$id])) {
                $ordered[] = $signatures[$id];
            }
        }

        return $ordered;
    }

    /**
     * Save signature usage for audit trail
     */
    protected function saveSignatureUsage(Voucher $voucher, array $signatureIds): void
    {
        if (empty($signatureIds)) {
            return;
        }

        // Delete existing usage records for this voucher
        DB::table('voucher_signature_usage')
            ->where('voucher_id', $voucher->id)
            ->delete();

        // Insert new usage records
        $usageData = [];
        foreach ($signatureIds as $position => $signatureId) {
            $usageData[] = [
                'voucher_id' => $voucher->id,
                'signature_id' => $signatureId,
                'position' => $position + 1, // 1-based position
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($usageData)) {
            DB::table('voucher_signature_usage')->insert($usageData);
        }
    }

    /**
     * Generate filename for PDF
     */
    protected function getFilename(Voucher $voucher): string
    {
        $type = $voucher->type === 'payment' ? 'Payment' : 'Receipt';
        $number = $voucher->voucher_number;
        $date = $voucher->voucher_date->format('Y-m-d');
        
        return "{$type}_Voucher_{$number}_{$date}.pdf";
    }

    /**
     * Convert amount to words (basic implementation)
     * You may want to use a library like "kwn/number-to-words" for better support
     */
    protected function convertAmountToWords(float $amount): string
    {
        // Basic implementation - you can enhance this with a proper library
        $whole = (int) $amount;
        $decimal = (int) (($amount - $whole) * 100);
        
        $words = $this->numberToWords($whole);
        
        if ($decimal > 0) {
            $words .= ' and ' . $this->numberToWords($decimal) . ' cents';
        }
        
        return ucfirst($words) . ' only';
    }

    /**
     * Convert number to words (simplified)
     */
    protected function numberToWords(int $number): string
    {
        if ($number === 0) {
            return 'zero';
        }

        $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        $teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        
        if ($number < 10) {
            return $ones[$number];
        } elseif ($number < 20) {
            return $teens[$number - 10];
        } elseif ($number < 100) {
            $tensDigit = (int) ($number / 10);
            $onesDigit = $number % 10;
            return $tens[$tensDigit] . ($onesDigit > 0 ? '-' . $ones[$onesDigit] : '');
        } elseif ($number < 1000) {
            $hundreds = (int) ($number / 100);
            $remainder = $number % 100;
            return $ones[$hundreds] . ' hundred' . ($remainder > 0 ? ' ' . $this->numberToWords($remainder) : '');
        } else {
            // For larger numbers, return the number itself
            return number_format($number);
        }
    }
}

