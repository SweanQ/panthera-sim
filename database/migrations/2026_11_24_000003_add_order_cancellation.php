<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('production_status', [
                'konsultasi',
                'desain_disetujui',
                'proses_sablon',
                'selesai',
                'diambil',
                'dibatalkan',
            ])->default('konsultasi')->change();
            $table->timestamp('production_started_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cancellation_reason', 500)->nullable();
        });
    }

    public function down(): void
    {
        if (DB::table('orders')->where('production_status', 'dibatalkan')->exists()) {
            throw new \RuntimeException('Cancelled orders exist; migrate them before rolling back this migration.');
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['production_started_at', 'cancelled_at', 'cancelled_by', 'cancellation_reason']);
            $table->enum('production_status', [
                'konsultasi',
                'desain_disetujui',
                'proses_sablon',
                'selesai',
                'diambil',
            ])->default('konsultasi')->change();
        });
    }
};
