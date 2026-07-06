@extends('layouts.app')
@section('title','Cetak Laporan')
@section('page-title','Cetak Laporan')
@section('page-sub','Export hasil penilaian SAW — Periode '.$periode)

@section('content')

<form action="{{ route('laporan.cetak') }}" method="GET" target="_blank">

<div class="Charts-Row">

    <div class="Card" style="margin-bottom:0">
        <div class="Card-Header"><div><p class="Card-Title">Opsi Laporan</p><p class="Card-Sub">Pilih mode, jenis, dan format</p></div></div>

        {{-- Mode Kriteria --}}
        <p class="Form-Label" style="margin-bottom:8px">Mode Kriteria</p>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px">
            @foreach($modeOptions as $m)
            <label style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--r-md);border:1.5px solid var(--border);background:rgba(255,255,255,0.06);cursor:pointer;transition:all .18s"
                   onmouseover="this.style.borderColor='rgba(201,168,76,0.45)'"
                   onmouseout="if(!this.querySelector('input').checked)this.style.borderColor='var(--border)'">
                <input type="radio" name="mode" value="{{ $m }}"
                    {{ $m==($modeOptions[count($modeOptions)-1]??1)?'checked':'' }}
                    onchange="document.querySelectorAll('.mode-label').forEach(el=>el.style.borderColor='var(--border)');this.closest('label').style.borderColor='rgba(201,168,76,0.65)'">
                <span style="font-size:.8rem;font-weight:700;color:var(--text-1)">{{ $m }} Kriteria</span>
            </label>
            @endforeach
        </div>

        {{-- Jenis --}}
        <p class="Form-Label" style="margin-bottom:8px">Jenis Laporan</p>
        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:18px">
            @foreach([['ranking','Ranking Lengkap','Semua peserta + skor + status'],['layak','Penerima Layak Saja','Hanya yang dinyatakan layak'],['analisis','Analisis SAW','Matriks normalisasi terinci']] as $j)
            <label style="display:flex;align-items:center;gap:10px;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--r-md);cursor:pointer;background:rgba(255,255,255,0.04);transition:all .18s"
                   onmouseover="this.style.borderColor='rgba(201,168,76,0.40)'"
                   onmouseout="if(!this.querySelector('input').checked)this.style.borderColor='var(--border)'">
                <input type="radio" name="jenis" value="{{ $j[0] }}" {{ $j[0]==='ranking'?'checked':'' }}>
                <div>
                    <p style="font-size:.82rem;font-weight:700;color:var(--text-1)">{{ $j[1] }}</p>
                    <p style="font-size:.7rem;color:var(--text-4)">{{ $j[2] }}</p>
                </div>
            </label>
            @endforeach
        </div>

        {{-- Format --}}
        <p class="Form-Label" style="margin-bottom:8px">Format Output</p>
        <div style="display:flex;gap:10px;margin-bottom:20px">
            <label style="display:flex;align-items:center;gap:8px;padding:10px 16px;border:1.5px solid rgba(224,92,92,0.35);border-radius:var(--r-md);cursor:pointer;background:rgba(224,92,92,0.08);flex:1">
                <input type="radio" name="format" value="pdf" checked>
                <div>
                    <p style="font-size:.82rem;font-weight:700;color:var(--red-l)">📄 PDF Preview</p>
                    <p style="font-size:.7rem;color:var(--text-4)">Tampilan jurnal resmi, bisa cetak</p>
                </div>
            </label>
            <label style="display:flex;align-items:center;gap:8px;padding:10px 16px;border:1.5px solid rgba(46,204,138,0.35);border-radius:var(--r-md);cursor:pointer;background:rgba(46,204,138,0.08);flex:1">
                <input type="radio" name="format" value="csv">
                <div>
                    <p style="font-size:.82rem;font-weight:700;color:var(--green-l)">📊 Download CSV</p>
                    <p style="font-size:.7rem;color:var(--text-4)">Data tabel untuk Excel</p>
                </div>
            </label>
        </div>

        <button type="submit" class="Btn Btn-Primary" style="width:100%;justify-content:center;padding:12px;font-size:.9rem">
            <ion-icon name="document-text-outline"></ion-icon>Generate Laporan
        </button>
    </div>

    <div class="Card" style="margin-bottom:0">
        <div class="Card-Header"><div><p class="Card-Title">Pilih Kriteria Ditampilkan</p><p class="Card-Sub">Centang kriteria yang ingin masuk laporan</p></div></div>
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach($kriteria as $k)
            @php $colors=['C1'=>'#e05c5c','C2'=>'#c9a84c','C3'=>'#2ecc8a','C4'=>'#4a90d9','C5'=>'#a07de0']; $c=$colors[$k->kode_kriteria]??'#c9a84c'; @endphp
            <label class="Toggle-Label" style="padding:10px 13px;background:rgba(255,255,255,0.04);border-radius:var(--r-md);border:1px solid var(--border)">
                <input type="checkbox" name="kriteria[]" value="{{ $k->kode_kriteria }}" class="Toggle-Input" checked>
                <div class="Toggle-Track"></div>
                <div style="display:flex;align-items:center;gap:8px;flex:1">
                    <span style="width:28px;height:28px;border-radius:50%;background:{{ $c }}22;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800;color:{{ $c }}">{{ $k->kode_kriteria }}</span>
                    <div>
                        <p style="font-size:.8rem;font-weight:700;color:var(--text-1)">{{ $k->nama_kriteria }}</p>
                        <p style="font-size:.68rem;color:var(--text-4)">{{ strtoupper($k->atribut) }} · {{ $k->bobot_persen }}</p>
                    </div>
                </div>
            </label>
            @endforeach
        </div>

        <div class="Alert info" style="margin-top:16px;margin-bottom:0">
            <ion-icon name="information-circle-outline"></ion-icon>
            <p style="font-size:.76rem">PDF akan terbuka di tab baru. Gunakan <strong>Ctrl+P</strong> atau tombol Cetak untuk simpan sebagai PDF.</p>
        </div>
    </div>

</div>

</form>

@endsection
