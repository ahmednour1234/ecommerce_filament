<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTransferDocument extends Model
{
    protected $fillable = [
        'service_transfer_id',
        'file_path',
        'file_name',
        'file_type',
        'uploaded_by',
    ];

    public function serviceTransfer(): BelongsTo
    {
        return $this->belongsTo(ServiceTransfer::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
