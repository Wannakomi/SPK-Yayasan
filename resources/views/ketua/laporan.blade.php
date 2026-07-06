@extends('layouts.ketua')
@section('title','Unduh Laporan')
@section('page-title','Unduh Laporan')
@section('page-sub','Dokumen resmi hasil seleksi penerima dana pendidikan')

@section('content')

<div class="Alert info">
    <ion-icon name="information-circle-outline"></ion-icon>
    Laporan bersifat <strong>read-only</strong>. Untuk mengubah data, hubungi Admin Yayasan.
</div>

<div class="Charts-Row">
    {{-- PILIHAN FORMAT --}}
    <div class="Card" style="margin-bottom:0">
        <div class="Card-Header"><div><p class="Card-Title">Pilih Format Laporan</p><p class="Card-Sub">Unduh dokumen resmi hasil seleksi</p></div></div>

        <a href="{{ route('ketua.cetak-pdf') }}" target="_blank"
           style="display:flex;align-items:center;gap:14px;padding:16px;background:rgba(224,92,92,0.08);border:1px solid rgba(224,92,92,0.20);border-radius:var(--r-md);margin-bottom:10px;cursor:pointer;transition:all .18s;text-decoration:none"
           onmouseover="this.style.borderColor='rgba(224,92,92,0.45)';this.style.background='rgba(224,92,92,0.14)'"
           onmouseout="this.style.borderColor='rgba(224,92,92,0.20)';this.style.background='rgba(224,92,92,0.08)'">
            <div style="width:46px;height:46px;border-radius:var(--r-md);background:rgba(224,92,92,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <ion-icon name="document-text-outline" style="font-size:1.4rem;color:var(--red-l)"></ion-icon>
            </div>
            <div style="flex:1">
                <p style="font-size:.84rem;font-weight:700;color:var(--text-1)">Laporan PDF — Cetak Resmi</p>
                <p style="font-size:.72rem;color:var(--text-4);margin-top:2px">Format siap cetak dengan kop surat dan kolom tanda tangan.</p>
            </div>
            <span class="Badge red">⬇ PDF</span>
        </a>

        <a href="{{ route('ketua.export-csv') }}"
           style="display:flex;align-items:center;gap:14px;padding:16px;background:rgba(46,204,138,0.08);border:1px solid rgba(46,204,138,0.20);border-radius:var(--r-md);margin-bottom:10px;cursor:pointer;transition:all .18s;text-decoration:none"
           onmouseover="this.style.borderColor='rgba(46,204,138,0.45)';this.style.background='rgba(46,204,138,0.14)'"
           onmouseout="this.style.borderColor='rgba(46,204,138,0.20)';this.style.background='rgba(46,204,138,0.08)'">
            <div style="width:46px;height:46px;border-radius:var(--r-md);background:rgba(46,204,138,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <ion-icon name="grid-outline" style="font-size:1.4rem;color:var(--green-l)"></ion-icon>
            </div>
            <div style="flex:1">
                <p style="font-size:.84rem;font-weight:700;color:var(--text-1)">Laporan Excel / CSV — Data Lengkap</p>
                <p style="font-size:.72rem;color:var(--text-4);margin-top:2px">Seluruh data penilaian, skor SAW, dan peringkat dalam format spreadsheet.</p>
            </div>
            <span class="Badge green">⬇ CSV</span>
        </a>

        <div onclick="window.open('{{ route('ketua.cetak-pdf') }}','_blank').print()"
           style="display:flex;align-items:center;gap:14px;padding:16px;background:rgba(74,144,217,0.08);border:1px solid rgba(74,144,217,0.20);border-radius:var(--r-md);cursor:pointer;transition:all .18s"
           onmouseover="this.style.borderColor='rgba(74,144,217,0.45)';this.style.background='rgba(74,144,217,0.14)'"
           onmouseout="this.style.borderColor='rgba(74,144,217,0.20)';this.style.background='rgba(74,144,217,0.08)'">
            <div style="width:46px;height:46px;border-radius:var(--r-md);background:rgba(74,144,217,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <ion-icon name="print-outline" style="font-size:1.4rem;color:var(--blue-l)"></ion-icon>
            </div>
            <div style="flex:1">
                <p style="font-size:.84rem;font-weight:700;color:var(--text-1)">Cetak Langsung</p>
                <p style="font-size:.72rem;color:var(--text-4);margin-top:2px">Buka dialog cetak browser untuk mencetak langsung ke printer.</p>
            </div>
            <span class="Badge blue">🖨 Cetak</span>
        </div>
    </div>

    {{-- RINGKASAN --}}
    <div class="Card" style="margin-bottom:0">
        <div class="Card-Header"><div><p class="Card-Title">Ringkasan Laporan</p><p class="Card-Sub">Periode {{ $periode }}</p></div></div>

        <div style="display:flex;flex-direction:column;gap:10px">
            @php $layakList = $ranking->where('status_kelayakan','layak'); @endphp

            <div style="padding:12px 14px;background:rgba(255,255,255,0.04);border-radius:var(--r-md);border:1px solid var(--border)">
                <p style="font-size:.7rem;color:var(--text-4);margin-bottom:3px">Total Peserta</p>
                <p style="font-size:1.2rem;font-weight:800;color:var(--text-1)">{{ $ranking->count() }} Calon</p>
            </div>
            <div style="padding:12px 14px;background:rgba(201,168,76,0.08);border-radius:var(--r-md);border:1px solid rgba(201,168,76,0.20)">
                <p style="font-size:.7rem;color:var(--gold);margin-bottom:3px">Dinyatakan Layak</p>
                <p style="font-size:1.2rem;font-weight:800;color:var(--gold-l)">{{ $layak }} Calon</p>
            </div>
            <div style="padding:12px 14px;background:rgba(224,92,92,0.08);border-radius:var(--r-md);border:1px solid rgba(224,92,92,0.18)">
                <p style="font-size:.7rem;color:var(--red-l);margin-bottom:3px">Tidak Lolos</p>
                <p style="font-size:1.2rem;font-weight:800;color:var(--red-l)">{{ $ranking->count() - $layak }} Calon</p>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:12px;margin-top:4px">
                <p style="font-size:.76rem;font-weight:700;color:var(--text-2);margin-bottom:8px">Penerima Beasiswa:</p>
                @foreach($layakList->take(5) as $r)
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:7px">
                    <span style="width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-l));display:flex;align-items:center;justify-content:center;font-size:.62rem;font-weight:800;color:#1a1200;flex-shrink:0">{{ $r->ranking }}</span>
                    <span style="font-size:.78rem;color:var(--text-2);font-weight:600">{{ $r->calonPenerima->nama }}</span>
                    <span style="font-size:.7rem;color:var(--gold-l);font-weight:700;margin-left:auto">{{ number_format($r->skor_akhir,4) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- PREVIEW LAPORAN --}}
