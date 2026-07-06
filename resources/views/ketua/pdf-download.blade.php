<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Resmi — {{ $periode }}</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        @page{
            size:A4 portrait;
            margin:0;
        }

        body{
            font-family:"Times New Roman", serif;
            font-size:11pt;
            padding:40px;
        }

        /* ==========================
        KOP SURAT
        ========================== */

        .kop-table{
            width:100%;
            border-collapse:collapse;
            margin-bottom:3px;
        }

        .kop-table td{
            vertical-align:middle;
        }

        .kop-logo{
            width:80px;
            text-align:center;
        }

        .kop-logo img{
            width:60px;
            height:60px;
        }

        .kop-text{
            text-align:center;
            padding-right:80px;
        }

        .kop-text h1{
            font-size:14pt;
            font-weight:bold;
            text-transform:uppercase;
            margin-bottom:2px;
        }

        .kop-text p{
            font-size:9pt;
            margin-bottom:1px;
        }

        .kop-line{
            border-bottom:2px solid #000;
            margin-bottom:1px;
        }

        .kop-line2{
            border-bottom:1px solid #000;
            margin-bottom:10px;
        }

        /* ==========================
        JUDUL
        ========================== */

        .judul{
            text-align:center;
            margin-bottom:8px;
        }

        .judul h2{
            font-size:12pt;
            font-weight:bold;
            text-transform:uppercase;
            text-decoration:underline;
            margin-bottom:2px;
        }

        .judul p{
            font-size:10pt;
        }

        /* ==========================
        INFO
        ========================== */

        .info-table{
            margin-top:8px;
            margin-bottom:12px;
        }

        .info-table td{
            padding:2px 5px;
        }

        /* ==========================
        PARAGRAF
        ========================== */

        .pembuka,
        .penutup{
            text-align:justify;
            font-size:10pt;
            line-height:1.4;
            margin-bottom:8px;
        }

        /* ==========================
        TABEL HASIL
        ========================== */

        table.ranking{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            margin-top:10px;
            margin-bottom:12px;
            font-size:9pt;
        }

        table.ranking th{
            border:1px solid #000;
            background:#f2f2f2;
            text-align:center;
            vertical-align:middle;
            padding:5px;
        }

        table.ranking td,
        table.ranking th{
            overflow:hidden;
        }

        .center{
            text-align:center;
        }

        .bold{
            font-weight:bold;
        }

        table.ranking tr.layak{
            background:#fafafa;
        }

        /* ==========================
        TANDA TANGAN
        ========================== */

        .ttd-table{
            width:100%;
            margin-top:20px;
        }

        .ttd-table td{
            width:50%;
            text-align:center;
            vertical-align:top;
            font-size:10pt;
        }

        .ttd-space{
            height:60px;
        }

        .ttd-name{
            display:inline-block;
            min-width:150px;
            border-top:1px solid #000;
            padding-top:3px;
            font-weight:bold;
        }

        /* ==========================
        FOOTER
        ========================== */

        .footer{
            margin-top:15px;
            text-align:center;
            font-size:8pt;
        }
    </style>
</head>
<body>

    {{-- KOP --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo">
            </td>
            <td class="kop-text">
                <h1>{{ $settings['yayasan_name'] ?? 'Yayasan Sahabat Yatim' }}</h1>
                <p>{{ $settings['yayasan_address'] ?? '' }}</p>
                <p>Telp: {{ $settings['yayasan_phone'] ?? '-' }}</p>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>
    <div class="kop-line2"></div>

    {{-- JUDUL --}}
    <div class="judul">
        <h2>Laporan Hasil Seleksi Penerima Dana Pendidikan</h2>
        <p>Metode Simple Additive Weighting (SAW) &mdash; Periode {{ $periode }}</p>
    </div>

    {{-- INFO --}}
    <table class="info-table">
        <tr><td>Tanggal</td><td>:</td><td>{{ now()->format('d F Y') }}</td></tr>
        <tr><td>Periode</td><td>:</td><td>{{ $periode }}</td></tr>
        <tr><td>Total Peserta</td><td>:</td><td>{{ $ranking->count() }} Calon</td></tr>
        <tr><td>Dinyatakan Layak</td><td>:</td><td>{{ $ranking->where('status_kelayakan','layak')->count() }} Calon</td></tr>
        <tr><td>Tidak Lolos</td><td>:</td><td>{{ $ranking->where('status_kelayakan','tidak_layak')->count() }} Calon</td></tr>
    </table>

    {{-- PEMBUKA --}}
    <div class="pembuka">
        Berdasarkan hasil perhitungan menggunakan metode <em>Simple Additive Weighting</em> (SAW) dengan kriteria
        @foreach($kriteria as $k){{ $k->kode_kriteria }} ({{ $k->nama_kriteria }}, bobot {{ $k->bobot*100 }}%){{ !$loop->last ? ', ' : '' }}@endforeach,
        berikut adalah hasil seleksi calon penerima dana pendidikan periode {{ $periode }}:
    </div>

    {{-- TABEL --}}
    <table class="ranking">
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:7%">Kode</th>
                <th style="width:25%">Nama Lengkap</th>
                <th style="width:15%">Jenjang</th>

                @foreach($kriteria as $k)
                <th style="width:8%">{{ $k->kode_kriteria }}</th>
                @endforeach

                <th style="width:11%">Skor</th>
                <th style="width:16%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ranking as $r)
            <tr class="{{ $r->is_layak ? 'layak' : '' }}">
                <td class="center">{{ $r->ranking }}</td>
                <td class="center bold">{{ $r->calonPenerima->kode_anak }}</td>
                <td class="bold">{{ $r->calonPenerima->nama }}</td>
                <td>{{ $r->calonPenerima->jenjang }} {{ $r->calonPenerima->kelas }}</td>
                @foreach($kriteria as $k)
                <td class="center">
                    {{ $r->calonPenerima->getNilai($k->kode_kriteria) !== null
                        ? number_format((float)$r->calonPenerima->getNilai($k->kode_kriteria), 2)
                        : '-' }}
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
        Demikian laporan ini dibuat berdasarkan hasil perhitungan sistem pendukung keputusan yang telah dilaksanakan.
        Laporan ini bersifat resmi dan dapat digunakan sebagai dasar pengambilan keputusan pemberian dana pendidikan
        kepada calon penerima yang dinyatakan layak.
    </div>

    {{-- TTD --}}
    <table class="ttd-table">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p>Ketua Yayasan {{ $settings['yayasan_name'] ?? '' }}</p>
                <div class="ttd-space"></div>
                <span class="ttd-name">{{ auth()->user()->name ?? '____________________' }}</span>
            </td>
            <td>
                <p>{{ now()->format('d F Y') }}</p>
                <p>Dibuat oleh, Admin Yayasan</p>
                <div class="ttd-space"></div>
                <span class="ttd-name">( Admin )</span>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Dicetak melalui Sistem Butterflies SPK SAW &mdash; {{ now()->format('d/m/Y H:i') }} WIB
    </div>

</body>
</html>