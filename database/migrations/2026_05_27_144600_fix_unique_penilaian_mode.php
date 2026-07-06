<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // cek dulu biar gak duplicate
        $indexes = DB::select("SHOW INDEX FROM penilaian WHERE Key_name = 'penilaian_unique_mode'");

        if (count($indexes) === 0) {

            Schema::table('penilaian', function (Blueprint $table) {

                $table->unique(
                    [
                        'calon_penerima_id',
                        'periode',
                        'mode_hitung'
                    ],
                    'penilaian_unique_mode'
                );

            });
        }
    }

    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {

            $table->dropUnique('penilaian_unique_mode');

        });
    }
};