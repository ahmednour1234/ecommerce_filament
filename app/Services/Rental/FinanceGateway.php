<?php

namespace App\Services\Rental;

use App\Models\Rental\RentalContract;

interface FinanceGateway
{
    public function postIncome(RentalContract $contract, float $amount, array $meta = []): ?int;
    
    public function postRefund(RentalContract $contract, float $amount, array $meta = []): ?int;
}
