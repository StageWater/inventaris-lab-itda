<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Longgarkan enum agar nilai lama + baru sama-sama valid selama pindah data.
        DB::statement("ALTER TABLE users MODIFY role ENUM('Super Admin','Admin Gedung','Admin Ruangan','Kepala Unit','Laboran') DEFAULT 'Admin Ruangan'");
        // 2. Pindahkan data + snapshot log.
        DB::table('users')->where('role', 'Admin Gedung')->update(['role' => 'Kepala Unit']);
        DB::table('users')->where('role', 'Admin Ruangan')->update(['role' => 'Laboran']);
        if (Schema::hasColumn('log_aktivitases', 'role_user')) {
            DB::table('log_aktivitases')->where('role_user', 'Admin Gedung')->update(['role_user' => 'Kepala Unit']);
            DB::table('log_aktivitases')->where('role_user', 'Admin Ruangan')->update(['role_user' => 'Laboran']);
        }
        // 3. Kunci enum ke nama baru.
        DB::statement("ALTER TABLE users MODIFY role ENUM('Super Admin','Kepala Unit','Laboran') DEFAULT 'Laboran'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('Super Admin','Admin Gedung','Admin Ruangan','Kepala Unit','Laboran') DEFAULT 'Laboran'");
        DB::table('users')->where('role', 'Kepala Unit')->update(['role' => 'Admin Gedung']);
        DB::table('users')->where('role', 'Laboran')->update(['role' => 'Admin Ruangan']);
        if (Schema::hasColumn('log_aktivitases', 'role_user')) {
            DB::table('log_aktivitases')->where('role_user', 'Kepala Unit')->update(['role_user' => 'Admin Gedung']);
            DB::table('log_aktivitases')->where('role_user', 'Laboran')->update(['role_user' => 'Admin Ruangan']);
        }
        DB::statement("ALTER TABLE users MODIFY role ENUM('Super Admin','Admin Gedung','Admin Ruangan') DEFAULT 'Admin Ruangan'");
    }
};
