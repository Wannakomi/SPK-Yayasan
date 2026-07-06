<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_kriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaian')->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->decimal('nilai_raw', 10, 4)->nullable();
            $table->decimal('nilai_normalisasi', 10, 6)->nullable();
            $table->decimal('nilai_terbobot', 10, 6)->nullable();
            $table->timestamps();

            $table->unique(['penilaian_id', 'kriteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_kriteria');
    }
};
