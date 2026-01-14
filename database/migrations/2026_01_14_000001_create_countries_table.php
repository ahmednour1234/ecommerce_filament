<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            // ✅ INT unsigned (مش BIGINT) لتفادي مشاكل FK لو عندك country_id unsignedInteger
            $table->increments('id');

            // ISO
            $table->char('iso2', 2)->unique();   // EG
            $table->char('iso3', 3)->nullable()->index(); // EGY

            // Name (JSON) لتشتغل Arabic/English بسهولة
            $table->json('name'); // {"en":"Egypt","ar":"مصر"}

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
        });

        // ✅ تأكد InnoDB (لو السيرفر بيعمل MyISAM)
        // (غالباً Laravel/MySQL InnoDB افتراضي لكن ده احتياط)
        // DB::statement("ALTER TABLE countries ENGINE=InnoDB");
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
