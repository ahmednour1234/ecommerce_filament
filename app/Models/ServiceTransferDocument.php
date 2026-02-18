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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if ($document->file_path && !$document->file_name) {
                $document->file_name = basename($document->file_path);
                $document->file_type = pathinfo($document->file_name, PATHINFO_EXTENSION);
            }
        });

        static::updating(function ($document) {
            if ($document->isDirty('file_path') && !$document->file_name) {
                $document->file_name = basename($document->file_path);
                $document->file_type = pathinfo($document->file_name, PATHINFO_EXTENSION);
            }
        });
    }

    public function serviceTransfer(): BelongsTo
    {
        return $this->belongsTo(ServiceTransfer::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
