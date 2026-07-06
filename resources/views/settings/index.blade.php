@extends('layouts.app')
@section('title','Settings')
@section('page-title','Settings')
@section('page-sub','Konfigurasi sistem Butterflies')

@section('content')

<div style="display:flex;gap:4px;padding:4px;background:rgba(255,255,255,0.55);border-radius:var(--r-md);width:fit-content;margin-bottom:20px;border:1.5px solid var(--border);backdrop-filter:blur(10px)">
    @foreach([['umum','settings-outline','Umum'],['spk','calculator-outline','SAW & SPK'],['periode','calendar-outline','Periode']] as $i=>$t)
    <button onclick="showSettingTab('{{ $t[0] }}')" id="stab-{{ $t[0] }}" class="Btn {{ $i===0?'Btn-Primary':'Btn-Secondary' }} Btn-Sm">
        <ion-icon name="{{ $t[1] }}"></ion-icon>{{ $t[2] }}
    </button>
    @endforeach
</div>

{{-- TAB UMUM --}}
<div id="spane-umum">
    <div class="Charts-Row">
        <div class="Card" style="margin-bottom:0">
            <div class="Card-Header"><div><p class="Card-Title">Identitas Sistem</p></div></div>
            <form method="POST" action="{{ route('settings.umum') }}">
                @csrf @method('PATCH')
                <div style="display:flex;flex-direction:column;gap:14px">
                    <div class="Form-Group"><label class="Form-Label">Nama Aplikasi</label><input type="text" name="app_name" class="Form-Input" value="{{ $settings['app_name'] ?? 'Butterflies' }}"></div>
                    <div class="Form-Group"><label class="Form-Label">Nama Yayasan</label><input type="text" name="yayasan_name" class="Form-Input" value="{{ $settings['yayasan_name'] ?? '' }}"></div>
                    <div class="Form-Group"><label class="Form-Label">Alamat</label><textarea name="yayasan_address" class="Form-Textarea" style="min-height:68px">{{ $settings['yayasan_address'] ?? '' }}</textarea></div>
                    <div class="Form-Group"><label class="Form-Label">Telepon</label><input type="text" name="yayasan_phone" class="Form-Input" value="{{ $settings['yayasan_phone'] ?? '' }}"></div>
                    <div class="Form-Group"><label class="Form-Label">Email</label><input type="email" name="yayasan_email" class="Form-Input" value="{{ $settings['yayasan_email'] ?? '' }}"></div>
                </div>
                <div style="margin-top:18px"><button type="submit" class="Btn Btn-Primary"><ion-icon name="save-outline"></ion-icon>Simpan Identitas</button></div>
            </form>
        </div>
        <div class="Card" style="margin-bottom:0">
            <div class="Card-Header"><div><p class="Card-Title">Status Sistem</p></div></div>
            @php $statuses=[['Database','Terhubung','green','server-outline'],['Periode Aktif',$settings['periode_aktif']??'2025/2026','blue','calendar-outline'],['Bobot Kriteria',\App\Models\Kriteria::bobotValid()?'100% Valid':'Tidak Valid',\App\Models\Kriteria::bobotValid()?'green':'red','book-outline'],['PHP Version',phpversion(),'blue','code-slash-outline']]; @endphp
            <div style="display:flex;flex-direction:column;gap:8px">
                @foreach($statuses as $s)
                <div style="display:flex;align-items:center;gap:8px;padding:10px 13px;border-radius:var(--r-md);background:rgba(255,255,255,0.40);border:1px solid var(--border)">
                    <ion-icon name="{{ $s[3] }}" style="font-size:.95rem;color:var(--{{ $s[2] }});flex-shrink:0"></ion-icon>
                    <div style="flex:1"><p style="font-size:.78rem;font-weight:700;color:var(--text-1)">{{ $s[0] }}</p><p style="font-size:.68rem;color:var(--text-4)">{{ $s[1] }}</p></div>
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--{{ $s[2] }})"></span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- TAB SPK --}}
<div id="spane-spk" style="display:none">
    <div class="Charts-Row">
        <div class="Card" style="margin-bottom:0">
            <div class="Card-Header"><div><p class="Card-Title">Parameter SAW</p></div></div>
            <form method="POST" action="{{ route('settings.spk') }}">
                @csrf @method('PATCH')
                <div style="display:flex;flex-direction:column;gap:14px">
                    <div class="Form-Group">
                        <label class="Form-Label">Threshold Kelayakan (Vi)</label>
                        <input type="number" name="threshold_layak" class="Form-Input" value="{{ $settings['threshold_layak'] ?? '0.75' }}" step="0.01" min="0" max="1">
                        <p class="Form-Hint">Calon dengan skor ≥ threshold dinyatakan Layak. Default: 0.75</p>
                    </div>
                    <div class="Form-Group">
                        <label class="Form-Label">Kuota Penerima</label>
                        <input type="number" name="kuota_penerima" class="Form-Input" value="{{ $settings['kuota_penerima'] ?? '5' }}" min="1">
                    </div>
                </div>
                <div style="margin-top:18px"><button type="submit" class="Btn Btn-Primary"><ion-icon name="save-outline"></ion-icon>Simpan Konfigurasi SAW</button></div>
            </form>
        </div>
        <div class="Card" style="margin-bottom:0">
            <div class="Card-Header"><div><p class="Card-Title">Bobot Aktif</p></div><a href="{{ route('kriteria.index') }}" class="Btn Btn-Secondary Btn-Sm"><ion-icon name="pencil-outline"></ion-icon>Edit</a></div>
            @foreach(\App\Models\Kriteria::orderBy('kode_kriteria')->get() as $k)
            @php $c=['C1'=>'#a78bfa','C2'=>'#34d399','C3'=>'#60a5fa'][$k->kode_kriteria]??'#a78bfa'; @endphp
            <div style="padding:12px 14px;border-radius:var(--r-md);border:1px solid var(--border);margin-bottom:10px;background:rgba(255,255,255,0.40)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="width:30px;height:30px;border-radius:50%;background:{{$c}}22;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;color:{{$c}}">{{ $k->kode_kriteria }}</span>
                        <div><p style="font-size:.8rem;font-weight:700;color:var(--text-1)">{{ $k->nama_kriteria }}</p><span class="Badge {{ $k->atribut==='cost'?'red':'green' }}" style="font-size:.62rem">{{ strtoupper($k->atribut) }}</span></div>
                    </div>
                    <span style="font-size:1.1rem;font-weight:900;color:{{$c}}">{{ $k->bobot_persen }}</span>
                </div>
                <div class="Acq-Bar-Wrap" style="height:6px"><div style="height:6px;width:{{ $k->bobot*100 }}%;background:{{$c}};border-radius:3px"></div></div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- TAB PERIODE --}}
