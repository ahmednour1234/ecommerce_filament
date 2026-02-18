<?php

namespace Modules\CompanyVisas\Entities;

use App\Models\Accounting\Account;
use App\Models\Accounting\JournalEntry;
use App\Models\MainCore\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyVisaContractExpense extends Model
{
    protected $table = 'company_visa_contract_expenses';

    protected $fillable = [
        'contract_id',
        'expense_account_id',
        'amount',
        'includes_vat',
        'expense_date',
        'payment_method_id',
        'invoice_no',
        'attachment_path',
        'description',
        'finance_entry_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'includes_vat' => 'boolean',
        'expense_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            if (empty($expense->created_by) && auth()->check()) {
                $expense->created_by = auth()->id();
            }
        });
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(CompanyVisaContract::class, 'contract_id');
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
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
