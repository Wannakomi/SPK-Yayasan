@extends('layouts.ketua')
@section('title','Ranking Calon')
@section('page-title','Ranking Calon Penerima')
@section('page-sub','Hasil seleksi metode SAW — Periode '.$periode)

@section('content')

{{-- STAT CARDS --}}
<div class="Stats-Grid">
    <div class="Stat-Card blue">
        <div class="Stat-Info">
            <p class="Stat-Label">Total Calon</p>
            <h2 class="Stat-Val">{{ $totalCalon }}</h2>
            <p class="Stat-Delta gray"><ion-icon name="people-outline"></ion-icon>Periode {{ $periode }}</p>
        </div>
        <div class="Stat-Ring">
            <svg viewBox="0 0 36 36"><circle class="ring-bg" cx="18" cy="18" r="15.9"/><circle class="ring-fg" cx="18" cy="18" r="15.9" stroke="#4a90d9" stroke-dasharray="{{ min($totalCalon*10,100) }} 100"/></svg>
            <span>{{ $totalCalon }}</span>
        </div>
    </div>
    <div class="Stat-Card green">
        <div class="Stat-Info">
            <p class="Stat-Label">Dinyatakan Layak</p>
            <h2 class="Stat-Val">{{ $layak }}</h2>
            <p class="Stat-Delta up"><ion-icon name="checkmark-circle-outline"></ion-icon>Vi ≥ {{ $threshold }}</p>
        </div>
        <div class="Stat-Ring">
            <svg viewBox="0 0 36 36"><circle class="ring-bg" cx="18" cy="18" r="15.9"/><circle class="ring-fg" cx="18" cy="18" r="15.9" stroke="#2ecc8a" stroke-dasharray="{{ $totalCalon>0?round($layak/$totalCalon*100):0 }} 100"/></svg>
            <span>{{ $totalCalon>0?round($layak/$totalCalon*100):0 }}%</span>
        </div>
    </div>
    <div class="Stat-Card gold">
        <div class="Stat-Info">
            <p class="Stat-Label">Skor Tertinggi</p>
            <h2 class="Stat-Val" style="font-size:1.5rem">{{ $skorTertinggi ? number_format($skorTertinggi,4) : '—' }}</h2>
            <p class="Stat-Delta gray"><ion-icon name="trophy-outline"></ion-icon>{{ $ranking->first()?->calonPenerima->nama ?? '-' }}</p>
        </div>
        <div class="Stat-Ring">
            <svg viewBox="0 0 36 36"><circle class="ring-bg" cx="18" cy="18" r="15.9"/><circle class="ring-fg" cx="18" cy="18" r="15.9" stroke="#c9a84c" stroke-dasharray="{{ round($skorTertinggi*100) }} 100"/></svg>
            <span>{{ round($skorTertinggi*100) }}%</span>
        </div>
    </div>
</div>

@if(!$sudahDihitung)
<div class="Card">
    <div class="Empty-State" style="padding:60px 24px">
        <ion-icon name="calculator-outline" style="font-size:3.5rem;color:var(--gold);opacity:.5;display:block;margin:0 auto 16px"></ion-icon>
        <p style="font-size:.9rem;font-weight:700;color:var(--text-2);margin-bottom:6px">Belum ada hasil ranking</p>
        <p style="font-size:.8rem;color:var(--text-4)">Proses SAW belum dijalankan. Hubungi Admin Yayasan untuk menjalankan perhitungan.</p>
    </div>
</div>
@else

{{-- ALERT STATUS --}}
<div class="Alert success">
    <ion-icon name="checkmark-circle-outline"></ion-icon>
    <span><strong>{{ $layak }} calon</strong> dinyatakan layak menerima dana pendidikan periode {{ $periode }}. Threshold: Vi ≥ {{ $threshold }}.</span>
</div>

