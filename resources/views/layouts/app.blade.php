<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Butterflies — @yield('title','Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/butterflies.css') }}">
    @stack('styles')
</head>
<body>
    
<div class="mobile-warning">
    Sistem ini dirancang untuk penggunaan desktop atau laptop.<br><br>
    Silakan akses menggunakan perangkat dengan resolusi layar yang lebih besar.
</div>

{{-- ══ NAV ══ --}}
<nav>
    <div class="Nav-Container">
        <a href="{{ route('dashboard') }}" class="Nav-Logo">
            <div class="Nav-Logo-Img">
                <img src="{{ asset('images/logo.png') }}" alt="Sahabat Yatim RMJ" style="width:42px;height:42px;border-radius:50%;object-fit:cover;">
            </div>
            <div class="Nav-Logo-Text">
                <span class="brand-name">Butterflies</span>
                <span class="brand-sub">Panel Admin</span>
            </div>
        </a>
        <div class="Nav-Links">
            <div class="Nav-Section">
                <p class="Section-Label">ACCOUNT</p>
                <a href="{{ route('akun.index') }}" class="Nav-Item {{ request()->routeIs('akun.*','profile.*') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="person-circle-outline"></ion-icon></div>
                    <span>Kelola Akun</span>
                </a>
            </div>
            <div class="Nav-Section">
                <p class="Section-Label">HOME</p>
                <a href="{{ route('dashboard') }}" class="Nav-Item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="planet-outline"></ion-icon></div>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="Nav-Section">
                <p class="Section-Label">DATA</p>
                <a href="{{ route('calon-penerima.index') }}" class="Nav-Item {{ request()->routeIs('calon-penerima.*') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="people-outline"></ion-icon></div>
                    <span>Data Calon Penerima</span>
                </a>
                <a href="{{ route('penilaian.index') }}" class="Nav-Item {{ request()->routeIs('penilaian.*') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="document-outline"></ion-icon></div>
                    <span>Data Penilaian</span>
                </a>
            </div>
            <div class="Nav-Section">
                <p class="Section-Label">SPK</p>
                <a href="{{ route('kriteria.index') }}" class="Nav-Item {{ request()->routeIs('kriteria.*') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="book-outline"></ion-icon></div>
                    <span>Bobot & Kriteria</span>
                </a>
                <a href="{{ route('saw.index') }}" class="Nav-Item {{ request()->routeIs('saw.*') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="paper-plane-outline"></ion-icon></div>
                    <span>Proses SAW</span>
                </a>
                <a href="{{ route('ranking.index') }}" class="Nav-Item {{ request()->routeIs('ranking.*') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="stats-chart-outline"></ion-icon></div>
                    <span>Hasil Ranking</span>
                </a>
                <a href="{{ route('laporan.index') }}" class="Nav-Item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="print-outline"></ion-icon></div>
                    <span>Cetak Laporan</span>
                </a>
            </div>
        </div>
        <div class="Nav-Footer">
            <p class="Section-Label">OTHERS</p>
            <a href="{{ route('settings.index') }}" class="Nav-Item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <div class="Nav-Icon"><ion-icon name="settings-outline"></ion-icon></div>
                <span>Settings</span>
            </a>
            {{-- Tambahkan ini --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="Nav-Item" style="width:100%;background:rgba(224,92,92,0.10);border:1px solid rgba(224,92,92,0.20);color:var(--red-l);margin-top:6px">
                    <div class="Nav-Icon"><ion-icon name="log-out-outline"></ion-icon></div>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- ══ MAIN ══ --}}
