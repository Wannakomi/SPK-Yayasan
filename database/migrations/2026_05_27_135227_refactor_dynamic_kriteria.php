<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        // =========================================================
        // 1. CALON PENERIMA
        // =========================================================

        // CEK apakah kolom belum ada
        if (!Schema::hasColumn('calon_penerima', 'nilai_kriteria')) {

            Schema::table('calon_penerima', function (Blueprint $table) {
                $table->json('nilai_kriteria')
                      ->nullable()
                      ->after('nama');
            });

        }

        // Ambil data lama lalu pindahkan ke JSON
        $calon = DB::table('calon_penerima')->get();

        foreach ($calon as $c) {

            $nilai = [];

            if (property_exists($c, 'penghasilan') && $c->penghasilan !== null) {
                $nilai['C1'] = (float) $c->penghasilan;
            }

            if (property_exists($c, 'nilai_akademik') && $c->nilai_akademik !== null) {
                $nilai['C2'] = (float) $c->nilai_akademik;
            }

            if (property_exists($c, 'kehadiran') && $c->kehadiran !== null) {
                $nilai['C3'] = (int) $c->kehadiran;
            }

            DB::table('calon_penerima')
                ->where('id', $c->id)
                ->update([
                    'nilai_kriteria' => json_encode($nilai)
                ]);
        }

        // DROP KOLOM LAMA JIKA MASIH ADA
        Schema::table('calon_penerima', function (Blueprint $table) {

            $columnsToDrop = [];

            if (Schema::hasColumn('calon_penerima', 'penghasilan')) {
                $columnsToDrop[] = 'penghasilan';
            }

            if (Schema::hasColumn('calon_penerima', 'nilai_akademik')) {
                $columnsToDrop[] = 'nilai_akademik';
            }

            if (Schema::hasColumn('calon_penerima', 'kehadiran')) {
                $columnsToDrop[] = 'kehadiran';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });


        // =========================================================
        // 2. PENILAIAN
        // =========================================================

        DB::table('penilaian')->delete();

        Schema::table('penilaian', function (Blueprint $table) {

            if (!Schema::hasColumn('penilaian', 'hasil_normalisasi')) {
                $table->json('hasil_normalisasi')
                      ->nullable()
                      ->after('periode');
            }

            if (!Schema::hasColumn('penilaian', 'hasil_terbobot')) {
                $table->json('hasil_terbobot')
                      ->nullable()
                      ->after('hasil_normalisasi');
            }

            if (!Schema::hasColumn('penilaian', 'mode_hitung')) {
                $table->unsignedTinyInteger('mode_hitung')
                      ->default(3)
                      ->after('hasil_terbobot');
            }
        });

        // DROP KOLOM LAMA PENILAIAN
        Schema::table('penilaian', function (Blueprint $table) {

            $drop = [];

            foreach ([
                'r_c1','r_c2','r_c3',
                'v_c1','v_c2','v_c3'
            ] as $col) {

                if (Schema::hasColumn('penilaian', $col)) {
                    $drop[] = $col;
                }
            }

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }

    public function down(): void
    {
        // =========================================================
        // KEMBALIKAN calon_penerima
        // =========================================================

        Schema::table('calon_penerima', function (Blueprint $table) {

            if (!Schema::hasColumn('calon_penerima', 'penghasilan')) {
                $table->decimal('penghasilan', 8, 2)
                      ->nullable()
                      ->after('nama');
            }

            if (!Schema::hasColumn('calon_penerima', 'nilai_akademik')) {
                $table->decimal('nilai_akademik', 5, 2)
                      ->nullable()
                      ->after('penghasilan');
            }

            if (!Schema::hasColumn('calon_penerima', 'kehadiran')) {
                $table->integer('kehadiran')
                      ->nullable()
                      ->after('nilai_akademik');
            }
        });

        if (Schema::hasColumn('calon_penerima', 'nilai_kriteria')) {

            Schema::table('calon_penerima', function (Blueprint $table) {
                $table->dropColumn('nilai_kriteria');
            });

        }

        // =========================================================
        // KEMBALIKAN penilaian
        // =========================================================

        Schema::table('penilaian', function (Blueprint $table) {

            foreach ([
                'r_c1','r_c2','r_c3',
                'v_c1','v_c2','v_c3'
            ] as $col) {

                if (!Schema::hasColumn('penilaian', $col)) {
                    $table->decimal($col, 8, 6)->nullable();
                }
            }

            $drop = [];

            foreach ([
                'hasil_normalisasi',
                'hasil_terbobot',
                'mode_hitung'
            ] as $col) {

                if (Schema::hasColumn('penilaian', $col)) {
                    $drop[] = $col;
                }
            }

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};