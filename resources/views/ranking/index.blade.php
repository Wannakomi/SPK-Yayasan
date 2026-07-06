@extends('layouts.app')

@section('title','Hasil Ranking')
@section('page-title','Hasil Ranking SAW')
@section('page-sub','Output akhir seleksi — Periode '.$periode)

@section('content')

@php
    $modeAktif = request('mode', $modeAktif ?? $kriteria->count());
@endphp

<style>

/* =========================================
   FILTER CARD
========================================= */

.Filter-Card{
    margin-bottom:18px;
}

/* =========================================
   STATS
========================================= */

.Stats-Grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:16px;
    margin-bottom:20px;
}

.Stat-Card{
    position:relative;
    overflow:hidden;
    border-radius:22px;
    padding:22px;
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,.08);
    transition:.35s ease;
}

.Stat-Card:hover{
    transform:translateY(-5px);
    box-shadow:0 15px 35px rgba(0,0,0,.08);
}

.Stat-Card::before{
    content:'';
    position:absolute;
    inset:0;
    opacity:.14;
}

.Stat-Card.purple{
    background:linear-gradient(135deg,#7c3aed,#a78bfa);
}

.Stat-Card.green{
    background:linear-gradient(135deg,#059669,#34d399);
}

.Stat-Card.orange{
    background:linear-gradient(135deg,#ea580c,#fb923c);
}

.Stat-Card.blue{
    background:linear-gradient(135deg,#2563eb,#60a5fa);
}

.Stat-Label{
    color:rgba(255,255,255,.85);
    font-size:.8rem;
    font-weight:600;
}

.Stat-Val{
    font-size:2rem;
    color:#fff;
    margin:10px 0;
    font-weight:900;
}

.Stat-Delta{
    color:#fff;
    display:flex;
    align-items:center;
    gap:5px;
    font-size:.75rem;
    font-weight:600;
}

/* =========================================
   PODIUM
========================================= */

.Podium{
    display:grid;
    grid-template-columns:1fr 1.2fr 1fr;
    gap:18px;
    align-items:end;
    margin-top:18px;
}

.Podium-Card{
    border-radius:24px 24px 0 0;
    text-align:center;
    color:#fff;
    position:relative;
    overflow:hidden;
    transition:.35s ease;
}

.Podium-Card:hover{
    transform:translateY(-6px);
}

.Podium-Name{
    font-weight:800;
    color:var(--text-1);
    margin-bottom:10px;
    font-size:.9rem;
}

.Podium-Card.gold{
    background:linear-gradient(
        135deg,
        #7c3aed,
        #a78bfa
    );
    padding:38px 0;
    box-shadow:0 15px 35px rgba(124,58,237,.35);
}

.Podium-Card.silver{
    background:linear-gradient(
        135deg,
        #94a3b8,
        #e2e8f0
    );
    padding:28px 0;
}

.Podium-Card.bronze{
    background:linear-gradient(
        135deg,
        #b45309,
        #fb923c
    );
    padding:22px 0;
}

.Podium-Score{
    font-size:1.9rem;
    font-weight:900;
}

.Podium-Rank{
    margin-top:5px;
    font-size:.75rem;
    opacity:.9;
}

/* =========================================
   TABLE
========================================= */

.Table-Wrap{
    overflow:auto;
}

table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 10px;
}

thead th{
    padding:14px;
    font-size:.75rem;
    text-transform:uppercase;
    color:#6b7280;
    font-weight:700;
}

tbody tr{
    background:#fff;
    transition:.3s ease;
}

tbody tr:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,.06);
}

tbody td{
    padding:16px;
    vertical-align:middle;
}

/* =========================================
   TOP RANK ROW
========================================= */

.Row-Gold{
    background:
        linear-gradient(
            90deg,
            rgba(124,58,237,.10),
            rgba(251,191,36,.14)
        );
}

.Row-Silver{
    background:
        linear-gradient(
            90deg,
            rgba(148,163,184,.10),
            rgba(226,232,240,.14)
        );
}

.Row-Bronze{
    background:
        linear-gradient(
            90deg,
            rgba(180,83,9,.10),
            rgba(251,146,60,.12)
        );
}

/* =========================================
   RANK MEDAL
========================================= */

.Rank-Medal{
    width:45px;
    height:45px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    font-size:1rem;
    box-shadow:0 6px 18px rgba(0,0,0,.12);
}

.Medal-1{
    background:linear-gradient(
        135deg,
        #f59e0b,
        #facc15
    );
    color:#fff;
}

.Medal-2{
    background:linear-gradient(
        135deg,
        #94a3b8,
        #e2e8f0
    );
    color:#fff;
}

.Medal-3{
    background:linear-gradient(
        135deg,
        #b45309,
        #fb923c
    );
    color:#fff;
}

.Medal-n{
    background:#f1f5f9;
    color:#334155;
}

/* =========================================
   TEXT
========================================= */

.Td-Code{
    font-weight:700;
    color:#6b7280;
}

.Td-Bold{
    font-weight:800;
    color:#111827;
}

.Td-Center{
    text-align:center;
}

/* =========================================
   SCORE BAR
========================================= */

.Modern-Score{
    display:flex;
    align-items:center;
    gap:12px;
}

.Modern-Bar{
    width:140px;
    height:12px;
    background:#e5e7eb;
    border-radius:999px;
    overflow:hidden;
}

.Modern-Fill{
    height:100%;
    border-radius:999px;
    position:relative;
}

.Modern-Fill::after{
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(
        90deg,
        rgba(255,255,255,.35),
        transparent
    );
}

.Modern-Fill.gold{
    background:linear-gradient(
        90deg,
        #f59e0b,
        #facc15
    );
}

.Modern-Fill.silver{
    background:linear-gradient(
        90deg,
        #94a3b8,
        #e2e8f0
    );
}

.Modern-Fill.bronze{
    background:linear-gradient(
        90deg,
        #b45309,
        #fb923c
    );
}

.Modern-Fill.default{
    background:linear-gradient(
        90deg,
        #7c3aed,
        #a78bfa
    );
}

.Modern-Score-Text{
    font-weight:900;
    color:#111827;
}

/* =========================================
   BADGE
========================================= */

.Badge{
    padding:8px 14px;
    border-radius:999px;
    font-size:.72rem;
    font-weight:700;
}

.Badge.green{
    background:rgba(16,185,129,.12);
    color:#059669;
}

.Badge.red{
    background:rgba(239,68,68,.12);
    color:#dc2626;
}

/* =========================================
   RESPONSIVE
========================================= */

@media(max-width:991px){

    .Stats-Grid{
        grid-template-columns:repeat(2,1fr);
    }

    .Podium{
        grid-template-columns:1fr;
    }

}

@media(max-width:600px){

    .Stats-Grid{
        grid-template-columns:1fr;
    }

}

</style>

{{-- FILTER MODE MODERN --}}
<div class="Card FilterBox">

    <div class="FilterHead">

        <div>

            <h3 class="FilterTitle">
                Mode & Konfigurasi
            </h3>

            <p class="FilterSub">
                Pilih jumlah kriteria yang digunakan
            </p>

        </div>

    </div>

    <form method="GET">

        @if(request('status'))
            <input type="hidden"
                   name="status"
                   value="{{ request('status') }}">
        @endif

        <div class="ModeWrap">

            @foreach($modeOptions as $mode)

                <button type="submit"
                        name="mode"
                        value="{{ $mode }}"
                        class="ModeBtn {{ $modeAktif == $mode ? 'active' : '' }}">

                    {{ $mode }} Kriteria

                </button>

            @endforeach

        </div>

    </form>

</div>

<style>

.FilterBox{
    padding:22px;
    border-radius:28px;
    background:#fff;
    border:1px solid #ececec;
    box-shadow:0 10px 30px rgba(0,0,0,.04);
    margin-bottom:20px;
}

.FilterHead{
    margin-bottom:18px;
}

.FilterTitle{
    font-size:1.3rem;
    font-weight:900;
    color:#111827;
    margin:0;
}

.FilterSub{
    margin-top:4px;
    font-size:.9rem;
    color:#9ca3af;
}

.ModeWrap{
    display:flex;
    gap:10px;
    padding:8px;
    border-radius:22px;
    background:#f8fafc;
    border:1px solid #e5e7eb;
}

.ModeBtn{
    flex:1;
    height:54px;
    border:none;
    border-radius:16px;
    background:transparent;
    font-weight:800;
    font-size:.95rem;
    color:#374151;
    cursor:pointer;
    transition:.3s;
}

.ModeBtn:hover{
    background:rgba(124,58,237,.08);
    color:#7c3aed;
}

.ModeBtn.active{
    background:linear-gradient(135deg,#7c3aed,#9333ea);
    color:#fff;
    box-shadow:0 10px 20px rgba(124,58,237,.25);
}

@media(max-width:768px){

    .ModeWrap{
        flex-direction:column;
    }

}

</style>

{{-- STATS --}}
<div class="Stats-Grid">

    <div class="Stat-Card purple">

        <p class="Stat-Label">
            Total Peserta
        </p>

        <h2 class="Stat-Val">
            {{ $ranking->count() }}
        </h2>

        <p class="Stat-Delta">

            <ion-icon name="people-outline"></ion-icon>

            Semua calon

        </p>

    </div>

    <div class="Stat-Card green">

        <p class="Stat-Label">
            Dinyatakan Layak
        </p>

        <h2 class="Stat-Val">
            {{ $layak }}
        </h2>

        <p class="Stat-Delta">

            <ion-icon name="checkmark-circle-outline"></ion-icon>

            Vi ≥ {{ $threshold }}

        </p>

    </div>

    <div class="Stat-Card orange">

        <p class="Stat-Label">
            Tidak Lolos
        </p>

        <h2 class="Stat-Val">
            {{ $tidakLayak }}
        </h2>

        <p class="Stat-Delta">

            <ion-icon name="close-circle-outline"></ion-icon>

            Vi &lt; {{ $threshold }}

        </p>

    </div>

    <div class="Stat-Card blue">

        <p class="Stat-Label">
            Skor Tertinggi
        </p>

        <h2 class="Stat-Val">

            {{ $tertinggi
                ? number_format($tertinggi->skor_akhir,3)
                : '-'
            }}

        </h2>

        <p class="Stat-Delta">

            <ion-icon name="trophy-outline"></ion-icon>

            {{ $tertinggi
                ? $tertinggi->calonPenerima->nama
                : '-'
            }}

        </p>

    </div>

</div>

@if($ranking->isEmpty())

<div class="Card">

    <div class="Empty-State">

        <ion-icon name="stats-chart-outline"></ion-icon>

        <p>
            Belum ada hasil ranking.
        </p>

    </div>

</div>

@else

{{-- PODIUM --}}
@if($layak >= 3)

<div class="Card">

    <div class="Card-Header">

        <div>

            <p class="Card-Title">
                🏆 Top Ranking
            </p>

        </div>

    </div>

    @php
        $top3 = $ranking
            ->where('status_kelayakan','layak')
            ->take(3)
            ->values();
    @endphp

    <div class="Podium">

        {{-- RANK 2 --}}
        <div>

            <p class="Podium-Name">

                {{ $top3[1]->calonPenerima->nama }}

            </p>

            <div class="Podium-Card silver">

                <div class="Podium-Score">

                    {{ number_format($top3[1]->skor_akhir,3) }}

                </div>

                <div class="Podium-Rank">

                    🥈 Ranking #2

                </div>

            </div>

        </div>

        {{-- RANK 1 --}}
        <div>

            <p class="Podium-Name">

                {{ $top3[0]->calonPenerima->nama }}

            </p>

            <div class="Podium-Card gold">

                <div class="Podium-Score">

                    {{ number_format($top3[0]->skor_akhir,3) }}

                </div>

                <div class="Podium-Rank">

                    🥇 TERBAIK

                </div>

            </div>

        </div>

        {{-- RANK 3 --}}
        <div>

            <p class="Podium-Name">

                {{ $top3[2]->calonPenerima->nama }}

            </p>

            <div class="Podium-Card bronze">

                <div class="Podium-Score">

                    {{ number_format($top3[2]->skor_akhir,3) }}

                </div>

                <div class="Podium-Rank">

                    🥉 Ranking #3

                </div>

            </div>

        </div>

    </div>

</div>

@endif

{{-- TABLE --}}
<div class="Card">

    <div class="Card-Header">

        <div>

            <p class="Card-Title">
                Tabel Ranking Lengkap
            </p>

            <p class="Card-Sub">
                Mode {{ $modeAktif }} Kriteria
            </p>

        </div>

    </div>

    <div class="Table-Wrap">

        <table>

            <thead>

                <tr>

                    <th>Rank</th>
                    <th>Kode</th>
                    <th>Nama</th>

                    @foreach($kriteria->take($modeAktif) as $k)

                        <th class="Td-Center">

                            {{ $k->kode_kriteria }}

                        </th>

                    @endforeach

                    <th>Skor Vi</th>
                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                @foreach($ranking as $r)

                <tr class="
                    {{ $r->ranking == 1 ? 'Row-Gold' : '' }}
                    {{ $r->ranking == 2 ? 'Row-Silver' : '' }}
                    {{ $r->ranking == 3 ? 'Row-Bronze' : '' }}
                ">

                    {{-- RANK --}}
                    <td>

                        @if($r->ranking==1)

                            <span class="Rank-Medal Medal-1">
                                🥇
                            </span>

                        @elseif($r->ranking==2)

                            <span class="Rank-Medal Medal-2">
                                🥈
                            </span>

                        @elseif($r->ranking==3)

                            <span class="Rank-Medal Medal-3">
                                🥉
                            </span>

                        @else

                            <span class="Rank-Medal Medal-n">

                                {{ $r->ranking }}

                            </span>

                        @endif

                    </td>

                    {{-- KODE --}}
                    <td class="Td-Code">

                        {{ $r->calonPenerima->kode_anak }}

                    </td>

                    {{-- NAMA --}}
                    <td class="Td-Bold">

                        {{ $r->calonPenerima->nama }}

                    </td>

                    {{-- NILAI --}}
                    @foreach($kriteria->take($modeAktif) as $k)

                        @php
                            $nilai = $r->calonPenerima->getNilai($k->kode_kriteria);
                        @endphp

                        <td class="Td-Center">

                            {{ $nilai }}

                        </td>

                    @endforeach

                    {{-- SKOR --}}
                    <td>

                        <div class="Modern-Score">

                            <div class="Modern-Bar">

                                <div class="Modern-Fill
                                    {{ $r->ranking == 1 ? 'gold' : '' }}
                                    {{ $r->ranking == 2 ? 'silver' : '' }}
                                    {{ $r->ranking == 3 ? 'bronze' : 'default' }}
                                "
                                    style="width:{{ $r->skor_akhir * 100 }}%">
                                </div>

                            </div>

                            <span class="Modern-Score-Text">

                                {{ number_format($r->skor_akhir,3) }}

                            </span>

                        </div>

                    </td>

                    {{-- STATUS --}}
                    <td>

                        <span class="Badge {{ $r->is_layak ? 'green' : 'red' }}">

                            {{ $r->is_layak ? '✓ Layak' : '✗ Tidak' }}

                        </span>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endif

@endsection