<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistem Absensi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --ink: #14171F;
            --ink-2: #21252F;
            --amber: #DB9A3D;
            --teal: #0D6E5A;
            --teal-soft: #E1F1EB;
            --muted: #6C6F7C;
            --line: #E6E2D6;
            --mono: 'IBM Plex Mono', ui-monospace, monospace;
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 85% 12%, rgba(219,154,61,.16) 0%, transparent 45%),
                linear-gradient(160deg, var(--ink) 0%, var(--ink-2) 55%, #0B0D12 100%);
        }
        h1, h2, h3, h4, h5, h6 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; }
        .login-card { border-radius: 1.3rem; border: none; box-shadow: 0 25px 70px rgba(0,0,0,.4); }
        .brand-mark {
            width: 50px; height: 50px; border-radius: 13px; margin: 0 auto;
            background: var(--ink);
            display: flex; align-items: center; justify-content: center; color: var(--amber); font-size: 1.4rem;
            box-shadow: inset 0 0 0 1px rgba(219,154,61,.35);
        }
        .login-clock {
            font-family: var(--mono); font-size: .78rem; letter-spacing: .05em; color: rgba(255,255,255,.55);
            text-align: center; margin-bottom: 1.6rem;
        }
        .form-control { border-radius: .6rem; border-color: var(--line); padding: .6rem .9rem; }
        .form-control:focus { border-color: var(--ink); box-shadow: 0 0 0 .2rem var(--teal-soft); }
        .form-label { font-weight: 700; font-size: .8rem; }
        .btn-primary { background: var(--ink); border-color: var(--ink); border-radius: .65rem; font-weight: 700; padding: .6rem; }
        .btn-primary:hover, .btn-primary:active { background: var(--ink-2) !important; border-color: var(--ink-2) !important; color: var(--amber) !important; }
        .demo-hint { background: var(--teal-soft); border-radius: .75rem; font-size: .78rem; color: #0A4F42; }
    </style>
</head>
<body>
<div class="container">
    <div class="login-clock font-mono" id="loginClock"></div>
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="card login-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="brand-mark mb-3"><i class="bi bi-qr-code-scan"></i></div>
                    <h4 class="mb-1">Sistem Absensi</h4>
                    <small class="text-muted">Masuk untuk melanjutkan</small>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus placeholder="nama@perusahaan.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                    <div class="form-check mb-4">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label small" for="remember">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        var el = document.getElementById('loginClock');
        function tick() {
            var d = new Date();
            el.textContent = d.toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }) + '  ·  ' + d.toLocaleTimeString('id-ID', { hour12: false });
        }
        tick();
        setInterval(tick, 1000);
    })();
</script>
</body>
</html>
