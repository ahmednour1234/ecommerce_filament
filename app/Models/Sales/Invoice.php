<?php

namespace App\Models\Sales;

use App\Models\Accounting\JournalEntry;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\MainCore\Currency;
use App\Traits\HasBranch;
use App\Traits\HasCostCenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasBranch, HasCostCenter;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'order_id',
        'customer_id',
        'branch_id',
        'cost_center_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'currency_id',
        'due_date',
        'paid_at',
        'journal_entry_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the cost center
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Get the currency
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the journal entry (if auto-generated)
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get all invoice items
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return !is_null($this->paid_at);
    }

    /**
     * Get installments for this invoice
     */
    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class, 'installmentable_id')
            ->where('installmentable_type', self::class);
    }
}

