<?php

namespace Modules\CompanyVisas\Entities;

use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyVisaRequest extends Model
{
    protected $table = 'company_visa_requests';

    protected $fillable = [
        'code',
        'request_date',
        'profession_id',
        'nationality_id',
        'gender',
        'workers_count',
        'visa_number',
        'used_count',
        'remaining_count',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'workers_count' => 'integer',
        'used_count' => 'integer',
        'remaining_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->code)) {
                $request->code = \Modules\CompanyVisas\Services\CompanyVisaRequestService::generateCode();
            }
            if (empty($request->remaining_count)) {
                $request->remaining_count = $request->workers_count;
            }
            if (empty($request->created_by) && auth()->check()) {
                $request->created_by = auth()->id();
            }
        });

        static::saving(function ($request) {
            if ($request->isDirty(['workers_count', 'used_count']) || $request->isNew()) {
                $request->remaining_count = $request->workers_count - $request->used_count;
            }
        });
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(CompanyVisaContract::class, 'visa_request_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completed', 'paid' => 'success',
            'rejected' => 'danger',
            'draft' => 'gray',
            default => 'gray',
        };
    }

    public function incrementUsedCount(int $count = 1): void
    {
        $this->increment('used_count', $count);
        $this->refresh();
        $this->remaining_count = $this->workers_count - $this->used_count;
        $this->save();
    }

    public function decrementUsedCount(int $count = 1): void
    {
        $this->decrement('used_count', $count);
        $this->refresh();
        $this->remaining_count = max(0, $this->workers_count - $this->used_count);
        $this->save();
    }
}
