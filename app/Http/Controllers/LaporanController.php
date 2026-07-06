<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Kriteria;
use App\Models\Setting;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        $modeOptions = $kriteria->count() > 0 ? range(1, $kriteria->count()) : [1];

        return view('laporan.index', compact('periode', 'kriteria', 'modeOptions'));
    }

    public function cetak(Request $request)
    {
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $settings = Setting::all()->pluck('value', 'key');

        $jenis   = $request->jenis   ?? 'ranking';
        $format  = $request->format  ?? 'pdf';
        $modeN   = (int) $request->get('mode', $kriteria->count());
        $modeN   = max(1, min($modeN, $kriteria->count() ?: 1));

        $kriteriaDipilih = $request->kriteria ?? [];
        $kriteriaFilter  = count($kriteriaDipilih)
            ? $kriteria->whereIn('kode_kriteria', $kriteriaDipilih)
            : $kriteria->take($modeN);

        $query = Penilaian::with('calonPenerima')
            ->where('periode', $periode)
            ->where('mode_hitung', $modeN)
            ->orderBy('ranking');

        if ($jenis === 'layak') {
            $query->where('status_kelayakan', 'layak');
        }

        $ranking = $query->get();

        // ── CSV ──────────────────────────────────────────────────
        if ($format === 'csv') {
            $filename = 'laporan-saw-' . str_replace('/', '-', $periode) . '-mode' . $modeN . '.csv';
            return response()->streamDownload(function () use ($ranking, $kriteriaFilter) {
                $handle = fopen('php://output', 'w');
                $header = ['Rank', 'Kode', 'Nama'];
                foreach ($kriteriaFilter as $k) $header[] = $k->kode_kriteria . ' - ' . $k->nama_kriteria;
                $header[] = 'Skor Vi';
                $header[] = 'Status';
                fputcsv($handle, $header);

                foreach ($ranking as $r) {
                    $row = [$r->ranking, $r->calonPenerima->kode_anak, $r->calonPenerima->nama];
                    foreach ($kriteriaFilter as $k) {
                        $row[] = $r->calonPenerima->getNilai($k->kode_kriteria) ?? '-';
                    }
                    $row[] = number_format($r->skor_akhir, 4);
                    $row[] = strtoupper($r->status_kelayakan);
                    fputcsv($handle, $row);
                }
                fclose($handle);
            }, $filename);
        }

        // ── PDF Preview (inline HTML → browser print) ────────────
        return view('laporan.pdf', compact(
            'periode', 'ranking', 'kriteria',
            'kriteriaFilter', 'settings', 'jenis', 'modeN'
        ));
    }

    public function exportPdf(Request $request)
    {
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $settings = Setting::all()->pluck('value', 'key');

        $jenis   = $request->jenis   ?? 'ranking';
        $modeN   = (int) $request->get('mode', $kriteria->count());
        $modeN   = max(1, min($modeN, $kriteria->count() ?: 1));

        $kriteriaDipilih = $request->kriteria ?? [];
        $kriteriaFilter  = count($kriteriaDipilih)
            ? $kriteria->whereIn('kode_kriteria', $kriteriaDipilih)
            : $kriteria->take($modeN);

        $query = Penilaian::with('calonPenerima')
            ->where('periode', $periode)
            ->where('mode_hitung', $modeN)
            ->orderBy('ranking');

        if ($jenis === 'layak') {
            $query->where('status_kelayakan', 'layak');
        }

        $ranking = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf-download', compact(
            'periode', 'ranking', 'kriteria',
            'kriteriaFilter', 'settings', 'jenis', 'modeN'
        ))->setPaper('a4', 'portrait');

        $filename = 'laporan-saw-' . str_replace('/', '-', $periode) . '-mode' . $modeN . '.pdf';

        return $pdf->download($filename);
    }

    public function csv(Request $request)
    {
        $request->merge(['format' => 'csv']);
        return $this->cetak($request);
    }
}