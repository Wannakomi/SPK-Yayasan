@extends('layouts.app')

@section('title','Proses SAW')
@section('page-title','Proses SAW')
@section('page-sub','Simple Additive Weighting — Periode '.$periode)

@section('content')

@php
    $view = request('view','raw');
@endphp

{{-- ALERT --}}
@if(session('success'))
<div class="Alert success">
    <ion-icon name="checkmark-circle-outline"></ion-icon>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="Alert danger">
    <ion-icon name="close-circle-outline"></ion-icon>
    {{ session('error') }}
</div>
@endif

{{-- HEADER --}}
<div class="Card" style="margin-bottom:16px">
    <div class="Card-Header">
        <div>
            <p class="Card-Title">Proses SAW</p>
            <p class="Card-Sub">
                Periode {{ $periode }} • Mode {{ $modeDefault }} Kriteria
            </p>
        </div>

        <div class="Btn-Group">
            <span class="Badge purple">{{ $calon->count() }} Calon</span>
            <span class="Badge blue">{{ $modeDefault }} Kriteria</span>
            <span class="Badge">{{ $bobotValid ? 'Bobot Valid' : 'Bobot Tidak Valid' }}</span>
        </div>
    </div>
</div>

{{-- STATUS PANEL --}}
<div class="Charts-Row">

    <div class="Card" style="margin-bottom:0">
        <div class="Card-Header">
            <p class="Card-Title">Status Sistem</p>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px">

            <div class="Alert {{ $calon->count() > 0 ? 'success' : 'danger' }}">
                <ion-icon name="people-outline"></ion-icon>
                Data Calon: {{ $calon->count() }} orang
            </div>

            <div class="Alert {{ $bobotValid ? 'success' : 'danger' }}">
                <ion-icon name="layers-outline"></ion-icon>
                Total Bobot: {{ $bobotValid ? 'Valid (100%)' : 'Tidak valid' }}
            </div>

        </div>
    </div>

   <div class="Card" style="margin-bottom:0">

    <div class="Card-Header">
        <div>
            <p class="Card-Title">Mode & Konfigurasi</p>
            <p class="Card-Sub">Pilih jumlah kriteria yang digunakan</p>
        </div>
    </div>

    {{-- SEGMENTED MODE (MODERN UI) --}}
    <form method="GET">

        <div style="
            display:flex;
            gap:8px;
            flex-wrap:wrap;
            padding:6px;
            background:rgba(255,255,255,.35);
            border:1px solid var(--border-2);
            border-radius:14px;
        ">

            @foreach($modeOptions as $mode)

                <button type="submit"
                        name="mode"
                        value="{{ $mode }}"
                        style="
                            flex:1;
                            min-width:70px;
                            padding:10px 12px;
                            border-radius:10px;
                            border:none;
                            cursor:pointer;
                            font-weight:800;
                            font-size:.85rem;

                            background:{{ $modeDefault == $mode ? 'var(--purple)' : 'transparent' }};
                            color:{{ $modeDefault == $mode ? '#fff' : 'var(--text-2)' }};
                            transition:.2s;
                        ">

                    {{ $mode }} Kriteria

                </button>

            @endforeach

        </div>

    </form>

    {{-- THRESHOLD INFO --}}
    <div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center">

        <div>
            <p style="font-size:.72rem;color:var(--text-4)">
                Threshold Kelayakan
            </p>

            <p style="font-weight:800;color:var(--purple)">
                {{ \App\Models\Setting::get('threshold_layak','0.75') }}
            </p>
        </div>

        <span class="Badge purple">
            Mode aktif: {{ $modeDefault }}
        </span>

    </div>

</div>

</div>

{{-- VIEW SWITCH BUTTON --}}
<div class="Card" style="margin-top:16px">

    <div class="Btn-Group">

        <a href="?mode={{ $modeDefault }}&view=raw"
           class="Btn {{ $view=='raw' ? 'Btn-Primary' : '' }}">
            Data Mentah
        </a>

        <a href="?mode={{ $modeDefault }}&view=norm"
           class="Btn {{ $view=='norm' ? 'Btn-Primary' : '' }}">
            Normalisasi
        </a>

        <a href="?mode={{ $modeDefault }}&view=weighted"
           class="Btn {{ $view=='weighted' ? 'Btn-Primary' : '' }}">
            Terbobot
        </a>

    </div>

</div>

{{-- TABLE SAW --}}
<div class="Card">

    <div class="Card-Header">
        <div>
            <p class="Card-Title">Matrix SAW</p>
            <p class="Card-Sub">
                @if($view=='raw')
                    Menampilkan Data Mentah
                @elseif($view=='norm')
                    Menampilkan Hasil Normalisasi
                @else
                    Menampilkan Hasil Terbobot
                @endif
            </p>
        </div>
    </div>

    @if(!empty($preview))

    <div class="Table-Wrap">

        <table>

            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>

                    @foreach($kriteria->take($modeDefault) as $k)
                        <th class="Td-Center">{{ $k->kode_kriteria }}</th>
                    @endforeach

                    <th class="Td-Center">Skor Akhir</th>
                </tr>
            </thead>

            <tbody>

            @foreach($preview as $row)
            <tr>

                <td class="Td-Code">{{ $row['kode_anak'] }}</td>
                <td class="Td-Bold">{{ $row['nama'] }}</td>

                {{-- VIEW LOGIC --}}
                @foreach($kriteria->take($modeDefault) as $k)

                    @php
                        $kode = $k->kode_kriteria;

                        if($view == 'raw') {
                            $val = $row['nilai_raw'][$kode] ?? 0;
                        }
                        elseif($view == 'norm') {
                            $val = $row['normalisasi'][$kode] ?? 0;
                        }
                        else {
                            $val = $row['terbobot'][$kode] ?? 0;
                        }
                    @endphp

                    <td class="Matrix-Cell">
                        {{ number_format($val,4) }}
                    </td>

                @endforeach

                <td class="Td-Center">
                    <span style="font-weight:900;color:var(--purple)">
                        {{ number_format($row['skor_akhir'],4) }}
                    </span>
                </td>

            </tr>
            @endforeach

            </tbody>

        </table>

    </div>

    @else

    <div class="Empty-State">
        <ion-icon name="calculator-outline"></ion-icon>
        <p>Tidak ada data SAW</p>
    </div>

    @endif

</div>

{{-- ACTION PANEL --}}
<div class="Card" style="margin-top:16px">

    <div style="display:flex;justify-content:space-between;align-items:center">

        <div>
            <p style="font-weight:800">
                {{ $bobotValid && $calon->count() > 0
                    ? 'Siap menjalankan SAW'
                    : 'Data belum lengkap'
                }}
            </p>

            <p style="font-size:.8rem;color:var(--text-3)">
                Sistem akan menghitung dan menyimpan ranking otomatis
            </p>
        </div>

        <div class="Btn-Group">

            @if($bobotValid && $calon->count() > 0)
            <form method="POST" action="{{ route('saw.hitung') }}">
                @csrf

                <input type="hidden" name="mode_hitung" value="{{ $modeDefault }}">

                <button class="Btn Btn-Primary">
                    Jalankan SAW
                </button>
            </form>
            @endif

            @if($sudahHitungPerMode[$modeDefault] ?? false)
            <form method="POST" action="{{ route('saw.reset') }}">
                @csrf

                <input type="hidden" name="mode_hitung" value="{{ $modeDefault }}">

                <button class="Btn Btn-Danger">
                    Reset
                </button>
            </form>
            @endif

        </div>

    </div>

</div>

@endsection