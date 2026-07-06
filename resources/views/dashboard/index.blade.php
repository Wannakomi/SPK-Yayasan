@extends('layouts.app')

@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-sub','Selamat datang - Periode '.$periode)

@section('content')

@php
    $mode = request('mode', $kriteria->count());
@endphp

{{-- STAT CARDS --}}
<div class="Stats-Grid">

    <div class="Stat-Card purple">
        <div class="Stat-Info">
            <p class="Stat-Label">Total Calon</p>
            <h2 class="Stat-Val">{{ $totalCalon }}</h2>
            <p class="Stat-Delta gray"><ion-icon name="people-outline"></ion-icon>{{ $mode }} kriteria aktif</p>
        </div>

        <div class="Stat-Ring">
            <svg viewBox="0 0 36 36">
                <circle class="ring-bg" cx="18" cy="18" r="15.9"/>
                <circle
                    class="ring-fg"
                    cx="18"
                    cy="18"
                    r="15.9"
                    stroke="#a78bfa"
                    stroke-dasharray="{{ $totalCalon * 10 }} 100"
                />
            </svg>
            <span>{{ $totalCalon }}</span>
        </div>
    </div>

    <div class="Stat-Card orange">
        <div class="Stat-Info">
            <p class="Stat-Label">Sudah Dinilai</p>
            <h2 class="Stat-Val">{{ $dinilai }}</h2>
            <p class="Stat-Delta {{ $dinilai == $totalCalon ? 'up' : 'gray' }}"><ion-icon name="checkmark-circle-outline"></ion-icon>{{ $totalCalon > 0 ? round($dinilai / $totalCalon * 100) : 0 }}% lengkap</p>
        </div>

        <div class="Stat-Ring">
            <svg viewBox="0 0 36 36">
                <circle class="ring-bg" cx="18" cy="18" r="15.9"/>

                <circle
                    class="ring-fg"
                    cx="18"
                    cy="18"
                    r="15.9"
                    stroke="#fb923c"
                    stroke-dasharray="{{ $totalCalon > 0 ? round($dinilai / $totalCalon * 100) : 0 }} 100"
                />
            </svg>

            <span>
                {{ $totalCalon > 0 ? round($dinilai / $totalCalon * 100) : 0 }}%
            </span>
        </div>
    </div>

    <div class="Stat-Card green">
        <div class="Stat-Info">
            <p class="Stat-Label">Dinyatakan Layak</p>
            <h2 class="Stat-Val">{{ $layak }}</h2>
            <p class="Stat-Delta down"><ion-icon name="people-outline"></ion-icon>{{ $tidakLayak }} tidak layak</p>
        </div>

        <div class="Stat-Ring">
            <svg viewBox="0 0 36 36">
                <circle class="ring-bg" cx="18" cy="18" r="15.9"/>

                <circle
                    class="ring-fg"
                    cx="18"
                    cy="18"
                    r="15.9"
                    stroke="#34d399"
                    stroke-dasharray="{{ $dinilai > 0 ? round($layak / $dinilai * 100) : 0 }} 100"
                />
            </svg>

            <span>
                {{ $dinilai > 0 ? round($layak / $dinilai * 100) : 0 }}%
            </span>
        </div>
    </div>

</div>

