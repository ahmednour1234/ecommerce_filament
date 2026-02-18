<?php

namespace Modules\CompanyVisas\Entities;

use App\Models\Accounting\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyVisaContractCost extends Model
{
    protected $table = 'company_visa_contract_costs';

    protected $fillable = [
        'contract_id',
        'cost_per_worker',
        'total_cost',
        'due_date',
        'description',
        'finance_entry_id',
        'created_by',
    ];

    protected $casts = [
        'cost_per_worker' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'due_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cost) {
            if (empty($cost->created_by) && auth()->check()) {
                $cost->created_by = auth()->id();
            }
        });
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(CompanyVisaContract::class, 'contract_id');
    }

    public function financeEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'finance_entry_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
