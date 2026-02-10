<?php

namespace App\Services;

use App\Models\Complaint;

class ComplaintService
{
    public function generateComplaintNo(): string
    {
        $date = now()->format('Ymd');
        $prefix = "COMP-{$date}-";
        
        $last = Complaint::withTrashed()
            ->where('complaint_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('complaint_no');
        
        $seq = 1;
        if ($last && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
