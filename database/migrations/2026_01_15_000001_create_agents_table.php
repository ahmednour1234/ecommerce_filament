<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('email')->nullable();
            $table->unsignedBigInteger('country_id');
            $table->string('city_ar')->nullable();
            $table->string('city_en')->nullable();
            $table->text('address_ar')->nullable();
            $table->text('address_en')->nullable();
            $table->string('license_number')->nullable();
            $table->string('phone_1');
            $table->string('phone_2')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->string('passport_issue_place')->nullable();
            $table->string('bank_sender')->nullable();
            $table->string('account_number')->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('password')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('country_id');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
