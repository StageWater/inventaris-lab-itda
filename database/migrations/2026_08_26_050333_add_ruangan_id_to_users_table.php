<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Karena kolom 'role' sudah ada, kita cukup tambahkan 'ruangan_id' saja
            $table->unsignedBigInteger('ruangan_id')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Saat di-rollback, cukup hapus ruangan_id
            $table->dropColumn('ruangan_id');
        });
    }
};