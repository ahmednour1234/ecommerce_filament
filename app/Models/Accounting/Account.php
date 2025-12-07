<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'level',
        'is_active',
        'allow_manual_entry',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_manual_entry' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * Get the parent account
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get child accounts
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id')->orderBy('code');
    }

    /**
     * Get all descendants recursively
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Check if account is a parent (has children)
     */
    public function isParent(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Check if account is a leaf (no children)
     */
    public function isLeaf(): bool
    {
        return !$this->isParent();
    }

    /**
     * Get full account path (e.g., "1.1.1 - Cash")
     */
    public function getFullPathAttribute(): string
    {
        $path = $this->code;
        $parent = $this->parent;
        
        while ($parent) {
            $path = $parent->code . '.' . $path;
            $parent = $parent->parent;
        }
        
        return $path . ' - ' . $this->name;
    }

    /**
     * Scope to get only active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get accounts by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get root accounts (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get leaf accounts (no children)
     */
    public function scopeLeaf($query)
    {
        return $query->whereDoesntHave('children');
    }
}