<div id="spane-periode" style="display:none">
    <div class="Charts-Row">
        <div class="Card" style="margin-bottom:0">
            <div class="Card-Header"><div><p class="Card-Title">Periode Aktif</p></div></div>
            <form method="POST" action="{{ route('settings.periode') }}">
                @csrf @method('PATCH')
                <div class="Form-Group">
                    <label class="Form-Label">Periode Aktif Saat Ini</label>
                    <input type="text" name="periode_aktif" class="Form-Input" value="{{ $settings['periode_aktif'] ?? '2025/2026' }}" placeholder="2025/2026">
                    <p class="Form-Hint">Format: YYYY/YYYY (contoh: 2025/2026)</p>
                </div>
                <div style="margin-top:14px"><button type="submit" class="Btn Btn-Primary"><ion-icon name="save-outline"></ion-icon>Perbarui Periode</button></div>
            </form>
        </div>
        <div class="Card" style="margin-bottom:0">
            <div class="Card-Header"><div><p class="Card-Title">Buka Periode Baru</p></div></div>
            <div class="Alert warning"><ion-icon name="warning-outline"></ion-icon>Membuka periode baru akan mengganti periode aktif. Data lama tetap tersimpan.</div>
            <form method="POST" action="{{ route('settings.periode.buka') }}">
                @csrf
                <div class="Form-Group">
                    <label class="Form-Label">Nama Periode Baru <span>*</span></label>
                    <input type="text" name="nama_periode" class="Form-Input" placeholder="Contoh: 2026/2027" required>
                </div>
                <div style="margin-top:14px"><button type="submit" class="Btn Btn-Orange" onclick="return confirm('Buka periode baru? Periode aktif akan berubah.')"><ion-icon name="calendar-outline"></ion-icon>Buka Periode Baru</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showSettingTab(name){
    ['umum','spk','periode'].forEach(t=>{
        document.getElementById('spane-'+t).style.display = t===name?'':'none';
        const btn=document.getElementById('stab-'+t);
        btn.className = t===name?'Btn Btn-Primary Btn-Sm':'Btn Btn-Secondary Btn-Sm';
    });
}
</script>
@endpush
