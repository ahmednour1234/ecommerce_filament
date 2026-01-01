<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class VoucherSignature extends Model
{
    protected $fillable = [
        'name',
        'title',
        'type',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get vouchers that use this signature
     */
    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class, 'voucher_signature_usage')
            ->withPivot('position', 'created_by', 'created_at')
            ->orderByPivot('position');
    }

    /**
     * Get signature usage records
     */
    public function usages(): HasMany
    {
        return $this->hasMany(VoucherSignatureUsage::class);
    }

    /**
     * Scope to get only active signatures
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by type
     */
    public function scopeForType($query, ?string $type)
    {
        if ($type === null) {
            return $query;
        }

        return $query->where(function ($q) use ($type) {
            $q->where('type', $type)
              ->orWhere('type', 'both')
              ->orWhereNull('type');
        });
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get full URL for signature image
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * Get display name with title
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->title) {
            return $this->name . ' (' . $this->title . ')';
        }
        return $this->name;
    }
}