<main>
    <div class="Topbar">
        <div class="Topbar-Left">
            <h1 class="Page-Title">@yield('page-title','Dashboard')</h1>
            <p class="Page-Sub">@yield('page-sub','Selamat datang 👋')</p>
        </div>
        <div class="Topbar-Right">
            <div class="Search-Bar">
                <ion-icon name="search-outline"></ion-icon>
                <input type="text" placeholder="Cari nama, kode...">
            </div>
            {{-- User pill --}}
            <div style="display:flex;align-items:center;gap:8px;padding:6px 13px;background:rgba(255,255,255,0.60);backdrop-filter:blur(10px);border:1.5px solid rgba(255,255,255,0.65);border-radius:50px;cursor:pointer;box-shadow:0 2px 10px rgba(100,80,160,0.08)" onclick="document.getElementById('logout-form').submit()">
                <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a78bfa);display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800;color:#fff">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <span style="font-size:.8rem;font-weight:600;color:var(--text-2)">{{ Auth::user()->name ?? 'Admin' }}</span>
                <ion-icon name="log-out-outline" style="font-size:.9rem;color:var(--text-4)"></ion-icon>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" hidden>@csrf</form>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="Alert success" style="margin:14px 22px 0" x-data x-init="setTimeout(()=>$el.remove(),4000)">
        <ion-icon name="checkmark-circle-outline"></ion-icon>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="Alert error" style="margin:14px 22px 0">
        <ion-icon name="alert-circle-outline"></ion-icon>{{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="Alert error" style="margin:14px 22px 0">
        <ion-icon name="alert-circle-outline"></ion-icon>
        <div>@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
    </div>
    @endif

    <div class="Page-Content">
        @yield('content')
    </div>
</main>

{{-- ══ ASIDE ══ --}}
<aside>
    @hasSection('aside')
        @yield('aside')
    @else
        {{-- Default aside --}}
        <div class="Aside-Top">
            <div class="Aside-Period">
                <ion-icon name="calendar-outline"></ion-icon>
                <div>
                    <p class="Aside-Period-Label">Periode Aktif</p>
                    <p class="Aside-Period-Val">{{ \App\Models\Setting::get('periode_aktif','2025/2026') }}</p>
                </div>
            </div>
            @php
                $periodeAktif = \App\Models\Setting::get('periode_aktif','2025/2026');
                $totalCalon   = \App\Models\CalonPenerima::where('periode',$periodeAktif)->count();
                $totalLayak   = \App\Models\Penilaian::where('periode',$periodeAktif)->where('status_kelayakan','layak')->count();
                $totalTidak   = \App\Models\Penilaian::where('periode',$periodeAktif)->where('status_kelayakan','tidak_layak')->count();
            @endphp
            <div class="Aside-Stats-Mini">
                <div class="Aside-Mini-Item"><span class="Aside-Mini-Val">{{ $totalCalon }}</span><span class="Aside-Mini-Label">Calon</span></div>
                <div class="Aside-Mini-Sep"></div>
                <div class="Aside-Mini-Item"><span class="Aside-Mini-Val" style="color:#34d399">{{ $totalLayak }}</span><span class="Aside-Mini-Label">Layak</span></div>
                <div class="Aside-Mini-Sep"></div>
                <div class="Aside-Mini-Item"><span class="Aside-Mini-Val" style="color:#f87171">{{ $totalTidak }}</span><span class="Aside-Mini-Label">Tidak</span></div>
            </div>
        </div>
        <div class="Aside-Section">
            <div class="Aside-Section-Header">
                <h4>Top Penerima</h4>
                <a href="{{ route('ranking.index') }}" class="See-All">Semua <ion-icon name="chevron-forward-outline"></ion-icon></a>
            </div>
            @php
                $topColors = ['purple-card','orange-card','green-card'];
                $topPenerima = \App\Models\Penilaian::with('calonPenerima')
                    ->where('periode',$periodeAktif)->where('status_kelayakan','layak')
                    ->orderBy('ranking')->take(3)->get();
            @endphp
            @forelse($topPenerima as $i => $p)
            <div class="Top-Card {{ $topColors[$i] ?? 'purple-card' }}">
                <span class="Top-Num">{{ $p->ranking }}</span>
                <div class="Top-Info">
                    <p class="Top-Name">{{ $p->calonPenerima->nama }}</p>
                    <p class="Top-Score">Vi = {{ number_format($p->skor_akhir,3) }}</p>
                </div>
            </div>
            @empty
            <p style="font-size:.76rem;color:var(--text-4);text-align:center;padding:12px 0">Belum ada data ranking.</p>
            @endforelse
        </div>
        {{-- Chatbox --}}
        <div class="Chatbox">
            <div class="Chat-Header">
                <div class="Chat-Header-Left">
                    <div class="Chat-Avatar">🤖</div>
                    <div><p class="Chat-Name">Asisten SPK</p><p class="Chat-Status"><span class="Online-Dot"></span>Online</p></div>
                </div>
            </div>
            <div class="Chat-Messages" id="chat-messages">
                <div class="Msg Bot"><div class="Msg-Bubble">Halo! Aku asisten SPK Butterflies 🦋 Tanya tentang ranking, bobot, atau calon!</div><p class="Msg-Time">{{ now()->format('H:i') }}</p></div>
                <div class="Quick-Replies" id="quick-replies">
                    <button class="Quick-Btn" onclick="sendQuick('Siapa ranking 1?')">Ranking 1?</button>
                    <button class="Quick-Btn" onclick="sendQuick('Total calon?')">Total calon?</button>
                    <button class="Quick-Btn" onclick="sendQuick('Bobot C1?')">Bobot C1?</button>
                    <button class="Quick-Btn" onclick="sendQuick('Metode SAW?')">Metode?</button>
                </div>
            </div>
            <div class="Chat-Input-Area">
                <input type="text" id="chat-input" class="Chat-Input" placeholder="Tanya tentang SPK..." onkeydown="if(event.key==='Enter')sendChat()">
                <button class="Chat-Send-Btn" onclick="sendChat()"><ion-icon name="send-outline"></ion-icon></button>
            </div>
        </div>
    @endif
</aside>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="{{ asset('js/butterflies-chat.js') }}"></script>
@stack('scripts')
</body>
</html>
