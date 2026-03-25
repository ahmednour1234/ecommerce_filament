<?php

namespace App\Models\Housing;

use App\Models\MainCore\Branch;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccommodationEntry extends Model
{
    use SoftDeletes;

    protected $table = 'accommodation_entries';

    protected $fillable = [
        'laborer_id',
        'type',
        'contract_no',
        'entry_type',
        'entry_date',
        'building_id',
        'status_id',
        'exit_date',
        'notes',
        'branch_id',
        'created_by',
        'updated_by',
        'new_sponsor_name',
        'old_sponsor_name',
        'new_sponsor_phone',
        'old_sponsor_phone',
        'customer_name',
        'customer_phone',
        'customer_id_number',
        'customer_id',
        'status_key',
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'exit_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            if (empty($entry->created_by) && auth()->check()) {
                $entry->created_by = auth()->id();
            }

            // Decrease building available capacity
            if ($entry->building_id) {
                $building = Building::find($entry->building_id);
                if ($building && $building->available_capacity > 0) {
                    $building->decrement('available_capacity');
                }
            }
        });

        static::created(function ($entry) {
            // Log initial status if status_id or status_key is set
            if ($entry->status_id || $entry->status_key) {
                AccommodationEntryStatusLog::create([
                    'accommodation_entry_id' => $entry->id,
                    'old_status_id' => null,
                    'new_status_id' => $entry->status_id ?: null,
                    'status_key' => $entry->status_key ?: null,
                    'status_date' => $entry->entry_date ? $entry->entry_date->toDateString() : now()->toDateString(),
                    'created_by' => $entry->created_by,
                ]);
            }
        });

        static::updating(function ($entry) {
            if (empty($entry->updated_by) && auth()->check()) {
                $entry->updated_by = auth()->id();
            }

            // Log status change
            if ($entry->isDirty('status_id') || $entry->isDirty('status_key')) {
                AccommodationEntryStatusLog::create([
                    'accommodation_entry_id' => $entry->id,
                    'old_status_id' => $entry->isDirty('status_id') ? $entry->getOriginal('status_id') : null,
                    'new_status_id' => $entry->isDirty('status_id') ? $entry->status_id : null,
                    'status_key' => $entry->isDirty('status_key') ? $entry->status_key : null,
                    'status_date' => now()->toDateString(),
                    'created_by' => auth()->id(),
                ]);
            }

            // Handle building capacity changes
            if ($entry->isDirty('building_id') || $entry->isDirty('exit_date')) {
                // If building changed, restore old building capacity and decrease new one
                if ($entry->isDirty('building_id')) {
                    $oldBuildingId = $entry->getOriginal('building_id');
                    if ($oldBuildingId) {
                        Building::where('id', $oldBuildingId)->increment('available_capacity');
                    }

                    if ($entry->building_id) {
                        $building = Building::find($entry->building_id);
                        if ($building && $building->available_capacity > 0) {
                            $building->decrement('available_capacity');
                        }
                    }
                }

                // If exit date is set, restore building capacity
                if ($entry->exit_date && !$entry->getOriginal('exit_date')) {
                    if ($entry->building_id) {
                        Building::where('id', $entry->building_id)->increment('available_capacity');
                    }
                }
            }
        });

        static::deleting(function ($entry) {
            // Restore building capacity on delete
            if ($entry->building_id && !$entry->exit_date) {
                Building::where('id', $entry->building_id)->increment('available_capacity');
            }
        });
    }

    public function laborer(): BelongsTo
    {
        return $this->belongsTo(Laborer::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(HousingStatus::class, 'status_id');
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('exit_date');
    }

    public function scopeNewArrival($query)
    {
        return $query->where('entry_type', 'new_arrival');
    }

    public function scopeReturn($query)
    {
        return $query->where('entry_type', 'return');
    }

    public function scopeTransfer($query)
    {
        return $query->where('entry_type', 'transfer');
    }

    public function scopeRecruitment($query)
    {
        return $query->where('type', 'recruitment');
    }

    public function scopeRental($query)
    {
        return $query->where('type', 'rental');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(AccommodationEntryStatusLog::class);
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Client::class, 'customer_id');
    }

    public function transferData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AccommodationEntryTransfer::class);
    }
}