{{-- CHARTS --}}
<div class="Charts-Row">

    {{-- BAR CHART --}}
    <div class="Chart-Card">
        <div class="Chart-Header">
            <div><h3>Distribusi Skor SAW</h3><p>Mode {{ $mode }} kriteria — {{ $periode }}</p></div>
            <div style="display:flex;align-items:center;gap:10px">

                {{-- MODE --}}
                <div class="Mode-Switch">

                    @foreach(range(1, $kriteria->count()) as $m)

                        <a
                            href="{{ request()->fullUrlWithQuery(['mode' => $m]) }}"
                            class="Mode-Chip {{ $mode == $m ? 'active' : '' }}"
                        >
                            {{ $m }}
                        </a>

                    @endforeach

                </div>

                {{-- PAGINATION CHART --}}
                @if($skorChart->isNotEmpty())

                    <div style="display:flex;align-items:center;gap:6px">
                        @if($chartPage > 1)
                            <a
                                href="{{ request()->fullUrlWithQuery([
                                    'page_chart' => $chartPage - 1
                                ]) }}"
                                class="Btn Btn-Secondary Btn-Sm"
                                style="padding:4px 10px"
                            >
                                <ion-icon name="chevron-back-outline"></ion-icon>
                            </a>

                        @else

                            <button
                                class="Btn Btn-Secondary Btn-Sm"
                                style="padding:4px 10px;opacity:.35"
                                disabled
                            >
                                <ion-icon name="chevron-back-outline"></ion-icon>
                            </button>

                        @endif

                        <span
                            style="
                                font-size:.72rem;
                                color:var(--text-4);
                                font-weight:600;
                                white-space:nowrap
                            "
                        >
                            {{ $chartPage }} / {{ $chartPages }}
                        </span>

                        @if($chartPage < $chartPages)

                            <a
                                href="{{ request()->fullUrlWithQuery([
                                    'page_chart' => $chartPage + 1
                                ]) }}"
                                class="Btn Btn-Secondary Btn-Sm"
                                style="padding:4px 10px"
                            >
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </a>

                        @else

                            <button
                                class="Btn Btn-Secondary Btn-Sm"
                                style="padding:4px 10px;opacity:.35"
                                disabled
                            >
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </button>

                        @endif

                    </div>

                @endif

            </div>

        </div>

        @if($skorChart->isEmpty())

            <div class="Empty-State">
                <ion-icon name="bar-chart-outline"></ion-icon>
                <p>Belum ada data SAW untuk mode ini.</p>
            </div>

        @else

            <div class="Bar-Chart">

                <div class="Bar-Y-Labels">
                    <span>1.0</span>
                    <span>0.8</span>
                    <span>0.6</span>
                    <span>0.4</span>
                    <span>0.2</span>
                </div>

                <div class="Bar-Groups" id="bar-area"></div>

            </div>

            <div class="Chart-Legend">

                <div class="Legend-Item">
                    <span class="Legend-Dot purple"></span>
                    Layak
                </div>

                <div class="Legend-Item">
                    <span class="Legend-Dot orange"></span>
                    Tidak Layak
                </div>

            </div>

        @endif

    </div>

    {{-- BOBOT --}}
    <div class="Chart-Card">

        <div class="Chart-Header">
            <div>
                <h3>Bobot Kriteria</h3>
                <p>Komposisi penilaian SAW</p>
            </div>
        </div>

        <div class="Acq-List">

            @php
                $chartColors = [
                    '#a78bfa',
                    '#34d399',
                    '#60a5fa',
                    '#fb923c',
                    '#f472b6',
                    '#facc15'
                ];
            @endphp

            @foreach($kriteria->take($mode) as $ki => $k)

                @php
                    $c = $chartColors[$ki % count($chartColors)];
                @endphp

                <div class="Acq-Row">

                    <div class="Acq-Label">
                        <span
                            class="Acq-Dot"
                            style="background:{{ $c }}"
                        ></span>

                        {{ $k->kode_kriteria }}
                        —
                        {{ $k->nama_kriteria }}
                    </div>

                    <div class="Acq-Bar-Wrap">

                        <div
                            class="Acq-Bar"
                            style="
                                width:{{ $k->bobot * 100 }}%;
                                background:{{ $c }}
                            "
                        ></div>

                    </div>

                    <span class="Acq-Val">
                        {{ $k->bobot * 100 }}%
                    </span>

                </div>

            @endforeach

        </div>

    </div>

</div>

