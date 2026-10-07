<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Hapus constraint lama
        $table->dropForeign(['gedung_id']);
        // Buat constraint baru dengan restrictOnDelete
        $table->foreign('gedung_id')
              ->references('id')->on('gedungs')
              ->restrictOnDelete();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['gedung_id']);
        $table->foreign('gedung_id')
              ->references('id')->on('gedungs')
              ->nullOnDelete(); // rollback ke pengaturan awal jika di-reverse
    });
}
};