{{-- TOP 3 PODIUM --}}
@if($top3->count() >= 3)
<div class="Card">
    <div class="Card-Header">
        <div><p class="Card-Title">🏆 Pemenang Teratas</p><p class="Card-Sub">3 calon dengan skor SAW tertinggi</p></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1.25fr 1fr;gap:14px;align-items:end;padding-bottom:4px">
        {{-- Rank 2 --}}
        <div style="text-align:center">
            <p style="font-weight:700;color:var(--text-1);font-size:.84rem;margin-bottom:4px">{{ $top3[1]->calonPenerima->nama }}</p>
            <p style="font-size:.68rem;color:var(--text-4)">{{ $top3[1]->calonPenerima->kode_anak }}</p>
            <div style="margin-top:10px;padding:18px 0;background:linear-gradient(135deg,rgba(107,114,128,0.70),rgba(156,163,175,0.60));border-radius:var(--r-lg) var(--r-lg) 0 0;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.08)">
                <span style="font-size:1.5rem;font-weight:900;color:#fff">{{ number_format($top3[1]->skor_akhir,4) }}</span>
                <p style="font-size:.68rem;color:rgba(255,255,255,.75);margin-top:2px">🥈 Ranking #2</p>
            </div>
        </div>
        {{-- Rank 1 --}}
        <div style="text-align:center">
            <p style="font-weight:800;color:var(--gold-l);font-size:.9rem;margin-bottom:4px">{{ $top3[0]->calonPenerima->nama }}</p>
            <p style="font-size:.68rem;color:var(--text-4)">{{ $top3[0]->calonPenerima->kode_anak }}</p>
            <div style="margin-top:10px;padding:28px 0;background:linear-gradient(135deg,rgba(201,168,76,0.75),rgba(232,201,109,0.65));border-radius:var(--r-lg) var(--r-lg) 0 0;backdrop-filter:blur(10px);box-shadow:0 6px 24px rgba(201,168,76,0.25);border:1px solid rgba(201,168,76,0.35)">
                <span style="font-size:1.85rem;font-weight:900;color:#1a1200">{{ number_format($top3[0]->skor_akhir,4) }}</span>
                <p style="font-size:.72rem;color:rgba(26,18,0,.75);margin-top:2px;font-weight:700">🥇 TERBAIK</p>
            </div>
        </div>
        {{-- Rank 3 --}}
        <div style="text-align:center">
            <p style="font-weight:700;color:var(--text-1);font-size:.84rem;margin-bottom:4px">{{ $top3[2]->calonPenerima->nama }}</p>
            <p style="font-size:.68rem;color:var(--text-4)">{{ $top3[2]->calonPenerima->kode_anak }}</p>
            <div style="margin-top:10px;padding:13px 0;background:linear-gradient(135deg,rgba(146,64,14,0.70),rgba(180,83,9,0.60));border-radius:var(--r-lg) var(--r-lg) 0 0;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.08)">
                <span style="font-size:1.5rem;font-weight:900;color:#fff">{{ number_format($top3[2]->skor_akhir,4) }}</span>
                <p style="font-size:.68rem;color:rgba(255,255,255,.75);margin-top:2px">🥉 Ranking #3</p>
            </div>
        </div>
    </div>
</div>
@endif

