<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'national_id',
        'mobile',
        'mobile2',
        'email',
        'birth_date',
        'marital_status',
        'classification',
        'building_no',
        'street_name',
        'city_name',
        'district_name',
        'postal_code',
        'additional_no',
        'unit_no',
        'building_no_en',
        'street_name_en',
        'city_name_en',
        'district_name_en',
        'unit_no_en',
        'full_address_ar',
        'full_address_en',
        'housing_type',
        'id_image',
        'other_document',
        'source',
        'office_referral',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'marital_status' => 'string',
        'classification' => 'string',
        'housing_type' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (empty($client->code)) {
                $client->code = static::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        return DB::transaction(function () {
            $prefix = 'CLT';
            $last = static::whereNotNull('code')
                ->where('code', 'like', $prefix . '-%')
                ->latest('id')
                ->first();

            if ($last) {
                $lastNumber = (int) substr($last->code, 4);
                $number = $lastNumber + 1;
            } else {
                $number = 1;
            }

            $code = $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);

            $attempts = 0;
            while (static::where('code', $code)->exists() && $attempts < 10) {
                $number++;
                $code = $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
                $attempts++;
            }

            return $code;
        });
    }
}
