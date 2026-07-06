<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Butterflies — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/butterflies.css') }}">
    <style>
        body { display:flex;align-items:center;justify-content:center;grid-template-columns:1fr; }
        body::before,body::after { content:'';position:fixed;inset:0;pointer-events:none; }
        body::before {
            background:
                radial-gradient(ellipse 70% 60% at 15% 20%,rgba(196,181,253,0.50) 0%,transparent 60%),
                radial-gradient(ellipse 60% 70% at 85% 75%,rgba(167,210,190,0.45) 0%,transparent 60%),
                radial-gradient(ellipse 80% 40% at 78% 8%,rgba(253,230,210,0.45) 0%,transparent 55%),
                linear-gradient(135deg,#e9e4f8 0%,#deeadf 45%,#f0e8e0 100%);
            z-index:0;
        }
        .login-wrap { position:relative;z-index:1;width:100%;max-width:420px;padding:20px; }
        .login-card {
            background:rgba(255,255,255,0.72);backdrop-filter:blur(28px);-webkit-backdrop-filter:blur(28px);
            border-radius:26px;padding:40px 36px;
            border:1.5px solid rgba(255,255,255,0.90);
            box-shadow:0 16px 48px rgba(100,80,160,0.18),inset 0 1px 0 rgba(255,255,255,0.9);
        }
        .login-logo { text-align:center;margin-bottom:28px; }
        .login-logo-icon { width:64px;height:64px;border-radius:20px;background:linear-gradient(135deg,rgba(124,58,237,0.85),rgba(167,139,250,0.80));display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 12px;box-shadow:0 4px 20px rgba(124,58,237,0.3); }
        .login-logo h1 { font-size:1.5rem;font-weight:900;color:var(--text-1);letter-spacing:-.04em; }
        .login-logo p  { font-size:.78rem;color:var(--text-4);margin-top:3px; }
        .login-error { background:rgba(220,38,38,0.12);border:1px solid rgba(220,38,38,0.25);color:var(--red);padding:10px 14px;border-radius:12px;font-size:.8rem;font-weight:600;margin-bottom:16px;backdrop-filter:blur(10px); }

        /* ── Password toggle ── */
        .pw-wrap {
            position:relative;
            display:flex;
            align-items:center;
        }
        .pw-wrap .Form-Input {
            width:100%;
            padding-right:44px;
            box-sizing:border-box;
        }
        .pw-toggle {
            position:absolute;
            right:14px;
            top:50%;
            transform:translateY(-50%);
            background:none;
            border:none;
            cursor:pointer;
            padding:0;
            margin:0;
            display:flex;
            align-items:center;
            justify-content:center;
            color:var(--text-4);
            font-size:1.05rem;
            opacity:0.55;
            transition:opacity 0.15s;
            line-height:1;
            z-index:2;
        }
        .pw-toggle:hover { opacity:1; }
        .pw-toggle:focus { outline:none; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">
            <div class="login-logo-icon" style="padding:4px">
                <img src="{{ asset('images/logo.png') }}" alt="Sahabat Yatim RMJ" style="width:56px;height:56px;border-radius:50%;object-fit:cover;">
            </div>
            <h1>Butterflies</h1>
            <p>Panel Admin · Yayasan Sahabat Yatim</p>
        </div>

        @if($errors->any())
        <div class="login-error">
            <ion-icon name="alert-circle-outline"></ion-icon>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="Form-Group" style="margin-bottom:14px">
                <label class="Form-Label">Email <span>*</span></label>
                <input type="email" name="email" class="Form-Input" value="{{ old('email') }}" placeholder="admin@sayat.id" required autofocus>
            </div>
            <div class="Form-Group" style="margin-bottom:20px">
                <label class="Form-Label">Password <span>*</span></label>
                <div class="pw-wrap">
                    <input type="password" name="password" id="password" class="Form-Input" placeholder="••••••••" required>
                    <button type="button" class="pw-toggle" onclick="togglePassword()" id="pw-toggle-btn" aria-label="Tampilkan password">
                        <ion-icon name="eye-outline" id="pw-icon"></ion-icon>
                    </button>
                </div>
            </div>

            <button type="submit" class="Btn Btn-Primary" style="width:100%;justify-content:center;padding:11px;font-size:.9rem">
                <ion-icon name="log-in-outline"></ion-icon>Masuk ke Panel
            </button>
        </form>

        <p style="text-align:center;font-size:.72rem;color:var(--text-4);margin-top:20px">
            Default: admin@sayat.id · sayat@2025!
        </p>
    </div>
</div>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
    function togglePassword() {
        const input    = document.getElementById('password');   
        const icon     = document.getElementById('pw-icon');
        const btn      = document.getElementById('pw-toggle-btn');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.setAttribute('name', isHidden ? 'eye-off-outline' : 'eye-outline');
        btn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
    }
</script>
</body>
</html>