{{-- TABEL RANKING LENGKAP --}}
<div class="Card">
    <div class="Card-Header">
        <div><p class="Card-Title">Tabel Ranking Lengkap</p><p class="Card-Sub">Diurutkan berdasarkan skor SAW — threshold: {{ $threshold }}</p></div>
        <div class="Btn-Group">
            <a href="{{ route('ketua.export-csv') }}" class="Btn Btn-Secondary Btn-Sm"><ion-icon name="download-outline"></ion-icon>Export CSV</a>
            <a href="{{ route('ketua.laporan') }}" class="Btn Btn-Primary Btn-Sm"><ion-icon name="document-text-outline"></ion-icon>Laporan</a>
        </div>
    </div>
    <div class="Table-Wrap">
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Kode</th>
                    <th>Nama Lengkap</th>
                    <th>Jenjang</th>
                    @foreach($kriteria as $k)
                    <th class="Td-Center">{{ $k->kode_kriteria }}<br>
                        <small style="font-weight:400;text-transform:none;letter-spacing:0;opacity:.7">{{ $k->nama_kriteria }}</small>
                    </th>
                    @endforeach
                    <th>Skor Vi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ranking as $r)
                <tr style="{{ $r->is_layak ? 'background:rgba(201,168,76,0.04)' : '' }}">
                    <td>
                        @if($r->ranking == 1)<span class="Rank-Medal Medal-1">1</span>
                        @elseif($r->ranking == 2)<span class="Rank-Medal Medal-2">2</span>
                        @elseif($r->ranking == 3)<span class="Rank-Medal Medal-3">3</span>
                        @else<span class="Rank-Medal Medal-n">{{ $r->ranking }}</span>
                        @endif
                    </td>
                    <td class="Td-Code">{{ $r->calonPenerima->kode_anak }}</td>
                    <td class="Td-Bold">{{ $r->calonPenerima->nama }}</td>
                    <td><span class="Badge gray">{{ $r->calonPenerima->jenjang }} {{ $r->calonPenerima->kelas }}</span></td>
                    @foreach($kriteria as $k)
                    <td class="Td-Center">
                        {{ $r->calonPenerima->getNilai($k->kode_kriteria) !== null
                            ? number_format((float)$r->calonPenerima->getNilai($k->kode_kriteria), 2)
                            : '—' }}
                    </td>
                    @endforeach
                    <td>
                        <div class="Score-Bar-Wrap">
                            <div class="Score-Bar-Bg">
                                <div class="Score-Bar-Fill" style="width:{{ $r->skor_akhir*100 }}%;background:{{ $r->is_layak ? 'var(--gold)' : 'var(--text-4)' }}"></div>
                            </div>
                            <span class="Score-Num" style="color:{{ $r->is_layak ? 'var(--gold-l)' : 'var(--text-4)' }};font-weight:800">
                                {{ number_format($r->skor_akhir,4) }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="Badge {{ $r->is_layak ? 'gold' : 'gray' }}">
                            {{ $r->is_layak ? '✓ Layak' : '✗ Tidak Layak' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ 4 + $kriteria->count() }}"><div class="Empty-State"><p>Belum ada data.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;padding:10px 14px;background:rgba(201,168,76,0.08);border-radius:var(--r-md);border:1px solid rgba(201,168,76,0.20)">
        <p style="font-size:.76rem;color:var(--gold);font-weight:600">
            ⚠️ Garis batas threshold Vi ≥ {{ $threshold }} — calon di bawah nilai ini dinyatakan Tidak Layak.
        </p>
    </div>
</div>

{{-- BOBOT KRITERIA --}}
<div class="Card">
    <div class="Card-Header">
        <div><p class="Card-Title">Parameter Penilaian SAW</p><p class="Card-Sub">Bobot kriteria yang digunakan dalam perhitungan</p></div>
    </div>
    @php
        $colorPalette = ['#e05c5c','#2ecc8a','#4a90d9','#c9a84c','#a78bfa','#fb923c','#34d399','#60a5fa'];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px">
        @foreach($kriteria as $i => $k)
        @php $c = $colorPalette[$i % count($colorPalette)]; @endphp
        <div style="padding:16px;background:rgba(255,255,255,0.04);border-radius:var(--r-md);border:1px solid var(--border);text-align:center">
            <p style="font-size:.68rem;color:var(--text-4);margin-bottom:6px">{{ $k->kode_kriteria }} — {{ $k->nama_kriteria }}</p>
            <p style="font-size:1.8rem;font-weight:900;color:{{ $c }}">{{ $k->bobot*100 }}%</p>
            <span class="Badge {{ $k->atribut==='cost'?'red':'green' }}" style="margin-top:8px;font-size:.62rem">{{ strtoupper($k->atribut) }}</span>
            <div class="Acq-Bar-Wrap" style="margin-top:10px">
                <div class="Acq-Bar" style="width:{{ $k->bobot*100 }}%;background:{{ $c }}"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection