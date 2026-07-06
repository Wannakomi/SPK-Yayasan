<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_penerima_id')->constrained('calon_penerima')->onDelete('cascade');
            $table->string('periode', 10)->default('2025/2026');

            // Nilai normalisasi R
            $table->decimal('r_c1', 8, 6)->nullable(); // normalisasi C1
            $table->decimal('r_c2', 8, 6)->nullable(); // normalisasi C2
            $table->decimal('r_c3', 8, 6)->nullable(); // normalisasi C3

            // Nilai terbobot V
            $table->decimal('v_c1', 8, 6)->nullable(); // bobot * R(C1)
            $table->decimal('v_c2', 8, 6)->nullable(); // bobot * R(C2)
            $table->decimal('v_c3', 8, 6)->nullable(); // bobot * R(C3)

            $table->decimal('skor_akhir', 8, 6)->nullable(); // Vi = sum(Vij)
            $table->integer('ranking')->nullable();
            $table->enum('status_kelayakan', ['layak', 'tidak_layak'])->nullable();
            $table->timestamps();

            $table->unique(
                ['calon_penerima_id', 'periode', 'mode_hitung'],
                'penilaian_unique_mode'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
