<?php

namespace App\Models\Housing;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccommodationEntryTransfer extends Model
{
    protected $table = 'accommodation_entry_transfers';

    protected $fillable = [
        'accommodation_entry_id',
        'transfer_client_id',
        'contract_file_path',
        'contract_file_name',
        'created_by',
    ];

    public function accommodationEntry(): BelongsTo
    {
        return $this->belongsTo(AccommodationEntry::class);
    }

    public function transferClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'transfer_client_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
