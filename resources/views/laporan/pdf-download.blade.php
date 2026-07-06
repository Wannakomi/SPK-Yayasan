<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan SAW — {{ $periode }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #1a1a2e;
            background: #fff;
        }

        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }

        /* ── HEADER ── */
        .header {
            background: #1a1a2e;
            color: #fff;
            padding: 12px 16px;
            margin-bottom: 10px;
            border-bottom: 3px solid #c9a84c;
            border-radius: 4px;
        }
        .header-logo-row {
            width: 100%;
            margin-bottom: 8px;
        }
        .header-logo-row td {
            vertical-align: middle;
        }
        .org-name {
            font-size: 11pt;
            font-weight: bold;
            color: #fff;
        }
        .org-address {
            font-size: 7.5pt;
            color: rgba(255,255,255,0.6);
            margin-top: 2px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            color: #fff;
            margin-bottom: 2px;
        }
        .doc-subtitle {
            font-size: 8pt;
            color: rgba(255,255,255,0.65);
            margin-bottom: 8px;
        }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td {
            font-size: 7pt;
            padding: 0 16px 0 0;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            width: 25%;
        }
        .meta-table td span {
            display: block;
            font-size: 8.5pt;
            font-weight: bold;
            color: #c9a84c;
            text-transform: none;
            letter-spacing: 0;
            margin-top: 1px;
        }

        /* ── STAT STRIP ── */
        .stat-strip {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .stat-cell {
            width: 25%;
            background: #f8f7ff;
            border: 1.5px solid #e0daf5;
            padding: 8px 10px;
            text-align: center;
        }
        .stat-label { font-size: 6.5pt; color: #9090aa; text-transform: uppercase; letter-spacing: 0.06em; }
        .stat-val   { font-size: 18pt; font-weight: bold; margin-top: 3px; line-height: 1; }
        .gold   { color: #92400e; }
        .green  { color: #065f46; }
        .red    { color: #991b1b; }
        .purple { color: #5b21b6; }

        /* ── KRITERIA BOX ── */
        .kriteria-box {
            background: #f8f7ff;
            border: 1.5px solid #e0daf5;
            padding: 7px 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            font-size: 7.5pt;
            line-height: 1.8;
        }
        .kriteria-label { font-weight: bold; color: #374151; }
        .pill {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 20px;
            font-size: 7pt;
            font-weight: bold;
            margin: 1px 2px;
        }
        .pill-benefit { background: #d1fae5; color: #065f46; }
        .pill-cost    { background: #fee2e2; color: #991b1b; }
        .threshold    { font-size: 7pt; color: #9090aa; margin-left: 4px; }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #5b21b6;
            border-bottom: 2px solid #e8e4f8;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        /* ── TABLE ── */
        table.ranking {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 16px;
        }
        table.ranking thead tr { background: #f0effe; }
        table.ranking th {
            padding: 7px 10px;
            font-size: 7.5pt;
            font-weight: bold;
            color: #1a1a2e;
            border: 1px solid #c8c4e8;
            text-align: center;
            background: #f0effe;
        }
        table.ranking th.left,
        table.ranking td.left { text-align: left; }
        table.ranking td {
            padding: 5px 10px;
            border: 1px solid #d8d4f0;
            color: #2d2d44;
            text-align: center;
            vertical-align: middle;
        }
        table.ranking tr.layak { background: #fffbeb; }
        table.ranking tr:nth-child(even) { background: #faf9ff; }
        table.ranking tr.layak:nth-child(even) { background: #fff8e0; }
        .bold  { font-weight: bold; color: #1a1a2e; }
        .kode  { font-weight: bold; color: #5b21b6; }
        .score { font-weight: bold; color: #92400e; }
        .badge-layak {
            background: #fef9ec;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            border: 1px solid #f9a825;
        }
        .badge-tidak {
            background: #f3f4f6;
            color: #6b7280;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 6.5pt;
        }

        /* ── TTD ── */
        .ttd-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1.5px solid #e8e4f8;
        }
        .ttd-wrap td {
            width: 50%;
            text-align: center;
            padding: 0 30px;
            font-size: 8pt;
            color: #374151;
            vertical-align: top;
        }
        .ttd-line {
            height: 44px;
            border-bottom: 1px solid #9ca3af;
            margin: 6px 20px;
        }
        .ttd-name { font-size: 7.5pt; color: #374151; margin-top: 4px; }

        /* ── FOOTER ── */
        .footer {
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #e8e4f8;
        }
        .footer-inner { width: 100%; border-collapse: collapse; }
        .footer-inner td { font-size: 7pt; color: #9090aa; padding: 0; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <table class="header-logo-row" style="border-collapse:collapse;margin-bottom:8px">
            <tr>
                <td style="width:50px;vertical-align:middle">
                    <img src="{{ public_path('images/logo.png') }}"
                         style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                </td>
                <td style="vertical-align:middle">
                    <div class="org-name">{{ $settings['yayasan_name'] ?? 'Yayasan Sahabat Yatim' }}</div>
                    <div class="org-address">{{ $settings['yayasan_address'] ?? '' }}</div>
                </td>
            </tr>
        </table>
        <div class="doc-title">Laporan Hasil Seleksi Penerima Dana Pendidikan</div>
        <div class="doc-subtitle">Metode Simple Additive Weighting (SAW) &mdash; {{ $jenis === 'layak' ? 'Daftar Penerima Layak' : 'Ranking Lengkap' }}</div>
        <table class="meta-table">
            <tr>
                <td>Periode<span>{{ $periode }}</span></td>
                <td>Tanggal Cetak<span>{{ now()->format('d F Y') }}</span></td>
                <td>Total Peserta<span>{{ $ranking->count() }}</span></td>
                <td>Mode Hitung<span>{{ $modeN }} Kriteria</span></td>
            </tr>
        </table>
    </div>

    {{-- STAT STRIP --}}
    <table class="stat-strip">
        <tr>
            <td class="stat-cell">
                <div class="stat-label">Total Peserta</div>
                <div class="stat-val purple">{{ $ranking->count() }}</div>
            </td>
            <td class="stat-cell" style="border-left:none">
                <div class="stat-label">Dinyatakan Layak</div>
                <div class="stat-val green">{{ $ranking->where('status_kelayakan','layak')->count() }}</div>
            </td>
            <td class="stat-cell" style="border-left:none">
                <div class="stat-label">Tidak Lolos</div>
                <div class="stat-val red">{{ $ranking->where('status_kelayakan','tidak_layak')->count() }}</div>
            </td>
            <td class="stat-cell" style="border-left:none">
                <div class="stat-label">Skor Tertinggi</div>
                <div class="stat-val gold">{{ $ranking->first() ? number_format($ranking->first()->skor_akhir,4) : '—' }}</div>
            </td>
        </tr>
    </table>

    {{-- KRITERIA --}}
    <div class="kriteria-box">
        <span class="kriteria-label">Parameter: </span>
        @foreach($kriteriaFilter as $k)
            <span class="pill {{ $k->atribut === 'cost' ? 'pill-cost' : 'pill-benefit' }}">
                {{ $k->kode_kriteria }} — {{ $k->nama_kriteria }} ({{ $k->bobot_persen }})
            </span>
        @endforeach
        <span class="threshold">&nbsp;Threshold: &ge; {{ \App\Models\Setting::get('threshold_layak','0.75') }}</span>
    </div>

    {{-- TABEL RANKING --}}
    <div class="section-title">Hasil Ranking SAW</div>
    <table class="ranking">
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th style="width:50px">Kode</th>
                <th class="left">Nama Lengkap</th>
                @foreach($kriteriaFilter as $k)
                    <th style="width:44px">{{ $k->kode_kriteria }}</th>
                @endforeach
                <th style="width:60px">Skor Vi</th>
                <th style="width:54px">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ranking as $r)
            <tr class="{{ $r->is_layak ? 'layak' : '' }}">
                <td>{{ $r->ranking }}</td>
                <td class="kode">{{ $r->calonPenerima->kode_anak }}</td>
                <td class="left bold">{{ $r->calonPenerima->nama }}</td>
                @foreach($kriteriaFilter as $k)
                <td>
                    {{ $r->calonPenerima->getNilai($k->kode_kriteria) !== null
                        ? number_format((float)$r->calonPenerima->getNilai($k->kode_kriteria), 2)
                        : '—' }}
                </td>
                @endforeach
                <td><span class="score">{{ number_format($r->skor_akhir, 4) }}</span></td>
                <td>
                    <span class="{{ $r->is_layak ? 'badge-layak' : 'badge-tidak' }}">
                        {{ $r->is_layak ? 'LAYAK' : 'TIDAK' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 4 + $kriteriaFilter->count() }}"
                    style="text-align:center;padding:20px;color:#9090aa">
                    Belum ada data penilaian.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TTD --}}
    <table class="ttd-wrap">
        <tr>
            <td>
                <div>Mengetahui,<br>Ketua Yayasan {{ $settings['yayasan_name'] ?? '' }}</div>
                <div class="ttd-line"></div>
                <div class="ttd-name">( __________________________ )</div>
            </td>
            <td>
                <div>Dibuat oleh,<br>Admin / Pengurus Yayasan</div>
                <div class="ttd-line"></div>
                <div class="ttd-name">( __________________________ )</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <table class="footer-inner">
            <tr>
                <td>Dokumen ini dicetak melalui Sistem Butterflies SPK SAW</td>
                <td style="text-align:right">{{ now()->format('d/m/Y H:i') }} WIB</td>
            </tr>
        </table>
    </div>

</body>
</html>