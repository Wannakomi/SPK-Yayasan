<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>
    Laporan SAW
</title>

<style>

@page{
    size:A4;
    margin:18mm;
}

body{
    font-family:Arial,sans-serif;
    font-size:11px;
    color:#111;
}

.header{
    border-bottom:2px solid #000;
    padding-bottom:12px;
    margin-bottom:18px;
}

.title{
    text-align:center;
    margin-bottom:20px;
}

.title h1{
    font-size:22px;
    margin-bottom:6px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:12px;
}

th{
    background:#f3f4f6;
    border:1px solid #ccc;
    padding:7px;
    font-size:10px;
}

td{
    border:1px solid #ddd;
    padding:7px;
    font-size:10px;
}

.center{
    text-align:center;
}

.layak{
    color:green;
    font-weight:bold;
}

.tidak{
    color:red;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="header">

    <h2>
        {{ $settings['yayasan_name'] ?? 'Yayasan Sahabat Yatim' }}
    </h2>

    <p>
        Periode :
        {{ $periode }}
    </p>

</div>

<div class="title">

    <h1>

        @if($jenis == 'ranking')

            Laporan Ranking

        @elseif($jenis == 'layak')

            Laporan Penerima Layak

        @else

            Analisis Metode SAW

        @endif

    </h1>

</div>

<table>

<thead>

<tr>

    <th>Rank</th>
    <th>Kode</th>
    <th>Nama</th>

    @foreach($kriteria as $k)

        <th>
            {{ $k->kode_kriteria }}
        </th>

    @endforeach

    <th>Skor</th>
    <th>Status</th>

</tr>

</thead>

<tbody>

@foreach($ranking as $r)

<tr>

    <td class="center">
        {{ $r->ranking }}
    </td>

    <td class="center">
        {{ $r->calonPenerima->kode_anak }}
    </td>

    <td>
        {{ $r->calonPenerima->nama }}
    </td>

    @foreach($kriteria as $k)

    <td class="center">

        {{ $r->calonPenerima
            ->getNilai($k->kode_kriteria) }}

    </td>

    @endforeach

    <td class="center">

        {{ number_format($r->skor_akhir,3) }}

    </td>

    <td class="center">

        <span class="{{ $r->is_layak ? 'layak' : 'tidak' }}">

            {{ $r->is_layak ? 'LAYAK' : 'TIDAK' }}

        </span>

    </td>

</tr>

@endforeach

</tbody>

</table>

</body>
</html>

