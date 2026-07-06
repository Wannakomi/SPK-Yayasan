<?php

namespace App\Http\Controllers;

use App\Models\CalonPenerima;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SawController extends Controller
{
    public function index(Request $request)
    {
        $periode    = Setting::get('periode_aktif', '2025/2026');
        $kriteria   = Kriteria::orderBy('kode_kriteria')->get();
        $calon      = CalonPenerima::where('periode', $periode)
            ->orderByRaw('CAST(SUBSTRING(kode_anak, 2) AS UNSIGNED)')
            ->get();

        $bobotValid = Kriteria::bobotValid();

        $modeOptions = $kriteria->count() > 0
            ? range(1, $kriteria->count())
            : [1];

        $modeDefault = (int) $request->get('mode', $kriteria->count());

        if ($modeDefault < 1) $modeDefault = 1;
        if ($modeDefault > $kriteria->count()) $modeDefault = $kriteria->count();

        $sudahHitungPerMode = [];
        foreach ($modeOptions as $m) {
            $sudahHitungPerMode[$m] = Penilaian::where('periode', $periode)
                ->where('mode_hitung', $m)
                ->exists();
        }

        $previewMode = $modeDefault;
        $preview = $this->buildMatrix($calon, $kriteria, $modeDefault);

        return view('saw.index', compact(
            'periode', 'kriteria', 'calon', 'bobotValid',
            'modeOptions', 'modeDefault', 'sudahHitungPerMode',
            'preview', 'previewMode'
        ));
    }

    public function preview(Request $request)
    {
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $calon    = CalonPenerima::where('periode', $periode)
            ->orderByRaw('CAST(SUBSTRING(kode_anak, 2) AS UNSIGNED)')
            ->get();
        $mode    = (int) $request->get('mode', $kriteria->count());
        $preview = $this->buildMatrix($calon, $kriteria, $mode);

        return response()->json(['data' => $preview, 'mode' => $mode]);
    }

    public function hitung(Request $request)
    {
        $request->validate(['mode_hitung' => 'required|integer|min:1']);

        $periode   = Setting::get('periode_aktif', '2025/2026');
        $threshold = (float) Setting::get('threshold_layak', '0.75');
        $kriteria  = Kriteria::orderBy('kode_kriteria')->get();
        $modeN     = (int) $request->mode_hitung;

        $calon = CalonPenerima::where('periode', $periode)
            ->orderByRaw('CAST(SUBSTRING(kode_anak, 2) AS UNSIGNED)')
            ->get();

        if ($kriteria->count() < $modeN) {
            return redirect()->route('saw.index')
                ->with('error', "Jumlah kriteria ({$kriteria->count()}) kurang dari mode yang dipilih ({$modeN}).");
        }

        if (!Kriteria::bobotValid()) {
            return redirect()->route('saw.index')
                ->with('error', 'Total bobot kriteria belum 100%.');
        }

        $matrix = $this->buildMatrix($calon, $kriteria, $modeN);

        if (empty($matrix)) {
            return redirect()->route('saw.index')
                ->with('error', "Tidak ada calon dengan nilai lengkap untuk {$modeN} kriteria.");
        }

        $kriteriaUsed = $kriteria->take($modeN);

        DB::transaction(function () use ($matrix, $periode, $threshold, $modeN, $kriteriaUsed) {

            Penilaian::where('periode', $periode)
                ->where('mode_hitung', $modeN)
                ->each(function ($p) {
                    DB::table('penilaian_kriteria')->where('penilaian_id', $p->id)->delete();
                });

            Penilaian::where('periode', $periode)
                ->where('mode_hitung', $modeN)
                ->delete();

            usort($matrix, fn($a, $b) => $b['skor_akhir'] <=> $a['skor_akhir']);

            foreach ($matrix as $rank => $item) {

                $penilaian = Penilaian::updateOrCreate(
                    [
                        'calon_penerima_id' => $item['calon_id'],
                        'periode'           => $periode,
                        'mode_hitung'       => $modeN,
                    ],
                    [
                        'hasil_normalisasi' => $item['normalisasi'],
                        'hasil_terbobot'    => $item['terbobot'],
                        'skor_akhir'        => $item['skor_akhir'],
                        'ranking'           => $rank + 1,
                        'status_kelayakan'  => $item['skor_akhir'] >= $threshold ? 'layak' : 'tidak_layak',
                    ]
                );

                // Sync pivot penilaian_kriteria
                $pivotData = [];
                foreach ($kriteriaUsed as $k) {
                    $kode = $k->kode_kriteria;
                    $pivotData[$k->id] = [
                        'nilai_raw'         => $item['nilai_raw'][$kode] ?? null,
                        'nilai_normalisasi'  => $item['normalisasi'][$kode] ?? null,
                        'nilai_terbobot'     => $item['terbobot'][$kode] ?? null,
                    ];
                }
                $penilaian->kriteria()->sync($pivotData);

                // Sync pivot nilai_kriteria (calon <-> kriteria)
                $calon = CalonPenerima::find($item['calon_id']);
                if ($calon) {
                    $nilaiPivot = [];
                    foreach ($kriteriaUsed as $k) {
                        $nilaiPivot[$k->id] = [
                            'nilai' => $item['nilai_raw'][$k->kode_kriteria] ?? null,
                        ];
                    }
                    $calon->kriteria()->sync($nilaiPivot);
                }
            }
        });

        return redirect()->route('ranking.index', ['mode' => $modeN])
            ->with('success', "Proses SAW {$modeN} kriteria berhasil dijalankan.");
    }

    public function reset(Request $request)
    {
        $periode = Setting::get('periode_aktif', '2025/2026');
        $modeN   = $request->filled('mode_hitung') ? (int) $request->mode_hitung : null;

        $query = Penilaian::where('periode', $periode);
        if ($modeN) $query->where('mode_hitung', $modeN);

        $query->each(function ($p) {
            DB::table('penilaian_kriteria')->where('penilaian_id', $p->id)->delete();
        });

        $query = Penilaian::where('periode', $periode);
        if ($modeN) $query->where('mode_hitung', $modeN);
        $query->delete();

        $msg = $modeN
            ? "Data penilaian {$modeN} kriteria berhasil direset."
            : 'Semua data penilaian berhasil direset.';

        return redirect()->route('saw.index')->with('success', $msg);
    }

    public function buildMatrix($calon, $kriteria, int $modeN): array
    {
        if ($calon->isEmpty() || $kriteria->isEmpty()) return [];

        $kriteriaUsed  = $kriteria->take($modeN);
        $calonFiltered = $calon->filter(fn($c) => $c->nilaiLengkapSampai($kriteria, $modeN));

        if ($calonFiltered->isEmpty()) return [];

        $totalBobot = $kriteriaUsed->sum('bobot');
        $bobotNorm  = [];
        foreach ($kriteriaUsed as $k) {
            $bobotNorm[$k->kode_kriteria] = $totalBobot > 0 ? $k->bobot / $totalBobot : 0;
        }

        $stats = [];
        foreach ($kriteriaUsed as $k) {
            $vals = $calonFiltered->map(fn($c) => (float) $c->getNilai($k->kode_kriteria));
            $stats[$k->kode_kriteria] = [
                'max'     => $vals->max() ?: 1,
                'min'     => $vals->min() ?: 1,
                'atribut' => $k->atribut,
            ];
        }

        $matrix = [];
        foreach ($calonFiltered as $c) {
            $normalisasi = [];
            $terbobot    = [];
            $skor        = 0;

            foreach ($kriteriaUsed as $k) {
                $kode = $k->kode_kriteria;
                $xi   = (float) $c->getNilai($kode);
                $stat = $stats[$kode];

                $r = $stat['atribut'] === 'cost'
                    ? ($xi > 0 ? $stat['min'] / $xi : 0)
                    : ($stat['max'] > 0 ? $xi / $stat['max'] : 0);

                $v = $bobotNorm[$kode] * $r;

                $normalisasi[$kode] = round($r, 6);
                $terbobot[$kode]    = round($v, 6);
                $skor              += $v;
            }

            $matrix[] = [
                'calon_id'    => $c->id,
                'kode_anak'   => $c->kode_anak,
                'nama'        => $c->nama,
                'nilai_raw'   => $c->nilai_kriteria,
                'normalisasi' => $normalisasi,
                'terbobot'    => $terbobot,
                'skor_akhir'  => round($skor, 6),
            ];
        }

        usort($matrix, fn($a, $b) => strnatcmp($a['kode_anak'], $b['kode_anak']));

        return $matrix;
    }
}
