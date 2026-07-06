<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('calon_penerima', function (Blueprint $table) {

            $table->id();

            $table->string('kode_anak', 10)->unique();

            $table->string('nama');

            // JSON KRITERIA
            $table->json('nilai_kriteria');

            $table->enum('jenjang', ['SD', 'SMP', 'SMA/SMK'])->nullable();

            $table->string('kelas')->nullable();

            $table->text('catatan')->nullable();

            $table->string('periode', 10)->default('2025/2026');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calon_penerima');
    }
};