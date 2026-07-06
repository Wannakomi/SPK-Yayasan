<?php

namespace App\Http\Controllers;

use App\Models\CalonPenerima;
use App\Models\Kriteria;
use App\Models\Setting;
use Illuminate\Http\Request;

class CalonPenerimaController extends Controller
{
    public function index(Request $request)
    {
        $periode = Setting::get('periode_aktif', '2025/2026');

        $query = CalonPenerima::where('periode', $periode);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama', 'like', "%{$q}%")
                   ->orWhere('kode_anak', 'like', "%{$q}%");
            });
        }

        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        $calon = $query
            ->orderByRaw('CAST(SUBSTRING(kode_anak, 2) AS UNSIGNED)')
            ->paginate(15);

        return view('calon-penerima.index', compact('calon', 'periode', 'kriteria'));
    }

    public function create()
    {
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $nextKode = CalonPenerima::generateKode($periode);
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        return view('calon-penerima.create', compact('periode', 'nextKode', 'kriteria'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama'    => 'required|string|max:255',
            'jenjang' => 'nullable|string',
            'kelas'   => 'nullable|string',
            'catatan' => 'nullable|string',
        ];

        foreach (Kriteria::all() as $k) {
            $rules['nilai_kriteria.' . $k->kode_kriteria] = 'nullable|numeric|min:0';
        }

        $request->validate($rules);

        CalonPenerima::create([
            'kode_anak'      => CalonPenerima::generateKode(),
            'nama'           => $request->nama,
            'nilai_kriteria' => $request->nilai_kriteria,
            'jenjang'        => $request->jenjang,
            'kelas'          => $request->kelas,
            'catatan'        => $request->catatan,
            'periode'        => Setting::get('periode_aktif', '2025/2026'),
            'created_by'     => auth()->id(),
        ]);

        return redirect()->route('calon-penerima.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit(CalonPenerima $calonPenerima)
    {
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        return view('calon-penerima.edit', [
            'calon'    => $calonPenerima,
            'kriteria' => $kriteria,
        ]);
    }

    public function update(Request $request, CalonPenerima $calonPenerima)
    {
        $rules = [
            'nama'    => 'required|string|max:150',
            'jenjang' => 'nullable|in:SD,SMP,SMA/SMK',
            'kelas'   => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
        ];

        foreach (Kriteria::all() as $k) {
            $rules['nilai_kriteria.' . $k->kode_kriteria] = 'nullable|numeric|min:0';
        }

        $request->validate($rules);

        $calonPenerima->update([
            'nama'           => $request->nama,
            'nilai_kriteria' => $request->nilai_kriteria,
            'jenjang'        => $request->jenjang,
            'kelas'          => $request->kelas,
            'catatan'        => $request->catatan,
        ]);

        return redirect()->route('calon-penerima.index')->with('success', 'Data calon berhasil diperbarui.');
    }

    public function destroy(CalonPenerima $calonPenerima)
    {
        $calonPenerima->penilaian()->delete();
        $calonPenerima->delete();

        return redirect()->route('calon-penerima.index')
            ->with('success', 'Data calon berhasil dihapus. Kode dapat digunakan kembali.');
    }

    public function export()
    {
        $periode  = Setting::get('periode_aktif', '2025/2026');
        $calon    = CalonPenerima::where('periode', $periode)
            ->orderByRaw('CAST(SUBSTRING(kode_anak, 2) AS UNSIGNED)')
            ->get();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        return response()->streamDownload(function () use ($calon, $kriteria) {
            $file   = fopen('php://output', 'w');
            $header = ['Kode', 'Nama'];
            foreach ($kriteria as $k) {
                $header[] = $k->kode_kriteria . ' - ' . $k->nama_kriteria;
            }
            $header[] = 'Jenjang';
            $header[] = 'Kelas';
            fputcsv($file, $header);

            foreach ($calon as $c) {
                $row = [$c->kode_anak, $c->nama];
                foreach ($kriteria as $k) {
                    $row[] = $c->getNilai($k->kode_kriteria) ?? '-';
                }
                $row[] = $c->jenjang;
                $row[] = $c->kelas;
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'calon-penerima-' . now()->format('Y-m-d') . '.csv');
    }
}
