<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. accommodation_entries — sponsor & passport fields ───────────────
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('accommodation_entries', 'new_sponsor_name')) {
                $table->string('new_sponsor_name')->nullable()->after('exit_date');
            }
            if (! Schema::hasColumn('accommodation_entries', 'old_sponsor_name')) {
                $table->string('old_sponsor_name')->nullable();
            }
            if (! Schema::hasColumn('accommodation_entries', 'nationality_id')) {
                $table->unsignedBigInteger('nationality_id')->nullable();
                $table->foreign('nationality_id')->references('id')->on('nationalities')->nullOnDelete();
            }
            if (! Schema::hasColumn('accommodation_entries', 'worker_passport_number')) {
                $table->string('worker_passport_number')->nullable();
            }
            if (! Schema::hasColumn('accommodation_entries', 'new_sponsor_phone')) {
                $table->string('new_sponsor_phone')->nullable();
            }
            if (! Schema::hasColumn('accommodation_entries', 'old_sponsor_phone')) {
                $table->string('old_sponsor_phone')->nullable();
            }
        });

        // ── 2. accommodation_entries — customer text fields ────────────────────
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('accommodation_entries', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (! Schema::hasColumn('accommodation_entries', 'customer_phone')) {
                $table->string('customer_phone')->nullable();
            }
            if (! Schema::hasColumn('accommodation_entries', 'customer_id_number')) {
                $table->string('customer_id_number')->nullable();
            }
        });

        // ── 3. accommodation_entries — customer_id FK ──────────────────────────
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('accommodation_entries', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->foreign('customer_id')->references('id')->on('clients')->nullOnDelete();
            }
        });

        // ── 4. accommodation_entries — status_key ─────────────────────────────
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('accommodation_entries', 'status_key')) {
                $table->string('status_key')->nullable()->after('status_id');
            }
        });

        // ── 5. accommodation_entry_status_logs — status_key & status_date ─────
        if (Schema::hasTable('accommodation_entry_status_logs')) {
            Schema::table('accommodation_entry_status_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('accommodation_entry_status_logs', 'status_key')) {
                    $table->string('status_key')->nullable();
                }
                if (! Schema::hasColumn('accommodation_entry_status_logs', 'status_date')) {
                    $table->date('status_date')->nullable();
                }
            });
        }

        // ── 6. accommodation_entry_transfers table ─────────────────────────────
        if (! Schema::hasTable('accommodation_entry_transfers')) {
            Schema::create('accommodation_entry_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('accommodation_entry_id')
                      ->constrained('accommodation_entries')
                      ->cascadeOnDelete();
                $table->unsignedBigInteger('transfer_client_id')->nullable();
                $table->foreign('transfer_client_id')->references('id')->on('clients')->nullOnDelete();
                $table->string('contract_file_path')->nullable();
                $table->string('contract_file_name')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('accommodation_entry_transfers')) {
            Schema::dropIfExists('accommodation_entry_transfers');
        }

        Schema::table('accommodation_entries', function (Blueprint $table) {
            $cols = [
                'new_sponsor_name', 'old_sponsor_name', 'nationality_id',
                'worker_passport_number', 'new_sponsor_phone', 'old_sponsor_phone',
                'customer_name', 'customer_phone', 'customer_id_number',
                'customer_id', 'status_key',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('accommodation_entries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
