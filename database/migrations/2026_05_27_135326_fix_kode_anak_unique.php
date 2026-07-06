<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('calon_penerima', function (Blueprint $table) {
            // Drop global unique, ganti ke composite (kode_anak + periode)
            $table->dropUnique(['kode_anak']);
            $table->unique(['kode_anak', 'periode'], 'calon_kode_periode_unique');
        });
    }

    public function down(): void
    {
        Schema::table('calon_penerima', function (Blueprint $table) {
            $table->dropUnique('calon_kode_periode_unique');
            $table->unique('kode_anak');
        });
    }
};