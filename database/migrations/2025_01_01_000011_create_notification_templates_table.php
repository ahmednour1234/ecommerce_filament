<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key'); // order.created
            $table->foreignId('channel_id')->constrained('notification_channels')->cascadeOnDelete();
            $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->string('subject')->nullable();
            $table->text('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->json('variables')->nullable(); // ["{user_name}", "{order_number}"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['key','channel_id','language_id'], 'notification_template_unique');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
