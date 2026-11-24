<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pegawai', 'customer'])->default('pegawai')->change();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::table('users')->where('role', 'customer')->exists()) {
            throw new \RuntimeException('Customer accounts exist; migrate them before rolling back this migration.');
        }

        if (DB::table('customers')->whereNotNull('user_id')->exists()) {
            throw new \RuntimeException('Customer order history is linked to accounts; migrate it before rolling back this migration.');
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pegawai'])->default('pegawai')->change();
        });
    }
};
