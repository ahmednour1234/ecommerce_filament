<?php

namespace App\Services\Rental;

use App\Models\Rental\RentalContract;
use App\Models\Rental\RentalContractRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentalContractService
{
    public function generateContractNo(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "CR-{$year}{$month}-";
        
        $last = RentalContract::withTrashed()
            ->where('contract_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('contract_no');
        
        $seq = 1;
        if ($last && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function generateRequestNo(): string
    {
        $date = now()->format('Ymd');
        $prefix = "REQ-{$date}-";
        
        $last = RentalContractRequest::withTrashed()
            ->where('request_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('request_no');
        
        $seq = 1;
        if ($last && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function computeTotals(RentalContract $contract): array
    {
        $package = $contract->package;
        $packageTotal = $package ? $package->total : 0;
        
        $discountValue = 0;
        if ($contract->discount_type === 'percent') {
            $discountValue = $packageTotal * ($contract->discount_value / 100);
        } elseif ($contract->discount_type === 'fixed') {
            $discountValue = $contract->discount_value;
        }
        
        $subtotal = max(0, $packageTotal - $discountValue);
        $taxValue = $subtotal * ($contract->tax_percent / 100);
        $total = $subtotal + $taxValue;
        
        $paidTotal = $contract->payments()
            ->where('status', 'posted')
            ->sum('amount');
        
        $refundedTotal = $contract->payments()
            ->where('status', 'refunded')
            ->sum('amount');
        
        $paidTotal = $paidTotal - $refundedTotal;
        $remainingTotal = max(0, $total - $paidTotal);
        
        $paymentStatus = 'unpaid';
        if ($remainingTotal <= 0) {
            $paymentStatus = 'paid';
        } elseif ($paidTotal > 0) {
            $paymentStatus = 'partial';
        }
        
        if ($contract->status === 'cancelled' && $refundedTotal > 0) {
            $paymentStatus = 'refunded';
        }
        
        return [
            'subtotal' => $subtotal,
            'tax_value' => $taxValue,
            'total' => $total,
            'paid_total' => $paidTotal,
            'remaining_total' => $remainingTotal,
            'payment_status' => $paymentStatus,
        ];
    }

    public function convertRequestToContract(RentalContractRequest $request): RentalContract
    {
        return DB::transaction(function () use ($request) {
            $package = $request->desiredPackage;
            if (!$package) {
                throw new \Exception('Package not found');
            }
            
            $startDate = $request->start_date;
            $duration = $request->duration;
            $durationType = $request->duration_type;
            
            $endDate = match($durationType) {
                'day' => Carbon::parse($startDate)->addDays($duration),
                'month' => Carbon::parse($startDate)->addMonths($duration),
                'year' => Carbon::parse($startDate)->addYears($duration),
                default => Carbon::parse($startDate)->addMonths($duration),
            };
            
            $contract = RentalContract::create([
                'contract_no' => $this->generateContractNo(),
                'request_no' => $request->request_no,
                'branch_id' => $request->branch_id,
                'customer_id' => $request->customer_id,
                'worker_id' => null,
                'country_id' => $request->desired_country_id,
                'profession_id' => $request->profession_id,
                'package_id' => $package->id,
                'status' => 'active',
                'payment_status' => 'unpaid',
                'start_date' => $startDate,
                'end_date' => $endDate->toDateString(),
                'duration_type' => $durationType,
                'duration' => $duration,
                'tax_percent' => $package->tax_percent ?? 0,
                'discount_type' => 'none',
                'discount_value' => 0,
                'created_by' => auth()->id(),
            ]);
            
            $totals = $this->computeTotals($contract);
            $contract->update($totals);
            
            $request->update(['status' => 'converted']);
            
            return $contract->fresh();
        });
    }

    public function logStatusChange(RentalContract $contract, ?string $oldStatus, string $newStatus, ?string $note = null): void
    {
        $contract->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