{{-- TABEL --}}
<div class="Card">

    <div class="Card-Header">

        <div>
            <p class="Card-Title">Data Calon Penerima</p>
            <p class="Card-Sub">
                {{ $totalCalon }} data —
                mode {{ $mode }} kriteria
            </p>
        </div>

        <div class="Btn-Group">

            <a
                href="{{ route('saw.index') }}"
                class="Btn Btn-Orange Btn-Sm"
            >
                <ion-icon name="calculator-outline"></ion-icon>
                Proses SAW
            </a>

            <a
                href="{{ route('calon-penerima.index') }}"
                class="Btn Btn-Secondary Btn-Sm"
            >
                <ion-icon name="people-outline"></ion-icon>
                Lihat Semua
            </a>

        </div>

    </div>

    <div class="Table-Wrap">

        <table>

            <thead>

                <tr>

                    <th>Kode</th>

                    <th>Nama</th>

                    @foreach($kriteria->take($mode) as $k)

                        <th class="Td-Center">
                            {{ $k->kode_kriteria }}
                        </th>

                    @endforeach

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                @forelse($semuaCalon as $c)

                    <tr>

                        <td class="Td-Code">
                            {{ $c->kode_anak }}
                        </td>

                        <td class="Td-Bold">
                            {{ $c->nama }}
                        </td>

                        @foreach($kriteria->take($mode) as $k)

                            @php
                                $val = $c->getNilai($k->kode_kriteria);
                            @endphp

                            <td class="Td-Center">

                                @if($val !== null)

                                    <span style="font-weight:600">

                                        {{ is_float($val)
                                            ? number_format($val,1)
                                            : $val }}

                                    </span>

                                @else

                                    <span
                                        style="
                                            color:var(--text-4);
                                            font-size:.75rem
                                        "
                                    >
                                        —
                                    </span>

                                @endif

                            </td>

                        @endforeach

                        <td>

                            @if($c->penilaianAktif)

                                @if($c->penilaianAktif->status_kelayakan === 'layak')

                                    <span class="Badge green">
                                        ✓ Layak
                                    </span>

                                @else

                                    <span class="Badge red">
                                        ✗ Tidak
                                    </span>

                                @endif

                            @else

                                <span class="Badge gray">
                                    Belum dinilai
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="{{ 2 + $mode + 1 }}">

                            <div class="Empty-State">

                                <ion-icon name="people-outline"></ion-icon>

                                <p>
                                    Belum ada data calon.
                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div class="Pagination-Custom">

        <div class="Page-Info">

            {{ $semuaCalon->firstItem() }}
            -
            {{ $semuaCalon->lastItem() }}

            dari

            {{ $semuaCalon->total() }}

            data

        </div>

        <div class="Pagination-Buttons">

            {{-- PREV --}}
            @if($semuaCalon->onFirstPage())

                <button disabled>
                    ‹
                </button>

            @else

                <a
                    href="{{ $semuaCalon
                        ->appends([
                            'mode' => request('mode')
                        ])
                        ->previousPageUrl() }}"
                >
                    ‹
                </a>

            @endif

            {{-- PAGE --}}
            @for($i = 1; $i <= $semuaCalon->lastPage(); $i++)

                <a
                    href="{{ $semuaCalon
                        ->appends([
                            'mode' => request('mode')
                        ])
                        ->url($i) }}"

                    class="{{ $semuaCalon->currentPage() == $i ? 'active' : '' }}"
                >
                    {{ $i }}
                </a>

            @endfor

            {{-- NEXT --}}
            @if($semuaCalon->hasMorePages())

                <a
                    href="{{ $semuaCalon
                        ->appends([
                            'mode' => request('mode')
                        ])
                        ->nextPageUrl() }}"
                >
                    ›
                </a>

            @else

                <button disabled>
                    ›
                </button>

            @endif

        </div>

    </div>

</div>

@endsection

@push('styles')
<style>

.Pagination-Custom{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 22px;
    gap:12px;
    flex-wrap:wrap;
}

.Page-Info{
    font-size:.82rem;
    color:var(--text-4);
    font-weight:500;
}

