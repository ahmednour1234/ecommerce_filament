<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->text('client_rating')->nullable()->after('client_text_message');
            $table->string('client_rating_proof_image')->nullable()->after('client_rating');
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropColumn(['client_rating', 'client_rating_proof_image']);
        });
    }
};
