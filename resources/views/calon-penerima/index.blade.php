@extends('layouts.app')
@section('title','Data Calon Penerima')
@section('page-title','Data Calon Penerima')
@section('page-sub','Kelola data calon — Periode '.$periode)

@section('content')

<div class="Stats-Grid">
    <div class="Stat-Card purple">
        <div class="Stat-Info">
            <p class="Stat-Label">Total Calon</p>
            <h2 class="Stat-Val">{{ $calon->total() }}</h2>
            <p class="Stat-Delta gray">
                <ion-icon name="people-outline"></ion-icon>
                Periode {{ $periode }}
            </p>
        </div>

        <div class="Stat-Ring">
            <svg viewBox="0 0 36 36">
                <circle class="ring-bg" cx="18" cy="18" r="15.9"/>
                <circle class="ring-fg"
                        cx="18" cy="18" r="15.9"
                        stroke="#a78bfa"
                        stroke-dasharray="{{ min($calon->total()*10,100) }} 100"/>
            </svg>
            <span>{{ $calon->total() }}</span>
        </div>
    </div>

    <div class="Stat-Card green">
        <div class="Stat-Info">
            <p class="Stat-Label">Sudah Dinilai</p>
            <h2 class="Stat-Val">
                {{ $calon->filter(fn($c)=>$c->penilaianAktif)->count() }}
            </h2>
            <p class="Stat-Delta up">
                <ion-icon name="checkmark-circle-outline"></ion-icon>
                Data lengkap
            </p>
        </div>

        <div class="Stat-Ring">
            <svg viewBox="0 0 36 36">
                <circle class="ring-bg" cx="18" cy="18" r="15.9"/>
                <circle class="ring-fg"
                        cx="18" cy="18" r="15.9"
                        stroke="#2ecc8a"
                        stroke-dasharray="80 100"/>
            </svg>
            <span>80%</span>
        </div>
    </div>

    <div class="Stat-Card orange">
        <div class="Stat-Info">
            <p class="Stat-Label">Rata-rata Nilai</p>
            <h2 class="Stat-Val">
                {{ $calon->count()
                    ? round($calon->avg(fn($c) => $c->getNilai('C2')),1)
                    : '-' }}
            </h2>
            <p class="Stat-Delta gray">
                <ion-icon name="analytics-outline"></ion-icon>
                Nilai akademik
            </p>
        </div>

        <div class="Stat-Ring">
            <svg viewBox="0 0 36 36">
                <circle class="ring-bg" cx="18" cy="18" r="15.9"/>
                <circle class="ring-fg"
                        cx="18" cy="18" r="15.9"
                        stroke="#fb923c"
                        stroke-dasharray="{{ $calon->count()
                        ? round($calon->avg(fn($c) => $c->getNilai('C2')))
                        : 0 }} 100"/>
            </svg>
            <span>
                {{ $calon->count()
                    ? round($calon->avg(fn($c) => $c->getNilai('C2')))
                    : 0 }}
            </span>
        </div>
    </div>
</div>

