<?php

namespace App\Models\Housing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousingCar extends Model
{
    use SoftDeletes;

    protected $table = 'housing_cars';

    protected $fillable = [
        'type',
        'car_type',
        'car_model',
        'plate_number',
        'serial_number',
        'driver_id',
        'insurance_expiry_date',
        'inspection_expiry_date',
        'form_expiry_date',
        'car_form_file',
        'driver_notes',
    ];

    protected $casts = [
        'insurance_expiry_date' => 'date',
        'inspection_expiry_date' => 'date',
        'form_expiry_date' => 'date',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(HousingDriver::class);
    }

    public function scopeRecruitment($query)
    {
        return $query->where('type', 'recruitment');
    }

    public function scopeRental($query)
    {
        return $query->where('type', 'rental');
    }
}
