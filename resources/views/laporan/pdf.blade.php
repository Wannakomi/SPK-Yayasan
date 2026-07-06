<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laporan SAW — {{ $periode }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ── SCREEN: Journal-style preview ── */
        * { margin:0;padding:0;box-sizing:border-box;font-family:'Plus Jakarta Sans',sans-serif; }

        body {
            background:#1a1a2e;
            min-height:100vh;
            padding:32px 20px;
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:20px;
        }

        /* Top action bar */
        .action-bar {
            width:100%;max-width:860px;
            display:flex;align-items:center;justify-content:space-between;
            background:rgba(255,255,255,0.07);
            backdrop-filter:blur(12px);
            border:1px solid rgba(255,255,255,0.12);
            border-radius:14px;padding:12px 20px;
        }
        .action-bar-title { font-size:.9rem;font-weight:700;color:#f0eeff; }
        .action-bar-sub   { font-size:.72rem;color:#9090aa;margin-top:2px; }
        .btn-print {
            display:inline-flex;align-items:center;gap:7px;
            padding:9px 20px;border-radius:10px;border:none;cursor:pointer;
            background:linear-gradient(135deg,rgba(201,168,76,0.85),rgba(232,201,109,0.80));
            color:#1a1200;font-size:.85rem;font-weight:800;
            box-shadow:0 3px 14px rgba(201,168,76,0.28);
            transition:all .18s;font-family:'Plus Jakarta Sans',sans-serif;
        }
        .btn-print:hover { transform:translateY(-1px);box-shadow:0 6px 20px rgba(201,168,76,0.40); }
        .btn-back {
            display:inline-flex;align-items:center;gap:6px;
            padding:9px 16px;border-radius:10px;
            background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.14);
            color:#c8c4e8;font-size:.82rem;font-weight:600;cursor:pointer;
            text-decoration:none;transition:all .18s;font-family:'Plus Jakarta Sans',sans-serif;
        }
        .btn-back:hover { background:rgba(255,255,255,0.14); }

        /* Paper / Journal */
        .paper {
            width:100%;max-width:860px;
            background:#fff;
            border-radius:8px;
            box-shadow:0 20px 60px rgba(0,0,0,0.55),0 4px 16px rgba(0,0,0,0.30);
            overflow:hidden;
        }

        /* Journal Header */
        .journal-header {
            background:linear-gradient(135deg,#1a1a2e,#2d2d50);
            padding:32px 40px 24px;
            color:#fff;
            border-bottom:4px solid #c9a84c;
        }
        .journal-logo { display:flex;align-items:center;gap:14px;margin-bottom:18px; }
        .journal-logo-icon {
            width:52px;height:52px;border-radius:12px;
            background:linear-gradient(135deg,rgba(201,168,76,0.30),rgba(201,168,76,0.12));
            display:flex;align-items:center;justify-content:center;font-size:1.6rem;
            border:1px solid rgba(201,168,76,0.35);
        }
        .journal-logo-text h1 { font-size:1.1rem;font-weight:900;color:#fff; }
        .journal-logo-text p  { font-size:.72rem;color:rgba(255,255,255,.6);margin-top:2px; }
        .journal-title {
            font-size:1.35rem;font-weight:900;color:#fff;
            letter-spacing:-.02em;margin-bottom:5px;
        }
        .journal-subtitle { font-size:.82rem;color:rgba(255,255,255,.65); }
        .journal-meta {
            display:flex;gap:20px;margin-top:18px;flex-wrap:wrap;
        }
        .meta-item { display:flex;flex-direction:column;gap:2px; }
        .meta-label { font-size:.62rem;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.5); }
        .meta-val   { font-size:.82rem;font-weight:700;color:#c9a84c; }

        /* Body */
        .journal-body { padding:32px 40px; }

        /* Stat strip */
        .stat-strip {
            display:grid;grid-template-columns:repeat(4,1fr);gap:12px;
            margin-bottom:24px;
        }
        .stat-box {
            padding:14px 16px;border-radius:10px;
            background:#f8f7ff;border:1px solid #e8e4f8;
            text-align:center;
        }
        .stat-box-label { font-size:.65rem;color:#9090aa;text-transform:uppercase;letter-spacing:.06em; }
        .stat-box-val   { font-size:1.6rem;font-weight:900;margin-top:4px; }
        .stat-box-val.gold   { color:#92400e; }
        .stat-box-val.green  { color:#065f46; }
        .stat-box-val.red    { color:#991b1b; }
        .stat-box-val.purple { color:#5b21b6; }

        /* Kriteria pills */
        .kriteria-row {
            display:flex;gap:8px;flex-wrap:wrap;
            margin-bottom:24px;
            padding:14px 16px;
            background:#f8f7ff;border-radius:10px;border:1px solid #e8e4f8;
        }
        .kriteria-pill {
            padding:4px 12px;border-radius:50px;font-size:.72rem;font-weight:700;
        }
        .pill-cost    { background:#fee2e2;color:#991b1b; }
        .pill-benefit { background:#d1fae5;color:#065f46; }

        /* Section title */
        .section-title {
            font-size:.75rem;font-weight:800;text-transform:uppercase;
            letter-spacing:.1em;color:#5b21b6;
            padding-bottom:8px;
            border-bottom:2px solid #e8e4f8;
            margin-bottom:16px;
        }

        /* Table */
        table { width:100%;border-collapse:collapse;margin-bottom:24px;font-size:.78rem; }
        thead { background:#f5f3ff; }
        th {
            padding:9px 12px;text-align:left;
            font-size:.67rem;font-weight:800;text-transform:uppercase;
            letter-spacing:.06em;color:#5b21b6;
            border-bottom:2px solid #e8e4f8;white-space:nowrap;
        }
        th.center, td.center { text-align:center; }
        td { padding:9px 12px;border-bottom:1px solid #f0effe;color:#2d2d44; }
        tr:last-child td { border-bottom:none; }
        tr.layak-row  { background:#fffbeb; }
        tr:hover      { background:#faf9ff; }
        .rank-badge {
            width:26px;height:26px;border-radius:50%;
            display:inline-flex;align-items:center;justify-content:center;
            font-size:.72rem;font-weight:800;
        }
        .r1 { background:linear-gradient(135deg,#c9a84c,#e8c96d);color:#1a1200; }
        .r2 { background:linear-gradient(135deg,#9ca3af,#d1d5db);color:#374151; }
        .r3 { background:linear-gradient(135deg,#b45309,#d97706);color:#fff; }
        .rn { background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb; }
        .kode { font-weight:700;color:#7c3aed;font-family:monospace; }
        .bold { font-weight:700;color:#1a1a2e; }
        .badge-layak { background:#fef9ec;color:#92400e;padding:3px 10px;border-radius:50px;font-size:.68rem;font-weight:700;border:1px solid #f9a825; }
        .badge-tidak { background:#f3f4f6;color:#6b7280;padding:3px 10px;border-radius:50px;font-size:.68rem;font-weight:600; }
        .score-bar-wrap { display:flex;align-items:center;gap:6px; }
        .score-bar-bg   { flex:1;height:5px;background:#f0effe;border-radius:3px;overflow:hidden;min-width:50px; }
        .score-bar-fill { height:5px;border-radius:3px; }

        /* TTD */
        .ttd-section {
            display:grid;grid-template-columns:1fr 1fr;gap:28px;
            margin-top:32px;padding-top:24px;
            border-top:2px solid #f0effe;
        }
        .ttd-box { text-align:center; }
        .ttd-label { font-size:.72rem;color:#6b7280;margin-bottom:8px; }
        .ttd-line  { height:52px;border-bottom:1px solid #9ca3af;margin:10px 0; }
        .ttd-name  { font-size:.72rem;color:#374151; }

        /* Footer */
        .journal-footer {
            background:#f8f7ff;border-top:1px solid #e8e4f8;
            padding:14px 40px;display:flex;justify-content:space-between;align-items:center;
        }
        .journal-footer p { font-size:.66rem;color:#9090aa; }

        /* ── PRINT STYLES ── */
        @media print {
            body { background:#fff;padding:0; }
            .action-bar { display:none!important; }
            .paper { box-shadow:none;border-radius:0;max-width:100%; }
            .journal-header { -webkit-print-color-adjust:exact;print-color-adjust:exact; }
            .stat-box, .kriteria-row { -webkit-print-color-adjust:exact;print-color-adjust:exact; }
            thead { -webkit-print-color-adjust:exact;print-color-adjust:exact; }
            .layak-row { -webkit-print-color-adjust:exact;print-color-adjust:exact; }
            @page { margin:0;size:A4 portrait; }
        }
    </style>
</head>
<body>

{{-- ACTION BAR (hidden on print) --}}
<div class="action-bar">
    <div>
        <p class="action-bar-title">Laporan SAW — {{ $periode }}</p>
        <p class="action-bar-sub">Mode {{ $modeN }} Kriteria · {{ $jenis === 'layak' ? 'Hanya Penerima Layak' : 'Semua Peserta' }}</p>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('laporan.index') }}" class="btn-back">Kembali</a>
        <a href="{{ route('laporan.export-pdf', request()->query()) }}"
            class="btn-print">
            🖨️ Download PDF
        </a>
    </div>
</div>

{{-- PAPER --}}
<div class="paper">

    {{-- Header --}}
    <div class="journal-header">
        <div class="journal-logo">
            <div class="journal-logo-icon" style="padding:4px">
                <img src="{{ asset('images/logo.png') }}" 
                    alt="Logo" 
                    style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
            </div>
            <div class="journal-logo-text">
                <h1>{{ $settings['yayasan_name'] ?? 'Yayasan Sahabat Yatim' }}</h1>
                <p>{{ $settings['yayasan_address'] ?? '' }}</p>
            </div>
        </div>
        <div class="journal-title">Laporan Hasil Seleksi Penerima Dana Pendidikan</div>
        <div class="journal-subtitle">Metode Simple Additive Weighting (SAW) · {{ $jenis === 'layak' ? 'Daftar Penerima Layak' : 'Ranking Lengkap' }}</div>
        <div class="journal-meta">
            <div class="meta-item"><span class="meta-label">Periode</span><span class="meta-val">{{ $periode }}</span></div>
            <div class="meta-item"><span class="meta-label">Tanggal Cetak</span><span class="meta-val">{{ now()->format('d F Y') }}</span></div>
            <div class="meta-item"><span class="meta-label">Total Peserta</span><span class="meta-val">{{ $ranking->count() }}</span></div>
            <div class="meta-item"><span class="meta-label">Mode Hitung</span><span class="meta-val">{{ $modeN }} Kriteria</span></div>
        </div>
    </div>

    {{-- Body --}}
    <div class="journal-body">

        {{-- Stat strip --}}
        <div class="stat-strip">
            <div class="stat-box">
                <p class="stat-box-label">Total Peserta</p>
                <p class="stat-box-val purple">{{ $ranking->count() }}</p>
            </div>
            <div class="stat-box">
                <p class="stat-box-label">Dinyatakan Layak</p>
                <p class="stat-box-val green">{{ $ranking->where('status_kelayakan','layak')->count() }}</p>
            </div>
            <div class="stat-box">
                <p class="stat-box-label">Tidak Lolos</p>
                <p class="stat-box-val red">{{ $ranking->where('status_kelayakan','tidak_layak')->count() }}</p>
            </div>
            <div class="stat-box">
                <p class="stat-box-label">Skor Tertinggi</p>
                <p class="stat-box-val gold">{{ $ranking->first() ? number_format($ranking->first()->skor_akhir,4) : '—' }}</p>
            </div>
        </div>

        {{-- Kriteria --}}
        <div class="kriteria-row">
            <span style="font-size:.72rem;font-weight:700;color:#374151;margin-right:4px">Parameter:</span>
            @foreach($kriteriaFilter as $k)
            <span class="kriteria-pill {{ $k->atribut==='cost'?'pill-cost':'pill-benefit' }}">
                {{ $k->kode_kriteria }} — {{ $k->nama_kriteria }} ({{ $k->bobot_persen }})
            </span>
            @endforeach
            <span style="font-size:.68rem;color:#9090aa;margin-left:8px">Threshold: ≥ {{ \App\Models\Setting::get('threshold_layak','0.75') }}</span>
        </div>

        {{-- Tabel Ranking --}}
        <div class="section-title">Hasil Ranking SAW</div>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Kode</th>
                    <th>Nama Lengkap</th>
                    @foreach($kriteriaFilter as $k)
                    <th class="center">{{ $k->kode_kriteria }}</th>
                    @endforeach
                    <th class="center">Skor Vi</th>
                    <th class="center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ranking as $r)
                <tr class="{{ $r->is_layak?'layak-row':'' }}">
                    <td>
                        <span class="rank-badge {{ $r->ranking==1?'r1':($r->ranking==2?'r2':($r->ranking==3?'r3':'rn')) }}">
                            {{ $r->ranking }}
                        </span>
                    </td>
                    <td class="kode">{{ $r->calonPenerima->kode_anak }}</td>
                    <td class="bold">{{ $r->calonPenerima->nama }}</td>
                    @foreach($kriteriaFilter as $k)
                    <td class="center">
                        {{ $r->calonPenerima->getNilai($k->kode_kriteria) !== null
                            ? number_format((float)$r->calonPenerima->getNilai($k->kode_kriteria), 2)
                            : '—' }}
                    </td>
                    @endforeach
                    <td class="center">
                        <div class="score-bar-wrap">
                            <div class="score-bar-bg">
                                <div class="score-bar-fill" style="width:{{ $r->skor_akhir*100 }}%;background:{{ $r->is_layak?'#c9a84c':'#9ca3af' }}"></div>
                            </div>
                            <strong style="color:{{ $r->is_layak?'#92400e':'#6b7280' }};min-width:50px">{{ number_format($r->skor_akhir,4) }}</strong>
                        </div>
                    </td>
                    <td class="center">
                        <span class="{{ $r->is_layak?'badge-layak':'badge-tidak' }}">
                            {{ $r->is_layak ? '✓ LAYAK' : 'TIDAK' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ 4 + $kriteriaFilter->count() }}" style="text-align:center;padding:24px;color:#9090aa">Belum ada data penilaian.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- TTD --}}
        <div class="ttd-section">
            <div class="ttd-box">
                <p class="ttd-label">Mengetahui,<br>Ketua Yayasan {{ $settings['yayasan_name'] ?? '' }}</p>
                <div class="ttd-line"></div>
                <p class="ttd-name">( __________________________ )</p>
            </div>
            <div class="ttd-box">
                <p class="ttd-label">Dibuat oleh,<br>Admin / Pengurus Yayasan</p>
                <div class="ttd-line"></div>
                <p class="ttd-name">( __________________________ )</p>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="journal-footer">
        <p>Dokumen ini dicetak melalui Sistem Butterflies SPK SAW</p>
        <p>{{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>

</div>

</body>
</html>
