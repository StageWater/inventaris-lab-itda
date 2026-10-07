<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gedungs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gedung');
            $table->string('lokasi')->nullable();
            $table->timestamps();
        });

        Schema::table('ruangans', function (Blueprint $table) {
            // ponytail: nullable + nullOnDelete agar 31 ruangan lama tetap valid;
            // ganti ke restrictOnDelete kalau hapus gedung harus ditolak.
            $table->foreignId('gedung_id')->nullable()->after('id')->constrained('gedungs')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('gedung_id')->nullable()->after('ruangan_id')->constrained('gedungs')->nullOnDelete();
            $table->enum('role', ['Super Admin', 'Admin Gedung', 'Admin Ruangan'])->default('Admin Ruangan')->after('gedung_id');
        });

        // Backfill: null ruangan+gedung = Super Admin, sisanya Admin Ruangan.
        // Admin Gedung diisi manual via UI (user punya gedung_id tanpa ruangan_id).
        \App\Models\User::whereNull('ruangan_id')->whereNull('gedung_id')->update(['role' => 'Super Admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['gedung_id']);
            $table->dropColumn(['gedung_id', 'role']);
        });

        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropForeign(['gedung_id']);
            $table->dropColumn('gedung_id');
        });

        Schema::dropIfExists('gedungs');
    }
};
