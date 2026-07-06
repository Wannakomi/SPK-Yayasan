<?php

namespace App\Http\Controllers;

use App\Models\CalonPenerima;
use App\Models\Penilaian;
use App\Models\Kriteria;
use App\Models\Setting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        // Mode default = jumlah kriteria penuh
        $modeAktif = (int) $request->get('mode', $kriteria->count());
        $modeAktif = max(1, min($modeAktif, max(1, $kriteria->count())));

        $totalCalon = CalonPenerima::where('periode', $periode)->count();

        // Hitung hanya untuk mode aktif
        $dinilai    = Penilaian::where('periode', $periode)->where('mode_hitung', $modeAktif)->count();
        $layak      = Penilaian::where('periode', $periode)->where('mode_hitung', $modeAktif)->where('status_kelayakan', 'layak')->count();
        $tidakLayak = Penilaian::where('periode', $periode)->where('mode_hitung', $modeAktif)->where('status_kelayakan', 'tidak_layak')->count();

        $topCalon = Penilaian::with('calonPenerima')
            ->where('periode', $periode)
            ->where('mode_hitung', $modeAktif)
            ->where('status_kelayakan', 'layak')
            ->orderBy('ranking')
            ->take(5)
            ->get();

        $semuaCalon = CalonPenerima::with(['penilaian' => fn($q) =>
                $q->where('periode', $periode)->where('mode_hitung', $modeAktif)
            ])
            ->where('periode', $periode)
            ->orderByRaw("CAST(SUBSTRING(kode_anak, 2) AS UNSIGNED), kode_anak")
            ->paginate(5)
            ->withQueryString();

        // Chart
        $chartLimit = 10;
        $chartTotal = Penilaian::where('periode', $periode)->where('mode_hitung', $modeAktif)->count();
        $chartPages = max(1, (int) ceil($chartTotal / $chartLimit));
        $chartPage  = max(1, min((int) $request->get('page_chart', 1), $chartPages));

        $skorChart = Penilaian::with('calonPenerima')
            ->where('periode', $periode)
            ->where('mode_hitung', $modeAktif)
            ->orderByDesc('skor_akhir')
            ->offset(($chartPage - 1) * $chartLimit)
            ->limit($chartLimit)
            ->get();

        // Mode options
        $modeOptions = $kriteria->count() > 0 ? range(1, $kriteria->count()) : [1];

        return view('dashboard.index', compact(
            'periode', 'totalCalon', 'dinilai', 'layak', 'tidakLayak',
            'topCalon', 'semuaCalon', 'skorChart', 'kriteria',
            'chartPage', 'chartPages', 'chartTotal',
            'modeAktif', 'modeOptions'
        ));
    }
}
