<?php

namespace Modules\CompanyVisas\Services;

use Modules\CompanyVisas\Entities\CompanyVisaRequest;

class CompanyVisaRequestService
{
    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $lastRequest = CompanyVisaRequest::where('code', 'like', "VISA-{$date}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRequest) {
            $lastNumber = (int) substr($lastRequest->code, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('VISA-%s-%04d', $date, $nextNumber);
    }
}
