@extends('layouts.app')
@section('title','Data Penilaian')
@section('page-title','Data Penilaian SAW')
@section('page-sub','Hasil perhitungan — Periode '.$periode)

@section('content')

{{-- MODE SELECTOR --}}
<div style="display:flex;gap:8px;align-items:center;margin-bottom:16px;flex-wrap:wrap">
    <span style="font-size:.8rem;font-weight:700;color:var(--text-2)">Mode Kriteria:</span>
    @foreach($modeOptions as $m)
    <a href="{{ request()->fullUrlWithQuery(['mode'=>$m]) }}"
       class="Btn {{ $m==$modeDefault?'Btn-Primary':'Btn-Secondary' }} Btn-Sm">
        {{ $m }} Kriteria
        @if(isset($sudahHitungPerMode[$m]) && $sudahHitungPerMode[$m])
        <span style="width:6px;height:6px;border-radius:50%;background:var(--green);display:inline-block;margin-left:3px"></span>
        @endif
    </a>
    @endforeach
    <span class="Badge gray" style="margin-left:4px">Mode {{ $modeDefault }}: pakai {{ $kriteriaMode->pluck('kode_kriteria')->join(', ') }}</span>
</div>

{{-- TABS --}}
<div style="display:flex;gap:4px;padding:4px;background:rgba(255,255,255,0.10);border-radius:var(--r-md);width:fit-content;margin-bottom:18px;border:1.5px solid var(--border);backdrop-filter:blur(10px)">
    <button onclick="showTab('raw')"    id="tab-raw"    class="Btn Btn-Primary Btn-Sm">Nilai Mentah</button>
    <button onclick="showTab('norm')"   id="tab-norm"   class="Btn Btn-Secondary Btn-Sm">Normalisasi (R)</button>
    <button onclick="showTab('weight')" id="tab-weight" class="Btn Btn-Secondary Btn-Sm">Terbobot (V) & Skor</button>
</div>

{{-- TAB: NILAI MENTAH --}}
<div id="pane-raw">
    <div class="Info-Box blue"><ion-icon name="information-circle-outline"></ion-icon>
        <div class="Info-Box-Text"><p><strong>Nilai Mentah</strong> — data asli sebelum normalisasi SAW. Hanya calon dengan nilai lengkap untuk {{ $modeDefault }} kriteria yang ditampilkan.</p></div>
    </div>
    <div class="Card">
        <div class="Card-Header">
            <div><p class="Card-Title">Matriks Keputusan — Mode {{ $modeDefault }} Kriteria</p></div>
            <div class="Btn-Group">
                @foreach($kriteriaMode as $k)
                <span class="Badge {{ $k->atribut==='cost'?'red':'green' }}">{{ $k->kode_kriteria }} = {{ strtoupper($k->atribut) }}</span>
                @endforeach
            </div>
        </div>
        <div class="Table-Wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th><th>Nama</th>
                        @foreach($kriteriaMode as $k)
                        <th class="Td-Center">{{ $k->kode_kriteria }}<br><small style="font-weight:400;text-transform:none;letter-spacing:0;opacity:.7">{{ $k->nama_kriteria }} · {{ $k->bobot_persen }}</small></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($calon as $c)
                    <tr>
                        <td class="Td-Code">{{ $c->kode_anak }}</td>
                        <td class="Td-Bold">{{ $c->nama }}</td>
                        @foreach($kriteriaMode as $k)
                        @php $val = $c->getNilai($k->kode_kriteria); @endphp
                        <td class="Matrix-Cell">
                            {{ $val !== null ? number_format((float)$val, 2) : '—' }}
                            @if($k->satuan) <small style="opacity:.6;font-size:.68rem"> {{ $k->satuan }}</small> @endif
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr><td colspan="{{ 2 + $kriteriaMode->count() }}">
                        <div class="Empty-State"><ion-icon name="document-outline"></ion-icon><p>Belum ada calon dengan nilai lengkap untuk {{ $modeDefault }} kriteria.</p></div>
                    </td></tr>
                    @endforelse
                    {{-- Baris max/min --}}
                    @if($calon->count() > 0)
                    <tr style="background:rgba(201,168,76,0.08)">
                        <td colspan="2" style="font-weight:800;color:var(--gold-l);font-size:.75rem">MAX (Benefit) / MIN (Cost)</td>
                        @foreach($kriteriaMode as $k)
                        @php
                            $vals = $calon->map(fn($c) => (float)($c->getNilai($k->kode_kriteria) ?? 0));
                            $stat = $k->atribut === 'cost' ? 'MIN = '.number_format($vals->min(),2) : 'MAX = '.number_format($vals->max(),2);
                        @endphp
                        <td class="Matrix-Cell" style="color:var(--gold-l);font-weight:800">{{ $stat }}</td>
                        @endforeach
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- TAB: NORMALISASI --}}
<div id="pane-norm" style="display:none">
    <div class="Info-Box purple"><ion-icon name="calculator-outline"></ion-icon>
        <div class="Info-Box-Text"><p><strong>Normalisasi SAW:</strong> Cost → R = min(Cj)/Xij &nbsp;|&nbsp; Benefit → R = Xij/max(Cj). Bobot dinormalisasi ulang agar total = 100% untuk {{ $modeDefault }} kriteria.</p></div>
    </div>
    <div class="Card">
        <div class="Card-Header"><div><p class="Card-Title">Matriks Ternormalisasi (R) — Mode {{ $modeDefault }}</p></div></div>
        <div class="Table-Wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th><th>Kode</th><th>Nama</th>
                        @foreach($kriteriaMode as $k)
                        <th class="Td-Center">R({{ $k->kode_kriteria }})<br><small style="font-weight:400;text-transform:none;letter-spacing:0;opacity:.7">{{ $k->atribut==='cost'?'min/Xi':'Xi/max' }}</small></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($penilaian as $p)
                    <tr>
                        <td><span class="Rank-Medal {{ $p->ranking==1?'Medal-1':($p->ranking==2?'Medal-2':($p->ranking==3?'Medal-3':'Medal-n')) }}">{{ $p->ranking }}</span></td>
                        <td class="Td-Code">{{ $p->calonPenerima->kode_anak }}</td>
                        <td class="Td-Bold">{{ $p->calonPenerima->nama }}</td>
                        @foreach($kriteriaMode as $k)
                        @php $r = $p->getNormalisasi($k->kode_kriteria); @endphp
                        <td class="Matrix-Cell {{ $r>=0.8?'Cell-High':($r<=0.4?'Cell-Low':'Cell-Mid') }}">{{ number_format($r,4) }}</td>
                        @endforeach
                    </tr>
                    @empty
                    <tr><td colspan="{{ 3 + $kriteriaMode->count() }}">
                        <div class="Empty-State"><ion-icon name="calculator-outline"></ion-icon><p>Belum ada data. <a href="{{ route('saw.index') }}" style="color:var(--gold-l)">Jalankan Proses SAW →</a></p></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- TAB: TERBOBOT --}}
