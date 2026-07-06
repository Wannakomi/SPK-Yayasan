<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\CalonPenerima;
use App\Models\Kriteria;
use App\Models\Setting;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $periode   = Setting::get('periode_aktif', '2025/2026');
        $threshold = (float) Setting::get('threshold_layak', '0.75');
        $kriteria  = Kriteria::orderBy('kode_kriteria')->get();

        // Mode yang tersedia = berapa kriteria yang ada
        $modeOptions = $kriteria->count() > 0
            ? range(1, $kriteria->count())
            : [1];

        $modeDefault = (int) $request->get('mode', $kriteria->count());
        $modeDefault = max(1, min($modeDefault, $kriteria->count()));

        // Kriteria yang dipakai di mode ini (N pertama)
        $kriteriaMode = $kriteria->take($modeDefault);

        // Status hitung per mode
        $sudahHitungPerMode = [];
        foreach ($modeOptions as $m) {
            $sudahHitungPerMode[$m] = Penilaian::where('periode', $periode)
                ->where('mode_hitung', $m)->exists();
        }

        // Data penilaian untuk mode terpilih
        $query = Penilaian::with('calonPenerima')
            ->where('periode', $periode)
            ->where('mode_hitung', $modeDefault)
            ->orderBy('ranking');

        if ($request->filled('status')) {
            $query->where('status_kelayakan', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('calonPenerima', fn($q) =>
                $q->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('kode_anak', 'like', '%'.$request->search.'%')
            );
        }

        $penilaian = $query->get();

        // Data calon mentah (untuk tab nilai mentah) — hanya yg punya nilai lengkap di mode ini
        $calon = CalonPenerima::where('periode', $periode)
            ->orderByRaw('CAST(SUBSTRING(kode_anak, 2) AS UNSIGNED)')
            ->get()
            ->filter(fn($c) => $c->nilaiLengkapSampai($kriteria, $modeDefault))
            ->values();

        return view('penilaian.index', compact(
            'penilaian', 'calon', 'periode', 'threshold',
            'kriteria', 'kriteriaMode',
            'modeDefault', 'modeOptions', 'sudahHitungPerMode'
        ));
    }

    public function destroy(Penilaian $penilaian)
    {
        $penilaian->delete();
        return redirect()->route('penilaian.index')
            ->with('success', 'Data penilaian berhasil dihapus.');
    }
}
