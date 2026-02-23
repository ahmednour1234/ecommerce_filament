<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Housing\HousingStatus;

return new class extends Migration {
    public function up(): void
    {
        $statuses = [
            [
                'key' => 'outside_warranty',
                'name_ar' => 'خارج الضمان',
                'name_en' => 'Outside Warranty',
                'color' => 'warning',
                'order' => 11,
                'is_active' => true,
            ],
            [
                'key' => 'inside_warranty',
                'name_ar' => 'داخل الضمان',
                'name_en' => 'Inside Warranty',
                'color' => 'success',
                'order' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            HousingStatus::updateOrCreate(
                ['key' => $status['key']],
                $status
            );
        }
    }

    public function down(): void
    {
        HousingStatus::whereIn('key', ['outside_warranty', 'inside_warranty'])->delete();
    }
};
