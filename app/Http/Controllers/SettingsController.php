<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $users    = User::query()->orderBy('role')->get();
        return view('settings.index', compact('settings', 'users'));
    }

    public function updateUmum(Request $request)
    {
        $request->validate([
            'app_name'       => 'required|string|max:100',
            'yayasan_name'   => 'required|string|max:150',
            'yayasan_address'=> 'nullable|string',
            'yayasan_phone'  => 'nullable|string|max:30',
            'yayasan_email'  => 'nullable|email|max:100',
            'timezone'       => 'nullable|string|max:50',
        ]);

        foreach ($request->only(['app_name','yayasan_name','yayasan_address','yayasan_phone','yayasan_email','timezone']) as $key => $val) {
            Setting::set($key, $val);
        }

        return redirect()->route('settings.index')->with('success', 'Identitas sistem berhasil diperbarui.');
    }

    public function updateSpk(Request $request)
    {
        $request->validate([
            'threshold_layak' => 'required|numeric|min:0|max:1',
            'kuota_penerima'  => 'required|integer|min:1',
        ]);

        Setting::set('threshold_layak', $request->threshold_layak, 'spk');
        Setting::set('kuota_penerima',  $request->kuota_penerima,  'spk');

        return redirect()->route('settings.index')->with('success', 'Konfigurasi SAW berhasil disimpan.');
    }

    public function updatePeriode(Request $request)
    {
        $request->validate(['periode_aktif' => 'required|string|max:20']);
        Setting::set('periode_aktif', $request->periode_aktif, 'spk');
        return redirect()->route('settings.index')->with('success', 'Periode aktif berhasil diperbarui.');
    }

    public function bukaPeriode(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:20',
        ]);
        Setting::set('periode_aktif', $request->nama_periode, 'spk');
        return redirect()->route('settings.index')->with('success', "Periode {$request->nama_periode} berhasil dibuka.");
    }
}