<div class="Card">
    <div class="Card-Header">
        <div><p class="Card-Title">Preview Laporan Cetak</p><p class="Card-Sub">Tampilan yang akan dicetak</p></div>
        <span class="Badge gold">Siap Cetak</span>
    </div>

    <div style="background:rgba(255,255,255,0.04);border-radius:var(--r-md);padding:20px;border:1px solid var(--border)">
        <div style="text-align:center;padding-bottom:14px;border-bottom:2px solid rgba(201,168,76,0.40);margin-bottom:18px">
            <p style="font-size:.95rem;font-weight:900;color:var(--text-1);text-transform:uppercase">{{ $settings['yayasan_name'] ?? 'Yayasan Sahabat Yatim' }}</p>
            <p style="font-size:.74rem;color:var(--text-4);margin-top:3px">{{ $settings['yayasan_address'] ?? '' }}</p>
            <p style="font-size:.88rem;font-weight:800;color:var(--gold-l);margin-top:10px;text-transform:uppercase">Laporan Hasil Seleksi Penerima Dana Pendidikan</p>
            <p style="font-size:.74rem;color:var(--text-4);margin-top:2px">Metode Simple Additive Weighting (SAW) — Periode {{ $periode }}</p>
        </div>

        <div class="Table-Wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Jenjang</th>
                        @foreach($kriteria as $k)
                        <th class="Td-Center">{{ $k->kode_kriteria }}</th>
                        @endforeach
                        <th class="Td-Center">Skor Vi</th>
                        <th class="Td-Center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ranking as $r)
                    <tr>
                        <td>
                            @if($r->ranking<=3)
                                <span class="Rank-Medal {{ ['Medal-1','Medal-2','Medal-3'][$r->ranking-1] }}">{{ $r->ranking }}</span>
                            @else
                                <span class="Rank-Medal Medal-n">{{ $r->ranking }}</span>
                            @endif
                        </td>
                        <td class="Td-Code">{{ $r->calonPenerima->kode_anak }}</td>
                        <td class="Td-Bold">{{ $r->calonPenerima->nama }}</td>
                        <td>{{ $r->calonPenerima->jenjang }} {{ $r->calonPenerima->kelas }}</td>
                        @foreach($kriteria as $k)
                        <td class="Td-Center">
                            {{ $r->calonPenerima->getNilai($k->kode_kriteria) !== null
                                ? number_format((float)$r->calonPenerima->getNilai($k->kode_kriteria), 2)
                                : '—' }}
                        </td>
                        @endforeach
                        <td class="Td-Center">
                            <strong style="color:{{ $r->is_layak?'var(--gold-l)':'var(--text-4)' }}">
                                {{ number_format($r->skor_akhir,4) }}
                            </strong>
                        </td>
                        <td class="Td-Center">
                            <span class="Badge {{ $r->is_layak?'gold':'gray' }}">
                                {{ $r->is_layak?'LAYAK':'TIDAK' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 4 + $kriteria->count() }}">
                            <div class="Empty-State"><p>Belum ada data.</p></div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:32px;text-align:center">
            <div>
                <p style="font-size:.74rem;color:var(--text-4)">Mengetahui,<br>Ketua Yayasan {{ $settings['yayasan_name'] ?? '' }}</p>
                <div style="height:50px;border-bottom:1px solid var(--border-4);margin:10px 0"></div>
                <p style="font-size:.74rem;color:var(--text-3)">( {{ auth()->user()->name ?? 'H. Mahmud' }} )</p>
            </div>
            <div>
                <p style="font-size:.74rem;color:var(--text-4)">Dibuat oleh,<br>Admin / Pengurus Yayasan</p>
                <div style="height:50px;border-bottom:1px solid var(--border-4);margin:10px 0"></div>
                <p style="font-size:.74rem;color:var(--text-3)">( Admin Butterflies )</p>
            </div>
        </div>
        <p style="text-align:center;font-size:.66rem;color:var(--text-4);margin-top:16px;padding-top:12px;border-top:1px solid var(--border-2)">
            Dicetak melalui Sistem Butterflies · {{ now()->format('d/m/Y H:i') }} WIB
        </p>
    </div>
</div>

@endsection