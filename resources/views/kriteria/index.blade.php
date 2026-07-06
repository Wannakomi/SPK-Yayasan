@extends('layouts.app')
@section('title','Bobot & Kriteria')
@section('page-title','Bobot & Kriteria')
@section('page-sub','Konfigurasi kriteria dan bobot SAW')

@section('content')

@if(!$bobotValid)
<div class="Alert warning"><ion-icon name="warning-outline"></ion-icon>Total bobot = <strong>{{ round($totalBobot*100,1) }}%</strong>. Harus tepat 100% sebelum proses SAW.</div>
@else
<div class="Alert success"><ion-icon name="checkmark-circle-outline"></ion-icon>Total bobot = <strong>100%</strong> — Konfigurasi valid dan siap digunakan.</div>
@endif

{{-- KARTU KRITERIA --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px">
    @foreach($kriteria as $k)
    @php $colors=['C1'=>'#a78bfa','C2'=>'#34d399','C3'=>'#60a5fa']; $c=$colors[$k->kode_kriteria]??'#a78bfa'; @endphp
    <div class="Card" style="margin-bottom:0;border-top:3px solid {{$c}}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <span style="font-size:1.1rem;font-weight:900;color:{{$c}}">{{ $k->kode_kriteria }}</span>
            <span class="Badge {{ $k->atribut==='cost'?'red':'green' }}">{{ strtoupper($k->atribut) }}</span>
        </div>
        <p style="font-size:.86rem;font-weight:800;color:var(--text-1);margin-bottom:5px">{{ $k->nama_kriteria }}</p>
        <p style="font-size:.72rem;color:var(--text-3);margin-bottom:12px;line-height:1.5">{{ $k->keterangan }}</p>
        <div style="margin-bottom:10px">
            <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                <span style="font-size:.72rem;color:var(--text-4)">Bobot</span>
                <span style="font-size:.82rem;font-weight:800;color:{{$c}}">{{ $k->bobot_persen }}</span>
            </div>
            <div class="Acq-Bar-Wrap" style="height:8px;border-radius:4px">
                <div style="height:8px;width:{{ $k->bobot*100 }}%;background:{{$c}};border-radius:4px"></div>
            </div>
        </div>
        <span class="Badge gray" style="font-size:.66rem">{{ $k->satuan }}</span>
        <div class="Btn-Group" style="margin-top:12px">
            <button onclick="openEditModal({{ $k->id }},'{{ $k->kode_kriteria }}','{{ $k->nama_kriteria }}','{{ $k->atribut }}',{{ $k->bobot }},'{{ $k->satuan }}','{{ addslashes($k->keterangan) }}')" class="Btn Btn-Secondary Btn-Sm"><ion-icon name="pencil-outline"></ion-icon>Edit</button>
            @if($kriteria->count() > 1)
            <form method="POST" action="{{ route('kriteria.destroy',$k) }}" onsubmit="return confirm('Hapus kriteria {{ $k->kode_kriteria }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="Btn Btn-Danger Btn-Sm"><ion-icon name="trash-outline"></ion-icon></button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- DETAIL TABLE --}}
<div class="Card">
    <div class="Card-Header">
        <div><p class="Card-Title">Detail Konfigurasi</p></div>
        <button onclick="document.getElementById('modal-tambah').classList.add('open')" class="Btn Btn-Primary">
            <ion-icon name="add-circle-outline"></ion-icon>Tambah Kriteria
        </button>
    </div>
    <div class="Table-Wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Atribut</th><th class="Td-Center">Bobot</th><th>Rumus Normalisasi</th><th class="Td-Center">Aksi</th></tr></thead>
            <tbody>
                @foreach($kriteria as $k)
                <tr>
                    <td class="Td-Code">{{ $k->kode_kriteria }}</td>
                    <td class="Td-Bold">{{ $k->nama_kriteria }}</td>
                    <td><span class="Badge {{ $k->atribut==='cost'?'red':'green' }}">{{ strtoupper($k->atribut) }}</span></td>
                    <td class="Td-Center">
                        <div class="Score-Bar-Wrap">
                            <div class="Score-Bar-Bg"><div class="Score-Bar-Fill" style="width:{{ $k->bobot*100 }}%;background:#a78bfa"></div></div>
                            <span class="Score-Num">{{ $k->bobot_persen }}</span>
                        </div>
                    </td>
                    <td><code style="background:rgba(124,58,237,0.10);padding:2px 8px;border-radius:6px;font-size:.75rem;color:var(--purple)">{{ $k->atribut==='cost'?'min/Xi':'Xi/max' }}</code></td>
                    <td class="Td-Center">
                        <button onclick="openEditModal({{ $k->id }},'{{ $k->kode_kriteria }}','{{ $k->nama_kriteria }}','{{ $k->atribut }}',{{ $k->bobot }},'{{ $k->satuan }}','{{ addslashes($k->keterangan) }}')" class="Btn Btn-Secondary Btn-Sm"><ion-icon name="pencil-outline"></ion-icon></button>
                    </td>
                </tr>
                @endforeach
                <tr style="background:rgba(167,139,250,0.10)">
                    <td colspan="3" style="font-weight:800;color:var(--purple)">TOTAL</td>
                    <td class="Td-Center" style="font-weight:900;color:{{ $bobotValid?'var(--purple)':'var(--red)' }};font-size:1rem">{{ round($totalBobot*100,1) }}%</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="Modal-Overlay" id="modal-tambah">
    <div class="Modal">
        <div class="Modal-Header">
            <p class="Modal-Title">Tambah Kriteria Baru</p>
            <button class="Modal-Close" onclick="document.getElementById('modal-tambah').classList.remove('open')"><ion-icon name="close-outline"></ion-icon></button>
        </div>
        <form method="POST" action="{{ route('kriteria.store') }}">
            @csrf
            <div class="Form-Grid">
                <div class="Form-Group"><label class="Form-Label">Kode <span>*</span></label><input type="text" name="kode_kriteria" class="Form-Input" placeholder="C4" required></div>
                <div class="Form-Group"><label class="Form-Label">Atribut <span>*</span></label>
                    <select name="atribut" class="Form-Select" required><option value="benefit">Benefit</option><option value="cost">Cost</option></select>
                </div>
                <div class="Form-Group Form-Full"><label class="Form-Label">Nama Kriteria <span>*</span></label><input type="text" name="nama_kriteria" class="Form-Input" placeholder="Nama kriteria" required></div>
                <div class="Form-Group"><label class="Form-Label">Bobot (0–1) <span>*</span></label><input type="number" name="bobot" class="Form-Input" step="0.01" min="0" max="1" placeholder="0.20" oninput="if(this.value > 1) this.value = 1;" required></div>
                <div class="Form-Group"><label class="Form-Label">Satuan</label><input type="text" name="satuan" class="Form-Input" placeholder="Contoh: Rp (juta)"></div>
                <div class="Form-Group Form-Full">
                    <div style="
                        padding:12px;
                        border-radius:10px;
                        background:#eef6ff;
                        border:1px solid #cfe2ff;
                        margin-bottom:12px;
                    ">
                        <strong>📌 Panduan Kriteria Benefit</strong>

                        <div style="margin-top:8px;font-size:.85rem;line-height:1.8">
                            1 = Sangat Buruk<br>
                            2 = Buruk<br>
                            3 = Cukup<br>
                            4 = Baik<br>
                            5 = Sangat Baik
                        </div>

                        <small style="display:block;margin-top:8px;color:#666">
                            Gunakan untuk kriteria kualitatif seperti Prestasi,
                            Kondisi Rumah, Keaktifan, dan sejenisnya.
                            Nilai yang dimasukkan pada calon penerima tetap berupa angka.
                        </small>
                    </div>

                    <label class="Form-Label">Keterangan</label>
                    <textarea
                        name="keterangan"
                        class="Form-Textarea"
                        placeholder="Penjelasan kriteria..."
                    ></textarea>
                </div>
            </div>
            <div class="Modal-Footer">
                <button type="button" class="Btn Btn-Secondary" onclick="document.getElementById('modal-tambah').classList.remove('open')">Batal</button>
                <button type="submit" class="Btn Btn-Primary"><ion-icon name="save-outline"></ion-icon>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="Modal-Overlay" id="modal-edit">
    <div class="Modal">
        <div class="Modal-Header">
            <p class="Modal-Title">Edit Kriteria</p>
            <button class="Modal-Close" onclick="document.getElementById('modal-edit').classList.remove('open')"><ion-icon name="close-outline"></ion-icon></button>
        </div>
        <form method="POST" id="edit-form">
            @csrf @method('PUT')
            <div class="Form-Grid">
                <div class="Form-Group"><label class="Form-Label">Kode <span>*</span></label><input type="text" name="kode_kriteria" id="edit-kode" class="Form-Input" required></div>
                <div class="Form-Group"><label class="Form-Label">Atribut <span>*</span></label>
                    <select name="atribut" id="edit-atribut" class="Form-Select" required><option value="benefit">Benefit</option><option value="cost">Cost</option></select>
                </div>
                <div class="Form-Group Form-Full"><label class="Form-Label">Nama Kriteria <span>*</span></label><input type="text" name="nama_kriteria" id="edit-nama" class="Form-Input" required></div>
                <div class="Form-Group"><label class="Form-Label">Bobot (0–1) <span>*</span></label><input type="number" name="bobot" id="edit-bobot" class="Form-Input" step="0.01" min="0" max="1" oninput="if(this.value > 1) this.value = 1;" required></div>
                <div class="Form-Group"><label class="Form-Label">Satuan</label><input type="text" name="satuan" id="edit-satuan" class="Form-Input" placeholder="Contoh: Rp (juta)"></div>
                <div class="Form-Group Form-Full">
                <div class="Form-Group Form-Full">
                    <div style="
                        padding:12px;
                        border-radius:10px;
                        background:#eef6ff;
                        border:1px solid #cfe2ff;
                        margin-bottom:12px;
                    ">
                        <strong>📌 Panduan Kriteria Benefit</strong>

                        <div style="margin-top:8px;font-size:.85rem;line-height:1.8">
                            1 = Sangat Buruk<br>
                            2 = Buruk<br>
                            3 = Cukup<br>
                            4 = Baik<br>
                            5 = Sangat Baik
                        </div>

                        <small style="display:block;margin-top:8px;color:#666">
                            Gunakan untuk kriteria kualitatif seperti Prestasi,
                            Kondisi Rumah, Keaktifan, dan sejenisnya.
                            Nilai yang dimasukkan pada calon penerima tetap berupa angka.
                        </small>
                    </div>

                    <label class="Form-Label">Keterangan</label>
                    <textarea
                        name="keterangan"
                        id="edit-ket"
                        class="Form-Textarea"
                    ></textarea>
                </div>
            </div>
            <div class="Modal-Footer">
                <button type="button" class="Btn Btn-Secondary" onclick="document.getElementById('modal-edit').classList.remove('open')">Batal</button>
                <button type="submit" class="Btn Btn-Primary"><ion-icon name="save-outline"></ion-icon>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, kode, nama, atribut, bobot, satuan, ket) {
    document.getElementById('edit-form').action = '/kriteria/'+id;
    document.getElementById('edit-kode').value   = kode;
    document.getElementById('edit-nama').value   = nama;
    document.getElementById('edit-atribut').value = atribut;
    document.getElementById('edit-bobot').value  = bobot;
    document.getElementById('edit-satuan').value = satuan;
    document.getElementById('edit-ket').value    = ket;
    document.getElementById('modal-edit').classList.add('open');
}
</script>
@endpush
