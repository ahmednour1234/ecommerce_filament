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
    public const TYPE_COORDINATOR = 'coordinator';
    public const TYPE_BRANCH_MANAGER = 'branch_manager';
    public const TYPE_COMPLAINTS_MANAGER = 'complaints_manager';
    public const TYPE_ACCOUNTANT = 'accountant';
    public const TYPE_COMPANY_OWNER = 'company_owner';
    public const TYPE_SUPER_ADMIN = 'super_admin';
    public const TYPE_CUSTOMER_SERVICE = 'customer_service';
    public const TYPE_GENERAL_ACCOUNTANT = 'general_accountant';
    public const TYPE_HR_MANAGER = 'hr_manager';

    public static function typeOptions(): array
    {
        return [
            self::TYPE_COORDINATOR => 'منسق',
            self::TYPE_BRANCH_MANAGER => 'مدير فرع',
            self::TYPE_COMPLAINTS_MANAGER => 'مدير شكاوي',
            self::TYPE_ACCOUNTANT => 'محاسب',
            self::TYPE_COMPANY_OWNER => 'صاحب الشركة',
            self::TYPE_SUPER_ADMIN => 'سوبر ادمن',
            self::TYPE_CUSTOMER_SERVICE => 'خدمة عملاء',
            self::TYPE_GENERAL_ACCOUNTANT => 'محاسب عام',
            self::TYPE_HR_MANAGER => 'مدير موارد بشرية',
        ];
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'branch_id',
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

    /**
     * Get the branch assigned to this user
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\MainCore\Branch::class);
    }

    /**
     * Get the employee associated with this user
     */
    public function employee()
    {
        return $this->hasOne(\App\Models\HR\Employee::class, 'user_id');
    }

    // دخول لوحة Filament
    public function canAccessPanel(Panel $panel): bool
    {
        // اسمح لأي مستخدم في جدول users بتسجيل الدخول
        return true;
    }

    public function getFilamentName(): string
    {
        return $this->name ?? $this->email;
    }
}
