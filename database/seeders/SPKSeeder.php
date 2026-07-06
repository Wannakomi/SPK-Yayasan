<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Kriteria;
use App\Models\CalonPenerima;
use App\Models\Setting;
use App\Models\Penilaian;

class SPKSeeder extends Seeder
{
    public function run(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        Penilaian::query()->delete();
        CalonPenerima::query()->delete();
        Kriteria::query()->delete();
        Setting::query()->delete();
        User::query()->delete();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        User::insert([
            ['name'=>'Super Admin', 'email'=>'admin@sayat.id',    'password'=>Hash::make('sayat@2025!'), 'role'=>'superadmin', 'is_active'=>true,  'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Ketua',       'email'=>'ketua@sayat.id',    'password'=>Hash::make('Ketua@2025!'), 'role'=>'ketua',      'is_active'=>true,  'created_at'=>now(),'updated_at'=>now()],
        ]);

        Kriteria::insert([
            ['kode_kriteria'=>'C1','nama_kriteria'=>'Penghasilan Orang Tua',  'atribut'=>'cost',   'bobot'=>0.45,'satuan'=>'Rp (juta/bln)', 'keterangan'=>'Semakin kecil penghasilan, calon lebih diprioritaskan.','created_at'=>now(),'updated_at'=>now()],
            ['kode_kriteria'=>'C2','nama_kriteria'=>'Nilai Rata-rata Raport', 'atribut'=>'benefit','bobot'=>0.30,'satuan'=>'Angka (0–100)', 'keterangan'=>'Semakin tinggi nilai, semakin baik.',                   'created_at'=>now(),'updated_at'=>now()],
            ['kode_kriteria'=>'C3','nama_kriteria'=>'Kehadiran',              'atribut'=>'benefit','bobot'=>0.25,'satuan'=>'Hari (0–11)',   'keterangan'=>'Semakin tinggi kehadiran, semakin baik.',               'created_at'=>now(),'updated_at'=>now()],
        ]);

        $calon = [
            ['A1', 'Dimas Yusuf Prasojo',      72.00, 9,  3.2, 'SD',  'Kelas 4'],
            ['A2', 'Ragil Gian Ramadhan',      90.70, 10, 3.7, 'SD',  'Kelas 6'],
            ['A3', 'Muhammad Wildan',          81.55, 9,  1.4, 'SD',  'Kelas 5'],
            ['A4', 'Fikri Ananda Firmansyah',  77.89, 9,  1.8, 'SMP', 'Kelas 7'],
            ['A5', 'Muhammad Abdul Rasyid',    86.20, 6,  1.7, 'SMP', 'Kelas 8'],
            ['A6', 'Muhammad Fahri',           82.56, 7,  1.8, 'SMP', 'Kelas 7'],
            ['A7', 'Nabila Pirna',             90.00, 0,  1.8, 'SD',  'Kelas 6'],
            ['A8', 'Yanna Putra',              82.00, 11, 1.5, 'SMP', 'Kelas 9'],
            ['A9', 'Muhamad Rasya Al Furqon',  81.77, 11, 2.0, 'SMP', 'Kelas 8'],
            ['A10','Muhammad Reza Al Faqih',   85.35, 4,  3.0, 'SD',  'Kelas 6'],
        ];

        foreach ($calon as $c) {

            CalonPenerima::create([

                'kode_anak' => $c[0],
                'nama'      => $c[1],

                'nilai_kriteria' => [
                    'C1' => $c[4],
                    'C2' => $c[2],
                    'C3' => $c[3],
                ],

                'jenjang' => $c[5],
                'kelas'   => $c[6],
                'periode' => '2025/2026',
            ]);
        }

        $settings = [
            ['key'=>'app_name',       'value'=>'Butterflies',              'group'=>'general'],
            ['key'=>'yayasan_name',   'value'=>'Yayasan Sahabat Yatim RMJ',   'group'=>'general'],
            ['key'=>'yayasan_address','value'=>'Jl. Rw. Mekar Jaya, Rw. Mekar Jaya, Kec. Serpong, Kota Tangerang Selatan, Banten 15310','group'=>'general'],
            ['key'=>'yayasan_phone',  'value'=>'+628998226669',           'group'=>'general'],
            ['key'=>'yayasan_email',  'value'=>'admin@sahabatyatim.org',  'group'=>'general'],
            ['key'=>'periode_aktif',  'value'=>'2025/2026',               'group'=>'spk'],
            ['key'=>'threshold_layak','value'=>'0.70',                    'group'=>'spk'],
            ['key'=>'kuota_penerima', 'value'=>'4',                       'group'=>'spk'],
            ['key'=>'timezone',       'value'=>'Asia/Jakarta',            'group'=>'general'],
        ];

        foreach ($settings as $s) Setting::create($s);

        $this->command->info('✅ Seeder selesai! 10 calon, 3 kriteria, 5 users.');
    }
}