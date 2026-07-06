<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Setting;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $periode   = Setting::get('periode_aktif', '2025/2026');
        $threshold = (float) Setting::get('threshold_layak', '0.75');

        // semua kriteria
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        // mode aktif
        $modeAktif = (int) $request->get('mode', $kriteria->count());

        // opsi mode
        $modeOptions = range(1, $kriteria->count());

        $query = Penilaian::with('calonPenerima')
            ->where('periode', $periode)
            ->where('mode_hitung', $modeAktif)
            ->orderBy('ranking');

        // filter status
        if ($request->filled('status')) {
            $query->where('status_kelayakan', $request->status);
        }

        $ranking = $query->get();

        $layak = $ranking
            ->where('status_kelayakan', 'layak')
            ->count();

        $tidakLayak = $ranking
            ->where('status_kelayakan', 'tidak_layak')
            ->count();

        $tertinggi = $ranking->first();

        return view('ranking.index', compact(
            'ranking',
            'layak',
            'tidakLayak',
            'tertinggi',
            'periode',
            'threshold',
            'kriteria',
            'modeAktif',
            'modeOptions'
        ));
    }

    public function export(Request $request)
    {
        $periode = Setting::get('periode_aktif', '2025/2026');

        $modeAktif = (int) $request->get('mode', 3);

        $ranking = Penilaian::with('calonPenerima')
            ->where('periode', $periode)
            ->where('mode_hitung', $modeAktif)
            ->orderBy('ranking')
            ->get();

        return response()->streamDownload(function () use ($ranking) {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Rank',
                'Kode',
                'Nama',
                'Skor Vi',
                'Status'
            ]);

            foreach ($ranking as $r) {

                fputcsv($file, [
                    $r->ranking,
                    $r->calonPenerima->kode_anak,
                    $r->calonPenerima->nama,
                    number_format($r->skor_akhir, 4),
                    $r->status_kelayakan === 'layak'
                        ? 'Layak'
                        : 'Tidak Layak',
                ]);
            }

            fclose($file);

        }, 'ranking-saw-' . now()->format('Y-m-d') . '.csv');
    }
}