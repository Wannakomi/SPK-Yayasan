<?php

namespace App\Http\Controllers;

use App\Models\CalonPenerima;
use App\Models\Penilaian;
use App\Models\Kriteria;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class KetuaController extends Controller
{
    private function checkAccess()
    {
        if (!in_array(auth()->user()->role, ['ketua', 'superadmin'])) {
            abort(403, 'Halaman ini hanya untuk Ketua Yayasan.');
        }
    }

    public function index()
    {
        return redirect()->route('ketua.ranking');
    }

    public function ranking()
    {
        $this->checkAccess();
        $periode   = Setting::get('periode_aktif', '2025/2026');
        $threshold = (float) Setting::get('threshold_layak', '0.75');
        $kuota     = (int) Setting::get('kuota_penerima', '5');

        $ranking = Penilaian::with('calonPenerima')
            ->where('periode', $periode)
            ->orderBy('ranking')
            ->get();

        $totalCalon    = CalonPenerima::where('periode', $periode)->count();
        $layak         = $ranking->where('status_kelayakan', 'layak')->count();
        $tidakLayak    = $ranking->where('status_kelayakan', 'tidak_layak')->count();
        $skorTertinggi = $ranking->first()?->skor_akhir ?? 0;
        $kriteria      = Kriteria::orderBy('kode_kriteria')->get();
        $top3          = $ranking->where('status_kelayakan', 'layak')->take(3)->values();
        $sudahDihitung = $ranking->isNotEmpty();

        return view('ketua.ranking', compact(
            'periode', 'threshold', 'kuota', 'ranking',
            'totalCalon', 'layak', 'tidakLayak',
            'skorTertinggi', 'kriteria', 'top3', 'sudahDihitung'
        ));
    }

    public function laporan()
    {
        $this->checkAccess();
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $ranking  = Penilaian::with('calonPenerima')->where('periode', $periode)->orderBy('ranking')->get();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $settings = Setting::all()->pluck('value', 'key');
        $layak    = $ranking->where('status_kelayakan', 'layak')->count();
        return view('ketua.laporan', compact('periode', 'ranking', 'kriteria', 'settings', 'layak'));
    }

    public function cetakPdf()
    {
        $this->checkAccess();
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $ranking  = Penilaian::with('calonPenerima')->where('periode', $periode)->orderBy('ranking')->get();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $settings = Setting::all()->pluck('value', 'key');
        return view('ketua.cetak', compact('periode', 'ranking', 'kriteria', 'settings'));
    }

    public function downloadPdf()
    {
        $this->checkAccess();
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $ranking  = Penilaian::with('calonPenerima')->where('periode', $periode)->orderBy('ranking')->get();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $settings = Setting::all()->pluck('value', 'key');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ketua.pdf-download', compact(
            'periode', 'ranking', 'kriteria', 'settings'
        ))->setPaper('a4', 'portrait');

        $filename = 'laporan-resmi-' . str_replace('/', '-', $periode) . '.pdf';

        return $pdf->download($filename);
    }

    public function exportCsv()
    {
        $this->checkAccess();
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $ranking  = Penilaian::with('calonPenerima')->where('periode', $periode)->orderBy('ranking')->get();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $yayasan  = Setting::get('yayasan_name', 'Yayasan Sahabat Yatim');

        return response()->streamDownload(function () use ($ranking, $kriteria, $yayasan, $periode) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [$yayasan]);
            fputcsv($file, ['Laporan Hasil Seleksi — Metode SAW']);
            fputcsv($file, ['Periode: ' . $periode]);
            fputcsv($file, ['Tanggal: ' . now()->format('d/m/Y H:i')]);
            fputcsv($file, []);

            $header = ['Rank', 'Kode', 'Nama', 'Jenjang'];
            foreach ($kriteria as $k) {
                $header[] = $k->kode_kriteria . ' - ' . $k->nama_kriteria;
            }
            $header[] = 'Skor SAW';
            $header[] = 'Status';
            fputcsv($file, $header);

            foreach ($ranking as $r) {
                $row = [
                    $r->ranking,
                    $r->calonPenerima->kode_anak,
                    $r->calonPenerima->nama,
                    $r->calonPenerima->jenjang . ' ' . $r->calonPenerima->kelas,
                ];
                foreach ($kriteria as $k) {
                    $row[] = $r->calonPenerima->getNilai($k->kode_kriteria) ?? '-';
                }
                $row[] = number_format($r->skor_akhir, 4);
                $row[] = $r->is_layak ? 'Layak' : 'Tidak Layak';
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'laporan-ketua-' . str_replace('/', '-', $periode) . '.csv');
    }
}   