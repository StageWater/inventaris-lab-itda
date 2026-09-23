<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Rapikan konsep peran:
// 1. Kolom role (enum admin/laboran/kepala_lab) tak pernah dipakai di kode --
//    peran murni ditentukan users.ruangan_id (null = Super Admin).
// 2. FK ruangan_id sebelumnya nullOnDelete: menghapus ruangan diam-diam
//    menaikkan adminnya jadi Super Admin. Diganti restrict agar aman.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropForeign(['ruangan_id']);
            $table->foreign('ruangan_id')->references('id')->on('ruangans')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'laboran', 'kepala_lab'])->default('laboran');
            $table->dropForeign(['ruangan_id']);
            $table->foreign('ruangan_id')->references('id')->on('ruangans')->nullOnDelete();
        });
    }
};