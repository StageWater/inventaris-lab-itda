<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FK: pastikan users.ruangan_id selalu menunjuk ke ruangan yang benar-benar ada.
        // nullOnDelete = jika ruangan dihapus, user otomatis "lepas" (menjadi Super Admin).
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('ruangan_id')
                ->references('id')
                ->on('ruangans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id']);
        });
    }
};
