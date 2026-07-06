<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_kriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_penerima_id')->constrained('calon_penerima')->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->decimal('nilai', 10, 4)->nullable();
            $table->timestamps();

            $table->unique(['calon_penerima_id', 'kriteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_kriteria');
    }
};
