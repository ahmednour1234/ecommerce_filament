<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
      protected $guarded = [];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function preferences()
{
    return $this->hasOne(\App\Models\MainCore\UserPreference::class);
}

    /**
     * Get all branches assigned to this user
     */
    public function branches()
    {
        return $this->belongsToMany(\App\Models\MainCore\Branch::class, 'branch_user');
    }

    // دخول لوحة Filament
    public function canAccessPanel(Panel $panel): bool
    {
        // اسمح لأي يوزر عنده role اسمه 'super_admin'
        return $this->hasRole('super_admin');
    }

    public function getFilamentName(): string
    {
        return $this->name ?? $this->email;
    }
}
