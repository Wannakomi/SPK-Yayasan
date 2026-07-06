@extends('layouts.app')
@section('title','Edit Calon')
@section('page-title','Edit Calon Penerima')
@section('page-sub','Ubah data — '.$calon->kode_anak.' · '.$calon->nama)

@section('content')
<div class="Card">
    <div class="Card-Header">
        <div>
            <p class="Card-Title">✏️ Edit — {{ $calon->kode_anak }}</p>
            <p class="Card-Sub">{{ $calon->nama }}</p>
        </div>
        <a href="{{ route('calon-penerima.index') }}" class="Btn Btn-Secondary Btn-Sm">
            <ion-icon name="arrow-back-outline"></ion-icon>Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('calon-penerima.update',$calon) }}">
        @csrf @method('PUT')

        <div class="Form-Grid">

            <div class="Form-Group Form-Full">
                <label class="Form-Label">Nama Lengkap <span>*</span></label>
                <input type="text"
                       name="nama"
                       class="Form-Input"
                       value="{{ old('nama', $calon->nama) }}"
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
                       placeholder="Input nilai {{ $k->kode_kriteria }} (opsional)"
                       step="0.01"
                       min="0"
                       value="{{ old('nilai_kriteria.'.$k->kode_kriteria, $calon->getNilai($k->kode_kriteria)) }}">
                <p class="Form-Hint">{{ ucfirst($k->atribut) }} · Bobot {{ $k->bobot_persen }}</p>
            </div>
            @endforeach

            <div class="Form-Group">
                <label class="Form-Label">Jenjang Sekolah</label>
                <select name="jenjang" class="Form-Select" id="jenjang" onchange="updateKelas()">
                    <option value="">-- Pilih --</option>
                    @foreach(['SD','SMP','SMA/SMK'] as $j)
                    <option value="{{ $j }}" {{ old('jenjang', $calon->jenjang) === $j ? 'selected' : '' }}>
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
                          placeholder="Keterangan tambahan (opsional)">{{ old('catatan', $calon->catatan) }}</textarea>
            </div>

        </div>

        <div style="margin-top:16px" class="Btn-Group">
            <button type="submit" class="Btn Btn-Primary">
                <ion-icon name="save-outline"></ion-icon>Simpan Perubahan
            </button>
            <a href="{{ route('calon-penerima.index') }}" class="Btn Btn-Secondary">Batal</a>
        </div>
    </form>
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
        const oldVal  = '{{ old('kelas', $calon->kelas) }}';

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