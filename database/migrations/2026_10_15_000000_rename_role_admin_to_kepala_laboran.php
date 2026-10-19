<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // 1. Longgarkan enum agar nilai lama + baru sama-sama valid selama pindah data.
            DB::statement("ALTER TABLE users MODIFY role ENUM('Super Admin','Admin Gedung','Admin Ruangan','Kepala Unit','Laboran') DEFAULT 'Admin Ruangan'");
        } elseif (DB::getDriverName() === 'sqlite') {
            // ponytail: sqlite tak bisa ubah CHECK via ALTER; rebuild tanpa check (MySQL tetap enum).
            $this->rebuildSqliteUsers(null);
        }
        // 2. Pindahkan data + snapshot log.
        DB::table('users')->where('role', 'Admin Gedung')->update(['role' => 'Kepala Unit']);
        DB::table('users')->where('role', 'Admin Ruangan')->update(['role' => 'Laboran']);
        if (Schema::hasColumn('log_aktivitases', 'role_user')) {
            DB::table('log_aktivitases')->where('role_user', 'Admin Gedung')->update(['role_user' => 'Kepala Unit']);
            DB::table('log_aktivitases')->where('role_user', 'Admin Ruangan')->update(['role_user' => 'Laboran']);
        }
        // 3. Kunci enum ke nama baru.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('Super Admin','Kepala Unit','Laboran') DEFAULT 'Laboran'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('Super Admin','Admin Gedung','Admin Ruangan','Kepala Unit','Laboran') DEFAULT 'Laboran'");
        }
        DB::table('users')->where('role', 'Kepala Unit')->update(['role' => 'Admin Gedung']);
        DB::table('users')->where('role', 'Laboran')->update(['role' => 'Admin Ruangan']);
        if (Schema::hasColumn('log_aktivitases', 'role_user')) {
            DB::table('log_aktivitases')->where('role_user', 'Kepala Unit')->update(['role_user' => 'Admin Gedung']);
            DB::table('log_aktivitases')->where('role_user', 'Laboran')->update(['role_user' => 'Admin Ruangan']);
        }
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('Super Admin','Admin Gedung','Admin Ruangan') DEFAULT 'Admin Ruangan'");
        } elseif (DB::getDriverName() === 'sqlite') {
            $this->rebuildSqliteUsers(['Super Admin', 'Admin Gedung', 'Admin Ruangan']);
        }
    }

    /**
     * Salin tabel users ke tabel baru (dengan/tanpa CHECK role), lalu ganti yang lama.
     * Index unik ikut disalin ulang karena DROP TABLE ikut menghapusnya.
     */
    private function rebuildSqliteUsers(?array $allowedRoles): void
    {
        $check = $allowedRoles === null
            ? ''
            : ' check ("role" in (\'' . implode('\', \'', $allowedRoles) . '\'))';

        $indexes = DB::select("SELECT sql FROM sqlite_master WHERE type = 'index' AND tbl_name = 'users' AND sql IS NOT NULL");

        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement('CREATE TABLE "users_rebuild" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "ruangan_id" integer, "gedung_id" integer, "role" varchar' . $check . ' not null default \'Admin Ruangan\', foreign key("ruangan_id") references ruangans("id") on delete restrict on update no action, foreign key("gedung_id") references "gedungs"("id") on delete set null)');
        DB::statement('INSERT INTO "users_rebuild" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "ruangan_id", "gedung_id", "role") SELECT "id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "ruangan_id", "gedung_id", "role" FROM "users"');
        DB::statement('DROP TABLE "users"');
        DB::statement('ALTER TABLE "users_rebuild" RENAME TO "users"');
        foreach ($indexes as $index) {
            DB::statement($index->sql);
        }
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
