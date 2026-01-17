<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laborers', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('passport_number')->unique();
            $table->string('passport_issue_place')->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->unsignedBigInteger('nationality_id');
            $table->unsignedBigInteger('profession_id');
            $table->string('experience_level')->nullable();
            $table->string('social_status')->nullable();
            $table->text('address')->nullable();
            $table->string('relative_name')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->unsignedBigInteger('agent_id');
            $table->unsignedInteger('country_id');
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->boolean('speaks_arabic')->default(false);
            $table->boolean('speaks_english')->default(false);
            $table->string('personal_image')->nullable();
            $table->string('cv_file')->nullable();
            $table->string('intro_video')->nullable();
            $table->decimal('monthly_salary_amount', 12, 2);
            $table->unsignedBigInteger('monthly_salary_currency_id');
            $table->longText('notes')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('show_on_website')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nationality_id')->references('id')->on('nationalities')->onDelete('restrict');
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('restrict');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('restrict');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('restrict');
            $table->foreign('monthly_salary_currency_id')->references('id')->on('currencies')->onDelete('restrict');

            $table->index('nationality_id');
            $table->index('profession_id');
            $table->index('agent_id');
            $table->index('country_id');
            $table->index('monthly_salary_currency_id');
            $table->index('passport_number');
            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laborers');
    }
};