<div id="pane-weight" style="display:none">
    <div class="Info-Box blue"><ion-icon name="stats-chart-outline"></ion-icon>
        <div class="Info-Box-Text"><p><strong>Nilai Terbobot:</strong> Vij = Wj × Rij (bobot dinormalisasi per mode). Skor Akhir: Vi = Σ(Vij). Threshold layak: ≥ {{ $threshold }}</p></div>
    </div>
    <div class="Card">
        <div class="Card-Header">
            <div><p class="Card-Title">Matriks Terbobot & Skor Akhir — Mode {{ $modeDefault }}</p></div>
            <div class="Btn-Group">
                <a href="{{ route('laporan.index') }}" class="Btn Btn-Secondary Btn-Sm"><ion-icon name="print-outline"></ion-icon>Laporan</a>
                <span class="Badge gold">READ ONLY</span>
            </div>
        </div>
        <div class="Table-Wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th><th>Kode</th><th>Nama</th>
                        @foreach($kriteriaMode as $k)
                        <th class="Td-Center">V({{ $k->kode_kriteria }})</th>
                        @endforeach
                        <th>Skor Vi</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penilaian as $p)
                    <tr style="{{ $p->is_layak?'background:rgba(201,168,76,0.04)':'' }}">
                        <td><span class="Rank-Medal {{ $p->ranking==1?'Medal-1':($p->ranking==2?'Medal-2':($p->ranking==3?'Medal-3':'Medal-n')) }}">{{ $p->ranking }}</span></td>
                        <td class="Td-Code">{{ $p->calonPenerima->kode_anak }}</td>
                        <td class="Td-Bold">{{ $p->calonPenerima->nama }}</td>
                        @foreach($kriteriaMode as $k)
                        <td class="Matrix-Cell">{{ number_format($p->getTerbobot($k->kode_kriteria),4) }}</td>
                        @endforeach
                        <td>
                            <div class="Score-Bar-Wrap">
                                <div class="Score-Bar-Bg"><div class="Score-Bar-Fill" style="width:{{ $p->skor_akhir*100 }}%;background:{{ $p->is_layak?'var(--gold)':'var(--text-4)' }}"></div></div>
                                <span class="Score-Num" style="color:{{ $p->is_layak?'var(--gold-l)':'var(--text-4)' }};font-weight:800">{{ number_format($p->skor_akhir,4) }}</span>
                            </div>
                        </td>
                        <td><span class="Badge {{ $p->is_layak?'gold':'gray' }}">{{ $p->is_layak?'✓ Layak':'✗ Tidak' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ 4 + $kriteriaMode->count() }}">
                        <div class="Empty-State"><ion-icon name="stats-chart-outline"></ion-icon><p>Belum ada penilaian untuk mode {{ $modeDefault }} kriteria.</p></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showTab(name) {
    ['raw','norm','weight'].forEach(t => {
        document.getElementById('pane-'+t).style.display = t===name ? '' : 'none';
        const btn = document.getElementById('tab-'+t);
        btn.className = t===name ? 'Btn Btn-Primary Btn-Sm' : 'Btn Btn-Secondary Btn-Sm';
    });
}
</script>
@endpush
