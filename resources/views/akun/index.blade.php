@extends('layouts.app')
@section('title','Kelola Akun')
@section('page-title','Kelola Akun')
@section('page-sub','Manajemen pengguna sistem')

@section('content')

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:13px;margin-bottom:16px">
    <div class="Stat-Card purple"><div class="Stat-Info"><p class="Stat-Label">Total Akun</p><h2 class="Stat-Val">{{ $users->count() }}</h2></div></div>
    <div class="Stat-Card orange"><div class="Stat-Info"><p class="Stat-Label">Super Admin</p><h2 class="Stat-Val">{{ $users->where('role','superadmin')->count() }}</h2></div></div>
    <div class="Stat-Card green"><div class="Stat-Info"><p class="Stat-Label">Admin</p><h2 class="Stat-Val">{{ $users->where('role','admin')->count() }}</h2></div></div>
    <div class="Stat-Card blue"><div class="Stat-Info"><p class="Stat-Label">Aktif</p><h2 class="Stat-Val">{{ $users->where('is_active',true)->count() }}</h2></div></div>
</div>

<div class="Card">
    <div class="Card-Header">
        <div><p class="Card-Title">Daftar Pengguna</p></div>
        <button onclick="document.getElementById('modal-tambah').classList.add('open')" class="Btn Btn-Primary">
            <ion-icon name="person-add-outline"></ion-icon>Tambah Akun
        </button>
    </div>
    <div class="Table-Wrap">
        <table>
            <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th class="Td-Center">Aksi</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:9px">
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--purple),var(--purple-l));display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:800;color:#fff;flex-shrink:0">{{ strtoupper($u->name[0]) }}</div>
                            <span class="Td-Bold">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--text-3)">{{ $u->email }}</td>
                    <td><span class="Badge {{ $u->role==='superadmin'?'purple':($u->role==='admin'?'blue':'gray') }}">{{ $u->role_label }}</span></td>
                    <td><span class="Badge {{ $u->is_active?'green':'gray' }}">{{ $u->is_active?'● Aktif':'○ Nonaktif' }}</span></td>
                    <td>
                        <div style="display:flex;gap:5px;justify-content:center">
                            <button onclick="openEditUser({{ $u->id }},'{{ $u->name }}','{{ $u->email }}','{{ $u->role }}')" class="Btn Btn-Secondary Btn-Sm"><ion-icon name="pencil-outline"></ion-icon></button>
                            @if($u->id !== auth()->id() && $u->role !== 'superadmin')
                            <form method="POST" action="{{ route('akun.toggle-status',$u) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="Btn Btn-Orange Btn-Sm" title="{{ $u->is_active?'Nonaktifkan':'Aktifkan' }}">
                                    <ion-icon name="{{ $u->is_active?'pause-circle-outline':'play-circle-outline' }}"></ion-icon>
                                </button>
                            </form>
                            @if($u->role !== 'superadmin')
                            <form method="POST" action="{{ route('akun.destroy',$u) }}" onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="Btn Btn-Danger Btn-Sm"><ion-icon name="trash-outline"></ion-icon></button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- PROFIL SAYA --}}
<div class="Card">
    <div class="Card-Header"><div><p class="Card-Title">Profil Saya</p><p class="Card-Sub">Ubah data akun Anda</p></div></div>
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PATCH')
        <div class="Form-Grid">
            <div class="Form-Group">
                <label class="Form-Label">Nama Lengkap <span>*</span></label>
                <input type="text" name="name" class="Form-Input" value="{{ auth()->user()->name }}" required>
            </div>
            <div class="Form-Group">
                <label class="Form-Label">Email <span>*</span></label>
                <input type="email" name="email" class="Form-Input" value="{{ auth()->user()->email }}" required>
            </div>
        </div>
        <div style="margin-top:14px" class="Btn-Group">
            <button type="submit" class="Btn Btn-Primary"><ion-icon name="save-outline"></ion-icon>Simpan Profil</button>
        </div>
    </form>

    <hr style="border:none;border-top:1px solid var(--border-2);margin:20px 0">

    <p class="Card-Title" style="margin-bottom:14px">Ganti Password</p>
    <form method="POST" action="{{ route('profile.password') }}">
        @csrf @method('PATCH')
        <div class="Form-Grid">
            <div class="Form-Group">
                <label class="Form-Label">Password Saat Ini <span>*</span></label>
                <div class="Input-Eye-Wrap">
                    <input type="password" name="current_password" id="pw-current" class="Form-Input" placeholder="Password lama" required>
                    <button type="button" class="Eye-Btn" onclick="togglePw('pw-current',this)"><ion-icon name="eye-outline"></ion-icon></button>
                </div>
            </div>
            <div class="Form-Group">
                <label class="Form-Label">Password Baru <span>*</span></label>
                <div class="Input-Eye-Wrap">
                    <input type="password" name="password" id="pw-new" class="Form-Input" placeholder="Min. 8 karakter" required>
                    <button type="button" class="Eye-Btn" onclick="togglePw('pw-new',this)"><ion-icon name="eye-outline"></ion-icon></button>
                </div>
            </div>
            <div class="Form-Group">
                <label class="Form-Label">Konfirmasi Password <span>*</span></label>
                <div class="Input-Eye-Wrap">
                    <input type="password" name="password_confirmation" id="pw-confirm" class="Form-Input" placeholder="Ulangi password baru" required>
                    <button type="button" class="Eye-Btn" onclick="togglePw('pw-confirm',this)"><ion-icon name="eye-outline"></ion-icon></button>
                </div>
            </div>
        </div>
        <div style="margin-top:14px">
            <button type="submit" class="Btn Btn-Secondary"><ion-icon name="key-outline"></ion-icon>Ganti Password</button>
        </div>
    </form>
