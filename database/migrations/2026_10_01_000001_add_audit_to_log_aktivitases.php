<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_aktivitases', function (Blueprint $table) {
            // ponytail: snapshot nama/role/IP saat aksi terjadi; relasi user tetap
            // sumber kebenaran akun, kolom ini hanya untuk audit yang tak berubah
            // walau user dihapus/diubah (user_id nullOnDelete).
            $table->string('nama_user')->nullable()->after('user_id');
            $table->string('role_user')->nullable()->after('nama_user');
            $table->string('ip_address', 45)->nullable()->after('deskripsi');
        });

        // Backfill agar riwayat lama tetap tampil nama + role.
        foreach (\App\Models\LogAktivitas::with('user')->get() as $log) {
            $log->updateQuietly([
                'nama_user' => $log->user?->name,
                'role_user' => $log->user?->role,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('log_aktivitases', function (Blueprint $table) {
            $table->dropColumn(['nama_user', 'role_user', 'ip_address']);
        });
    }
};