{{-- FORM TAMBAH --}}
<div class="Card">
    <div class="Card-Header">
        <div>
            <p class="Card-Title">➕ Tambah Calon Penerima</p>
            <p class="Card-Sub">
                Kode anak dibuat otomatis — nomor yang kosong akan digunakan kembali
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('calon-penerima.store') }}">
        @csrf

        <div class="Form-Grid">

            <div class="Form-Group Form-Full">
                <label class="Form-Label">
                    Nama Lengkap <span>*</span>
                </label>
                <input type="text"
                       name="nama"
                       class="Form-Input"
                       value="{{ old('nama') }}"
                       required>
            </div>

            @foreach($kriteria as $k)
            <div class="Form-Group">
                <label class="Form-Label">
                    {{ $k->kode_kriteria }} — {{ $k->nama_kriteria }}
                </label>
                <input type="number"
                       name="nilai_kriteria[{{ $k->kode_kriteria }}]"
                       class="Form-Input"
                       step="0.01"
                       min="0"
                       value="{{ old('nilai_kriteria.'.$k->kode_kriteria) }}">
                <p class="Form-Hint">
                    {{ ucfirst($k->atribut) }} · Bobot {{ $k->bobot_persen }}
                </p>
            </div>
            @endforeach

            <div class="Form-Group">
                <label class="Form-Label">Jenjang Sekolah</label>
                <select name="jenjang"
                        class="Form-Select"
                        id="jenjang"
                        onchange="updateKelas()">
                    <option value="">-- Pilih --</option>
                    @foreach(['SD','SMP','SMA/SMK'] as $j)
                    <option value="{{ $j }}" {{ old('jenjang')===$j?'selected':'' }}>
                        {{ $j }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="Form-Group">
                <label class="Form-Label">Kelas</label>
                <select name="kelas" class="Form-Select" id="kelas">
                    <option value="">-- Pilih jenjang dulu --</option>
                </select>
            </div>

            <div class="Form-Group Form-Full">
                <label class="Form-Label">Catatan</label>
                <textarea name="catatan"
                          class="Form-Textarea"
                          placeholder="Keterangan tambahan (opsional)">{{ old('catatan') }}</textarea>
            </div>

        </div>

        <div style="margin-top:16px" class="Btn-Group">
            <button type="submit" class="Btn Btn-Primary">
                <ion-icon name="save-outline"></ion-icon>
                Simpan Data
            </button>
            <button type="reset" class="Btn Btn-Secondary">
                <ion-icon name="refresh-outline"></ion-icon>
                Reset
            </button>
        </div>
    </form>
</div>

{{-- TABEL DATA --}}
<div class="Card">
    <div class="Card-Header">
        <div>
            <p class="Card-Title">Semua Calon Penerima</p>
            <p class="Card-Sub">
                {{ $calon->total() }} data · kode otomatis & bisa digunakan ulang
            </p>
        </div>

        <div class="Btn-Group">
            <form method="GET" style="display:flex;gap:8px">
                <div class="Search-Bar">
                    <ion-icon name="search-outline"></ion-icon>
                    <input type="text"
                           name="search"
                           placeholder="Cari..."
                           value="{{ request('search') }}">
                </div>
                <button type="submit" class="Btn Btn-Secondary Btn-Sm">
                    <ion-icon name="search-outline"></ion-icon>
                </button>
            </form>

            <a href="{{ route('calon-penerima.export') }}" class="Btn Btn-Secondary Btn-Sm">
                <ion-icon name="download-outline"></ion-icon>
                CSV
            </a>
        </div>
    </div>

    <div class="Table-Wrap">
        <table>
            <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Jenjang</th>
                @foreach($kriteria as $k)
                <th class="Td-Center">{{ $k->kode_kriteria }} {{ $k->nama_kriteria }}</th>
                @endforeach
                <th>Status SAW</th>
                <th class="Td-Center">Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse($calon as $c)
            <tr>
                <td class="Td-Code">{{ $c->kode_anak }}</td>
                <td class="Td-Bold">{{ $c->nama }}</td>
                <td>
                    <span class="Badge gray">{{ $c->jenjang }} {{ $c->kelas }}</span>
                </td>

                @foreach($kriteria as $k)
                <td class="Td-Center">
                    @php $nilai = $c->getNilai($k->kode_kriteria); @endphp
                    {{ $nilai ?? '—' }}
                </td>
                @endforeach

                <td>
                    @if($c->penilaianAktif)
                        <span class="Badge {{ $c->penilaianAktif->is_layak?'green':'red' }}">
                            {{ $c->penilaianAktif->is_layak?'✓ Layak':'✗ Tidak' }}
                        </span>
                    @else
                        <span class="Badge gray">Belum dinilai</span>
                    @endif
                </td>

                <td>
                    <div style="display:flex;gap:5px;justify-content:center">
                        <a href="{{ route('calon-penerima.edit',$c) }}"
                           class="Btn Btn-Secondary Btn-Sm">
                            <ion-icon name="pencil-outline"></ion-icon>
                        </a>

                        <form method="POST"
                              action="{{ route('calon-penerima.destroy',$c) }}"
                              onsubmit="return confirm('Hapus {{ $c->nama }}? Kode {{ $c->kode_anak }} bisa digunakan lagi.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="Btn Btn-Danger Btn-Sm">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="{{ 5 + $kriteria->count() }}">
                    <div class="Empty-State">
                        <ion-icon name="people-outline"></ion-icon>
                        <p>Belum ada data calon.</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="Pagination">
        <p class="Page-Info">
            {{ $calon->firstItem() }}–{{ $calon->lastItem() }}
            dari {{ $calon->total() }}
        </p>
        {{ $calon->links() }}
    </div>
</div>

<script>
    const kelasMap = {
        'SD': ['Kelas 1','Kelas 2','Kelas 3','Kelas 4','Kelas 5','Kelas 6'],
        'SMP': ['Kelas 7','Kelas 8','Kelas 9'],
        'SMA/SMK': ['Kelas 10','Kelas 11','Kelas 12'],
    };

    function updateKelas() {
        const jenjang = document.getElementById('jenjang').value;
        const kelasEl = document.getElementById('kelas');
        const oldVal  = '{{ old('kelas') }}';

        kelasEl.innerHTML = '<option value="">-- Pilih kelas --</option>';

        if (kelasMap[jenjang]) {
            kelasMap[jenjang].forEach(k => {
                const opt = document.createElement('option');
                opt.value = k;
                opt.textContent = k;
                if (k === oldVal) opt.selected = true;
                kelasEl.appendChild(opt);
            });
        } else {
            kelasEl.innerHTML = '<option value="">-- Pilih jenjang dulu --</option>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const jenjang = document.getElementById('jenjang').value;
        if (jenjang) updateKelas();
    });
</script>

@endsection