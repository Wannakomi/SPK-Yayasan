<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria   = Kriteria::orderBy('kode_kriteria')->get();
        $totalBobot = Kriteria::totalBobot();
        $bobotValid = Kriteria::bobotValid();
        return view('kriteria.index', compact('kriteria', 'totalBobot', 'bobotValid'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kriteria' => 'required|string|max:10|unique:kriteria,kode_kriteria',
            'nama_kriteria' => 'required|string|max:150',
            'atribut'       => 'required|in:cost,benefit',
            'bobot'         => 'required|numeric|min:0|max:1',
            'satuan'        => 'nullable|string|max:50',
            'keterangan'    => 'nullable|string',
        ]);

        $newTotal = Kriteria::totalBobot() + (float) $request->bobot;
        if ($newTotal > 1.001) {
            return back()->withErrors(['bobot' => 'Total bobot tidak boleh melebihi 100%. Sisa: ' . round((1 - Kriteria::totalBobot()) * 100, 1) . '%'])->withInput();
        }

        Kriteria::create(array_merge(
            $request->only(['kode_kriteria', 'nama_kriteria', 'atribut', 'bobot', 'satuan', 'keterangan']),
            ['created_by' => auth()->id()]
        ));

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, Kriteria $kriterium)
    {
        $request->validate([
            'kode_kriteria' => 'required|string|max:10|unique:kriteria,kode_kriteria,' . $kriterium->id,
            'nama_kriteria' => 'required|string|max:150',
            'atribut'       => 'required|in:cost,benefit',
            'bobot'         => 'required|numeric|min:0|max:1',
            'satuan'        => 'nullable|string|max:50',
            'keterangan'    => 'nullable|string',
        ]);

        $lainnya  = Kriteria::where('id', '!=', $kriterium->id)->sum('bobot');
        $newTotal = $lainnya + (float) $request->bobot;
        if ($newTotal > 1.001) {
            return back()->withErrors(['bobot' => 'Total bobot melebihi 100%.'])->withInput();
        }

        $kriterium->update($request->only(['kode_kriteria', 'nama_kriteria', 'atribut', 'bobot', 'satuan', 'keterangan']));

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriterium)
    {
        if (Kriteria::count() <= 1) {
            return back()->with('error', 'Minimal harus ada 1 kriteria.');
        }
        $kriterium->delete();
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }
}
