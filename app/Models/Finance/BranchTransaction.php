<?php

namespace App\Models\Finance;

use App\Models\User;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchTransaction extends Model
{
    protected $table = 'finance_branch_transactions';

    protected $fillable = [
        'trx_date',
        'branch_id',
        'country_id',
        'currency_id',
        'finance_type_id',
        'amount',
        'payment_method',
        'recipient_name',
        'reference_no',
        'notes',
        'attachment_path',
        'created_by',
    ];

    protected $casts = [
        'trx_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function financeType(): BelongsTo
    {
        return $this->belongsTo(FinanceType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForCurrency($query, $currencyId)
    {
        return $query->where('currency_id', $currencyId);
    }

    public function scopeForDateRange($query, $from, $to)
    {
        return $query->whereBetween('trx_date', [$from, $to]);
    }

    public function scopeIncome($query)
    {
        return $query->whereHas('financeType', function ($q) {
            $q->where('kind', 'income');
        });
    }

    public function scopeExpense($query)
    {
        return $query->whereHas('financeType', function ($q) {
            $q->where('kind', 'expense');
        });
    }
}
