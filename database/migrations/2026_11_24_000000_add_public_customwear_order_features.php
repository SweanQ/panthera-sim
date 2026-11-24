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
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->text('notes')->nullable();
        });

        Schema::create('custom_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('whatsapp_number', 20);
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->nullable();
            $table->text('details');
            $table->enum('status', ['baru', 'dihubungi', 'selesai'])->default('baru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (DB::table('orders')->whereNull('user_id')->exists()) {
            throw new \RuntimeException('Public orders exist; migrate their ownership before rolling back this migration.');
        }

        Schema::dropIfExists('custom_inquiries');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('notes');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