</div>

{{-- MODAL TAMBAH --}}
<div class="Modal-Overlay" id="modal-tambah">
    <div class="Modal">
        <div class="Modal-Header">
            <p class="Modal-Title">Tambah Akun Baru</p>
            <button class="Modal-Close" onclick="document.getElementById('modal-tambah').classList.remove('open')"><ion-icon name="close-outline"></ion-icon></button>
        </div>
        <form method="POST" action="{{ route('akun.store') }}">
            @csrf
            <div class="Form-Grid">
                <div class="Form-Group Form-Full"><label class="Form-Label">Nama <span>*</span></label><input type="text" name="name" class="Form-Input" required></div>
                <div class="Form-Group Form-Full"><label class="Form-Label">Email <span>*</span></label><input type="email" name="email" class="Form-Input" required></div>
                <div class="Form-Group"><label class="Form-Label">Password <span>*</span></label><input type="password" name="password" class="Form-Input" required></div>
                <div class="Form-Group"><label class="Form-Label">Role <span>*</span></label>
                    <select name="role" class="Form-Select" required>
                        <option value="admin">Admin</option>
                        <option value="viewer">Viewer</option>
                        @if(auth()->user()->isSuperAdmin())<option value="superadmin">Super Admin</option>@endif
                    </select>
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
            <p class="Modal-Title">Edit Akun</p>
            <button class="Modal-Close" onclick="document.getElementById('modal-edit').classList.remove('open')"><ion-icon name="close-outline"></ion-icon></button>
        </div>
        <form method="POST" id="edit-user-form">
            @csrf @method('PUT')
            <div class="Form-Grid">
                <div class="Form-Group Form-Full"><label class="Form-Label">Nama <span>*</span></label><input type="text" name="name" id="eu-name" class="Form-Input" required></div>
                <div class="Form-Group Form-Full"><label class="Form-Label">Email <span>*</span></label><input type="email" name="email" id="eu-email" class="Form-Input" required></div>
                <div class="Form-Group"><label class="Form-Label">Role <span>*</span></label>
                    <select name="role" id="eu-role" class="Form-Select">
                        <option value="admin">Admin</option>
                        <option value="viewer">Viewer</option>
                        @if(auth()->user()->isSuperAdmin())<option value="superadmin">Super Admin</option>@endif
                    </select>
                </div>
                <div class="Form-Group"><label class="Form-Label">Password Baru <small>(kosongkan jika tidak diubah)</small></label><input type="password" name="password" class="Form-Input"></div>
            </div>
            <div class="Modal-Footer">
                <button type="button" class="Btn Btn-Secondary" onclick="document.getElementById('modal-edit').classList.remove('open')">Batal</button>
                <button type="submit" class="Btn Btn-Primary"><ion-icon name="save-outline"></ion-icon>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.Input-Eye-Wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.Input-Eye-Wrap .Form-Input {
    padding-right: 40px;
    width: 100%;
}
.Eye-Btn {
    position: absolute;
    right: 10px;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-3);
    display: flex;
    align-items: center;
    padding: 0;
    font-size: 1.1rem;
    transition: color .2s;
}
.Eye-Btn:hover {
    color: var(--purple);
}
</style>
@endpush

@push('scripts')
<script>
function openEditUser(id, name, email, role) {
    document.getElementById('edit-user-form').action = '/akun/'+id;
    document.getElementById('eu-name').value  = name;
    document.getElementById('eu-email').value = email;
    document.getElementById('eu-role').value  = role;
    document.getElementById('modal-edit').classList.add('open');
}

function togglePw(inputId, btn) {
    const input = document.getElementById(inputId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.querySelector('ion-icon').setAttribute('name', isHidden ? 'eye-off-outline' : 'eye-outline');
}
</script>
@endpush