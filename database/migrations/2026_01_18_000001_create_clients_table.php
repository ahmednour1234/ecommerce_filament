<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('clients')) {
            return;
        }

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('national_id')->unique();
            $table->string('mobile');
            $table->string('mobile2')->nullable();
            $table->string('email')->nullable();
            $table->date('birth_date');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed']);
            $table->enum('classification', ['new', 'vip', 'blocked']);
            $table->string('building_no')->nullable();
            $table->string('street_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('district_name')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('additional_no')->nullable();
            $table->string('unit_no')->nullable();
            $table->string('building_no_en')->nullable();
            $table->string('street_name_en')->nullable();
            $table->string('city_name_en')->nullable();
            $table->string('district_name_en')->nullable();
            $table->string('unit_no_en')->nullable();
            $table->text('full_address_ar')->nullable();
            $table->text('full_address_en')->nullable();
            $table->enum('housing_type', ['villa', 'building', 'apartment', 'farm'])->nullable();
            $table->string('id_image')->nullable();
            $table->string('other_document')->nullable();
            $table->string('source')->nullable();
            $table->string('office_referral')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('national_id');
            $table->index('mobile');
            $table->index('city_name');
            $table->index('classification');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
