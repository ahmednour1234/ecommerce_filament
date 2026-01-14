<?php

namespace App\Models\Finance;

use App\Models\User;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'document_no',
        'branch_id',
        'country_id',
        'type',
        'amount',
        'currency_id',
        'amount_base',
        'rate_used',
        'transaction_date',
        'receiver_name',
        'payment_method',
        'reference_no',
        'notes',
        'attachment_path',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'approval_note',
        'rejected_by',
        'rejected_at',
        'rejection_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_base' => 'decimal:2',
        'rate_used' => 'decimal:8',
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // relations
    public function branch() { return $this->belongsTo(Branch::class); }
    public function country() { return $this->belongsTo(Country::class); }
    public function currency() { return $this->belongsTo(Currency::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejecter() { return $this->belongsTo(User::class, 'rejected_by'); }

    // scopes (speed)
    public function scopeApproved($q) { return $q->where('status', 'approved'); }
    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopeExpense($q) { return $q->where('type', 'expense'); }
    public function scopeIncome($q) { return $q->where('type', 'income'); }
}