.Pagination-Buttons{
    display:flex;
    align-items:center;
    gap:6px;
}

.Pagination-Buttons a,
.Pagination-Buttons button{
    width:34px;
    height:34px;
    border:none;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    background:#f3f4f6;
    color:#6b7280;
    font-weight:600;
    transition:.2s;
}

.Pagination-Buttons a:hover{
    background:#ede9fe;
    color:#7c3aed;
}

.Pagination-Buttons a.active{
    background:#7c3aed;
    color:#fff;
}

.Pagination-Buttons button:disabled{
    opacity:.4;
    cursor:not-allowed;
}

.Bar-Group{
    position:relative;
}

.Bar-Tooltip{
    position:absolute;
    bottom:calc(100% + 8px);
    left:50%;
    transform:translateX(-50%) translateY(4px);
    background:#1e1b2e;
    color:#f1f0f5;
    font-size:.7rem;
    font-weight:600;
    white-space:nowrap;
    padding:4px 8px;
    border-radius:6px;
    pointer-events:none;
    opacity:0;
    transition:opacity .18s ease, transform .18s ease;
    box-shadow:0 4px 12px rgba(0,0,0,.25);
    z-index:10;
}

.Bar-Tooltip::after{
    content:'';
    position:absolute;
    top:100%;
    left:50%;
    transform:translateX(-50%);
    border:5px solid transparent;
    border-top-color:#1e1b2e;
}

.Bar-Group:hover .Bar-Tooltip{
    opacity:1;
    transform:translateX(-50%) translateY(0);
}

.Bar-Group:hover .Bar-Col{
    filter:brightness(1.2);
    transform:scaleY(1.04);
    transform-origin:bottom;
}

.Bar-Col{
    transition:filter .18s ease, transform .18s ease;
}

.Mode-Switch{
    display:flex;
    gap:8px;
    padding:6px;
    background:#f5f3ff;
    border:1px solid #ede9fe;
    border-radius:14px;
}

.Mode-Chip{
    min-width:38px;
    height:38px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    font-size:.82rem;
    font-weight:700;
    color:#6b7280;
    border-radius:10px;
    transition:.2s ease;
}

.Mode-Chip:hover{
    background:#ede9fe;
    color:#7c3aed;
}

.Mode-Chip.active{
    background:linear-gradient(
        135deg,
        #7c3aed,
        #a855f7
    );
    color:#fff;
    box-shadow:
        0 6px 18px rgba(124,58,237,.25);
}

</style>
@endpush

@push('scripts')
<script>

const chartData = @json(
    $skorChart->map(fn($p)=>[
        'nama'   => $p->calonPenerima->nama,
        'skor'   => $p->skor_akhir,
        'layak'  => $p->status_kelayakan === 'layak'
    ])
);

const area = document.getElementById('bar-area');

if(area && chartData.length){

    area.innerHTML = chartData.map((d,i)=>{

        const h = Math.round(d.skor * 130);

        const c = d.layak
            ? '#7c3aed'
            : '#f97316';

        const SKIP = [
            'muhammad',
            'muhamad',
            'mochamad',
            'mohammad',
            'mohamad',
            'mochammad',
            'muhammadiyah'
        ];

        const parts = d.nama.trim().split(/\s+/);

        const nama =
            (SKIP.includes(parts[0].toLowerCase()) && parts.length > 1
                ? parts[1]
                : parts[0]
            ).toUpperCase();

        const skor = d.skor.toFixed(3);

        return `
        <div class="Bar-Group">

            <div class="Bar-Tooltip">
                ${nama}: ${skor}
            </div>

            <div
                class="Bar-Col"
                style="
                    height:${h}px;
                    background:linear-gradient(
                        180deg,
                        ${c},
                        ${c}88
                    );
                    animation-delay:${i * 0.06}s
                "
            ></div>

            <span class="Bar-Group-Label">
                ${nama}
            </span>

        </div>
        `;

    }).join('');
}

</script>
@endpush