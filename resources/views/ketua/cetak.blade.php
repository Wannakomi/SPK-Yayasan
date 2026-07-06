<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Resmi — {{ $periode }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
            padding: 0;
        }
        .page {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm 20mm 15mm 25mm;
        }

        /* ── KOP SURAT ── */
        .kop {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 10px;
            border-bottom: 3px solid #000;
            margin-bottom: 4px;
        }
        .kop img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 3px;
        }
        .kop-text p { font-size: 10pt; color: #333; }
        .kop-sub {
            border-bottom: 1.5px solid #000;
            margin-bottom: 16px;
            padding-bottom: 4px;
        }

        /* ── JUDUL SURAT ── */
        .judul-surat {
            text-align: center;
            margin: 16px 0 12px;
        }
        .judul-surat h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: .04em;
        }
        .judul-surat p { font-size: 11pt; margin-top: 3px; }

        /* ── INFO SURAT ── */
        .info-surat {
            margin-bottom: 14px;
            font-size: 11pt;
        }
        .info-surat table { width: auto; border-collapse: collapse; }
        .info-surat td { padding: 2px 8px 2px 0; vertical-align: top; }
        .info-surat td:first-child { white-space: nowrap; }

        /* ── PEMBUKA ── */
        .pembuka {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 14px;
            line-height: 1.6;
        }

        /* ── TABEL ── */
        table.ranking {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10pt;
        }
        table.ranking th {
            padding: 6px 8px;
            background: #f0f0f0;
            font-weight: bold;
            border: 1px solid #000;
            text-align: center;
            font-size: 10pt;
        }
        table.ranking td {
            padding: 5px 8px;
            border: 1px solid #000;
            vertical-align: middle;
            font-size: 10pt;
        }
        table.ranking tr:nth-child(even) { background: #f9f9f9; }
        .layak-row { background: #f5f5f5 !important; }
        .bold { font-weight: bold; }
        .center { text-align: center; }

        /* ── PENUTUP ── */
        .penutup {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        /* ── TTD ── */
        .ttd-wrap {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }
        .ttd-box { text-align: center; width: 45%; font-size: 11pt; }
        .ttd-space { height: 60px; }
        .ttd-name { font-weight: bold; border-top: 1px solid #000; padding-top: 4px; display: inline-block; min-width: 160px; }

        /* ── FOOTER ── */
        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }

        /* ── PRINT ── */
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { size: A4 portrait; margin: 20mm 20mm 15mm 25mm; }
            .page { padding: 0; max-width: 100%; }
        }

        /* ── ACTION BAR ── */
        .action-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #1a1a2e;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 999;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .action-bar p { color: #c9a84c; font-family: sans-serif; font-size: .85rem; font-weight: 700; }
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            background: #c9a84c;
            color: #1a1200;
            border: none;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 800;
            cursor: pointer;
            font-family: sans-serif;
            text-decoration: none;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            font-family: sans-serif;
            text-decoration: none;
        }
        .page-wrap { margin-top: 52px; }
    </style>
</head>
<body>

{{-- ACTION BAR --}}
<div class="action-bar no-print">
    <p>Preview Laporan Resmi — {{ $periode }}</p>
    <div style="display:flex;gap:10px">
        <a href="{{ route('ketua.laporan') }}" class="btn-back">Kembali</a>
        <a href="{{ route('ketua.download-pdf') }}" class="btn-download">Download PDF</a>
    </div>
</div>

<div class="page-wrap">
<div class="page">

    {{-- KOP SURAT --}}
    <div class="kop">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
        <div class="kop-text">
            <h1>{{ $settings['yayasan_name'] ?? 'Yayasan Sahabat Yatim' }}</h1>
            <p>{{ $settings['yayasan_address'] ?? '' }}</p>
            <p>Telp: {{ $settings['yayasan_phone'] ?? '-' }}</p>
        </div>
    </div>
    <div class="kop-sub"></div>

    {{-- JUDUL --}}
    <div class="judul-surat">
        <h2>Laporan Hasil Seleksi Penerima Dana Pendidikan</h2>
        <p>Metode Simple Additive Weighting (SAW) &mdash; Periode {{ $periode }}</p>
    </div>

    {{-- INFO --}}
    <div class="info-surat">
        <table>
            <tr><td>Tanggal</td><td>:</td><td>{{ now()->format('d F Y') }}</td></tr>
            <tr><td>Periode</td><td>:</td><td>{{ $periode }}</td></tr>
            <tr><td>Total Peserta</td><td>:</td><td>{{ $ranking->count() }} Calon</td></tr>
            <tr><td>Dinyatakan Layak</td><td>:</td><td>{{ $ranking->where('status_kelayakan','layak')->count() }} Calon</td></tr>
            <tr><td>Tidak Lolos</td><td>:</td><td>{{ $ranking->where('status_kelayakan','tidak_layak')->count() }} Calon</td></tr>
            <tr><td>Threshold</td><td>:</td><td>Vi &ge; {{ \App\Models\Setting::get('threshold_layak','0.75') }}</td></tr>
        </table>
    </div>

    {{-- PEMBUKA --}}
    <div class="pembuka">
        <p>Berdasarkan hasil perhitungan menggunakan metode <em>Simple Additive Weighting</em> (SAW) dengan kriteria
        @foreach($kriteria as $k){{ $k->kode_kriteria }} ({{ $k->nama_kriteria }}, bobot {{ $k->bobot*100 }}%){{ !$loop->last ? ', ' : '' }}@endforeach,
        berikut adalah hasil seleksi calon penerima dana pendidikan periode {{ $periode }}:</p>
    </div>

    {{-- TABEL RANKING --}}
    <table class="ranking">
        <thead>
            <tr>
                <th style="width:36px">No</th>
                <th style="width:50px">Kode</th>
                <th>Nama Lengkap</th>
                <th>Jenjang</th>
                @foreach($kriteria as $k)
                <th style="width:50px">{{ $k->kode_kriteria }}</th>
                @endforeach
                <th style="width:60px">Skor</th>
                <th style="width:60px">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ranking as $r)
            <tr class="{{ $r->is_layak ? 'layak-row' : '' }}">
                <td class="center">{{ $r->ranking }}</td>
                <td class="center bold">{{ $r->calonPenerima->kode_anak }}</td>
                <td class="bold">{{ $r->calonPenerima->nama }}</td>
                <td>{{ $r->calonPenerima->jenjang }} {{ $r->calonPenerima->kelas }}</td>
                @foreach($kriteria as $k)
                <td class="center">
                    {{ $r->calonPenerima->getNilai($k->kode_kriteria) !== null
                        ? number_format((float)$r->calonPenerima->getNilai($k->kode_kriteria), 2)
                        : '—' }}
                </td>
                @endforeach
                <td class="center bold">{{ number_format($r->skor_akhir, 4) }}</td>
                <td class="center">{{ $r->is_layak ? 'Layak' : 'Tidak Layak' }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ 5 + $kriteria->count() }}" class="center">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- PENUTUP --}}
    <div class="penutup">
        <p>Demikian laporan ini dibuat berdasarkan hasil perhitungan sistem pendukung keputusan yang telah dilaksanakan.
        Laporan ini bersifat resmi dan dapat digunakan sebagai dasar pengambilan keputusan pemberian dana pendidikan
        kepada calon penerima yang dinyatakan layak.</p>
    </div>

    {{-- TTD --}}
    <div class="ttd-wrap">
        <div class="ttd-box">
            <p>Mengetahui,</p>
            <p>Ketua Yayasan {{ $settings['yayasan_name'] ?? '' }}</p>
            <div class="ttd-space"></div>
            <span class="ttd-name">{{ auth()->user()->name ?? '____________________' }}</span>
        </div>
        <div class="ttd-box">
            <p>{{ now()->format('d F Y') }}</p>
            <p>Dibuat oleh, Admin Yayasan</p>
            <div class="ttd-space"></div>
            <span class="ttd-name">( Admin Butterflies )</span>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Dicetak melalui Sistem Butterflies SPK SAW &mdash; {{ now()->format('d/m/Y H:i') }} WIB
    </div>

</div>
</div>

<script>
    window.addEventListener('load', function() {
        setTimeout(() => window.print(), 500);
    });
</script>
</body>
</html>