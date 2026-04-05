<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PT Arung Laut Nusantara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #c97570;
            --primary-dark: #8b5557;
            --surface: #f8f1ef;
            --ink: #4a2f32;
            --muted: #816568;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(224,161,157,.22), transparent 24%),
                radial-gradient(circle at bottom right, rgba(201,117,112,.18), transparent 26%),
                linear-gradient(180deg, #fbf6f4 0%, #f6eeeb 100%);
        }
        .login-shell {
            width: min(1080px, 100%);
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            border-radius: 34px;
            overflow: hidden;
            background: rgba(255,255,255,.86);
            box-shadow: 0 28px 60px rgba(87,52,57,.12);
            border: 1px solid rgba(129,101,104,.12);
            backdrop-filter: blur(14px);
        }
        .login-side {
            padding: 52px 46px;
            background: linear-gradient(160deg, #8b5557 0%, #c97570 62%, #df9a95 100%);
            color: #fff7f6;
            position: relative;
            overflow: hidden;
        }
        .login-side::after {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            right: -120px;
            bottom: -150px;
            background: radial-gradient(circle, rgba(255,255,255,.2), transparent 70%);
        }
        .login-card {
            padding: 52px 46px;
        }
        .brand-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .form-control {
            width: 100%;
            min-height: 50px;
            border-radius: 16px;
            border: 1px solid rgba(129,101,104,.18);
            padding: 0 16px;
            font: inherit;
            box-shadow: none;
        }
        .form-control:focus {
            outline: none;
            border-color: rgba(201,117,112,.48);
            box-shadow: 0 0 0 4px rgba(201,117,112,.12);
        }
        .btn-primary {
            width: 100%;
            min-height: 52px;
            border-radius: 16px;
            border: 0;
            color: #fff;
            font: inherit;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, #df9a95 100%);
            cursor: pointer;
        }
        .alert {
            border-radius: 18px;
            padding: 14px 16px;
            margin-bottom: 16px;
            background: #fdeaea;
            color: #8b3b3f;
        }
        .alert-success {
            background: #ebf6ef;
            color: #3b6b4e;
        }
        .small-muted { color: var(--muted); font-size: 14px; }
        @media (max-width: 900px) {
            .login-shell { grid-template-columns: 1fr; }
            .login-side { display: none; }
            .login-card { padding: 36px 24px; }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <div class="login-side">
            <div class="brand-chip"><i class="bi bi-qr-code-scan"></i>Attendance Portal</div>
            <h1 style="font-family:'Fraunces',serif;font-size:44px;line-height:1.1;margin:26px 0 14px;">Masuk ke sistem absensi PT Arung Laut Nusantara.</h1>
            <p style="max-width:420px;color:rgba(255,247,246,.8);font-size:16px;line-height:1.7;">Tampilan baru mengikuti struktur proyek referensi, tetapi tetap mempertahankan identitas coral hangat yang lembut untuk Yohanna Project Attendance.</p>
            <div style="display:grid;gap:14px;margin-top:36px;">
                <div class="brand-chip" style="text-transform:none;letter-spacing:0;background:rgba(255,255,255,.1);">QR check-in dan check-out sesuai shift</div>
                <div class="brand-chip" style="text-transform:none;letter-spacing:0;background:rgba(255,255,255,.1);">Dashboard admin, HRD, dan karyawan yang lebih informatif</div>
                <div class="brand-chip" style="text-transform:none;letter-spacing:0;background:rgba(255,255,255,.1);">Filter dan pagination siap untuk data besar</div>
            </div>
        </div>

        <div class="login-card">
            <div class="small-muted">Welcome back</div>
            <h2 style="margin:6px 0 10px;font-size:30px;letter-spacing:-.03em;">Login ke akun Anda</h2>
            <p class="small-muted" style="margin-bottom:28px;">Masuk menggunakan email dan password yang telah terdaftar pada sistem.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" style="display:grid;gap:18px;">
                @csrf
                <div>
                    <label style="display:block;font-weight:700;margin-bottom:8px;">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@perusahaan.com" required autofocus>
                </div>
                <div>
                    <label style="display:block;font-weight:700;margin-bottom:8px;">Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                        <button type="button" id="togglePassword" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:0;color:#816568;cursor:pointer;">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                <label style="display:flex;align-items:center;gap:8px;font-size:14px;color:#816568;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya di perangkat ini
                </label>
                <button type="submit" class="btn-primary">Login</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    </script>
</body>
</html>
