<?php

namespace Modules\CompanyVisas\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyVisaContractDocument extends Model
{
    protected $table = 'company_visa_contract_documents';

    protected $fillable = [
        'contract_id',
        'title',
        'file_path',
        'mime',
        'size',
        'created_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->created_by) && auth()->check()) {
                $document->created_by = auth()->id();
            }
        });
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(CompanyVisaContract::class, 'contract_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
