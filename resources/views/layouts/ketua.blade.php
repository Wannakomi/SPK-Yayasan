<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Ketua — @yield('title','Butterflies')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/butterflies.css') }}">
    <style>
        /* Override body grid for ketua layout (no aside) */
        body {
            grid-template-columns: 220px 1fr !important;
        }
        /* Ketua-specific nav tweaks */
        .ketua-badge {
            display:inline-flex;align-items:center;gap:5px;
            padding:4px 11px;border-radius:50px;
            background:rgba(201,168,76,0.18);
            color:var(--gold-l);
            border:1px solid rgba(201,168,76,0.30);
            font-size:.7rem;font-weight:700;
            backdrop-filter:blur(10px);
        }
        .readonly-notice {
            display:flex;align-items:center;gap:8px;
            padding:8px 14px;
            background:rgba(201,168,76,0.08);
            border:1px solid rgba(201,168,76,0.20);
            border-radius:var(--r-md);
            font-size:.75rem;color:var(--gold);
            margin-bottom:16px;
        }
        .readonly-notice ion-icon { font-size:1rem;flex-shrink:0; }
        /* Info sidebar box */
        .info-sidebar {
            background:rgba(212, 188, 234, 0.7);
            backdrop-filter:blur(18px);
            border-radius:var(--r-lg);
            border:1px solid var(--border);
            padding:16px;
            margin-top:10px;
        }
        .info-sidebar-title {
            font-size:.62rem;font-weight:700;text-transform:uppercase;
            letter-spacing:.1em;color:var(--text-4);margin-bottom:12px;
        }
        .info-row {
            display:flex;align-items:center;justify-content:space-between;
            padding:7px 0;border-bottom:1px solid var(--border-2);
            font-size:.78rem;
        }
        .info-row:last-child { border-bottom:none; }
        .info-row-label { color:var(--text-4); }
        .info-row-val   { font-weight:700;color:var(--text-1); }
    </style>
    @stack('styles')
</head>
<body>
<div class="mobile-warning">
    Sistem ini dirancang untuk penggunaan desktop atau laptop.<br><br>
    Silakan akses menggunakan perangkat dengan resolusi layar yang lebih besar.
</div>
{{-- ══ NAV KETUA ══ --}}
<nav>
    <div class="Nav-Container">
        {{-- Logo --}}
        <div class="Nav-Logo" style="cursor:default">
            <div class="Nav-Logo-Img">
                <img src="{{ asset('images/logo.png') }}" alt="Sahabat Yatim RMJ" style="width:42px;height:42px;border-radius:50%;object-fit:cover;">
            </div>
            <div class="Nav-Logo-Text">
                <span class="brand-name" style="font-size:.85rem">Portal Ketua</span>
                <span class="brand-sub">Yayasan Sahabat Yatim</span>
            </div>
        </div>

        {{-- Nav items --}}
        <div class="Nav-Links">
            <div class="Nav-Section">
                <p class="Section-Label">MENU</p>
                <a href="{{ route('ketua.ranking') }}" class="Nav-Item {{ request()->routeIs('ketua.ranking') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="stats-chart-outline"></ion-icon></div>
                    <span>Ranking Calon</span>
                </a>
                <a href="{{ route('ketua.laporan') }}" class="Nav-Item {{ request()->routeIs('ketua.laporan') ? 'active' : '' }}">
                    <div class="Nav-Icon"><ion-icon name="document-text-outline"></ion-icon></div>
                    <span>Unduh Laporan</span>
                </a>
            </div>
        </div>

        {{-- Info sidebar box --}}
        <div class="info-sidebar">
            <p class="info-sidebar-title">Info Sistem</p>
            @php
                $p = \App\Models\Setting::get('periode_aktif','2025/2026');
                $tc = \App\Models\CalonPenerima::where('periode',$p)->count();
                $tl = \App\Models\Penilaian::where('periode',$p)->where('status_kelayakan','layak')->count();
            @endphp
            <div class="info-row"><span class="info-row-label">📅 Periode</span><span class="info-row-val">{{ $p }}</span></div>
            <div class="info-row"><span class="info-row-label">👥 Total Calon</span><span class="info-row-val" style="color:var(--gold-l)">{{ $tc }}</span></div>
            <div class="info-row"><span class="info-row-label">✅ Penerima</span><span class="info-row-val" style="color:var(--green)">{{ $tl }}</span></div>
            <div class="info-row"><span class="info-row-label">📊 Metode</span><span class="info-row-val">SAW</span></div>
        </div>

        {{-- Footer --}}
        <div class="Nav-Footer" style="margin-top:14px">

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="Nav-Item" style="width:100%;background:rgba(224,92,92,0.10);border-color:rgba(224,92,92,0.20);color:var(--red-l)">
                    <div class="Nav-Icon"><ion-icon name="log-out-outline"></ion-icon></div>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- ══ MAIN ══ --}}
<main>
    {{-- Topbar --}}
    <div class="Topbar">
        <div class="Topbar-Left">
            <h1 class="Page-Title">@yield('page-title')</h1>
            <p class="Page-Sub">@yield('page-sub')</p>
        </div>
        <div class="Topbar-Right">

            {{-- User info --}}
            <div style="display:flex;align-items:center;gap:8px;padding:6px 13px;background:rgba(255,255,255,0.05);border:1px solid var(--border-4);border-radius:50px">
                <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-l));display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800;color:#1a1200">
                    {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
                </div>
                <span style="font-size:.8rem;font-weight:600;color:var(--text-2)">{{ Auth::user()->name ?? 'Ketua' }}</span>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="Alert success" style="margin:14px 22px 0"><ion-icon name="checkmark-circle-outline"></ion-icon>{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="Alert error" style="margin:14px 22px 0"><ion-icon name="alert-circle-outline"></ion-icon>{{ session('error') }}</div>
    @endif

    <div class="Page-Content">
        {{-- Read-only notice --}}
        <div class="readonly-notice">
            <ion-icon name="eye-outline"></ion-icon>
            <span>Portal ini bersifat <strong>read-only</strong>. Untuk mengubah data, hubungi Admin Yayasan.</span>
        </div>

        @yield('content')
    </div>
</main>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@stack('scripts')
</body>
</html>
