<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Absensi') | Sistem Absensi</title>
    <script>
        // Terapkan tema tersimpan sedini mungkin agar tidak ada "kedipan" tema saat halaman dimuat
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = stored || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', theme);
            } catch (e) {}
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --ink: #14171F;
            --ink-2: #21252F;
            --teal: #0D6E5A;
            --teal-dark: #0A4F42;
            --teal-soft: #E1F1EB;
            --amber: #DB9A3D;
            --amber-ink: #7A4E10;
            --amber-soft: #FBF0DC;
            --coral: #DD4B51;
            --coral-soft: #FCE7E8;
            --mist: #F3F1EA;
            --line: #E6E2D6;
            --muted: #6C6F7C;
            --mono: 'IBM Plex Mono', ui-monospace, monospace;
            --display: 'Space Grotesk', sans-serif;
            --surface: #fff;
            /* Warna "ink" tetap: dipakai untuk permukaan aksen gelap (sidebar, brand-mark,
               bottom-nav, tombol utama) yang sengaja tetap gelap di kedua mode tampilan. */
            --ink-fixed: #14171F;
            --ink-fixed-2: #21252F;
            /* Token tambahan: bayangan & warna info — nilai sama seperti sebelumnya,
               hanya dijadikan variabel agar bisa dipakai ulang & konsisten di kedua tema. */
            --info: #2F5FA3;
            --info-soft: #E4ECF7;
            --shadow-1: rgba(20,23,31,.04);
            --shadow-2: rgba(20,23,31,.06);
            --shadow-3: rgba(20,23,31,.16);
            --shadow-4: rgba(20,23,31,.22);
            --shadow-5: rgba(20,23,31,.25);
            --focus-ring: rgba(219,154,61,.45);
        }

        /* ---------- Dark mode ---------- */
        [data-bs-theme="dark"] {
            --ink: #E9EAF1;
            --mist: #171A22;
            --line: #2E3240;
            --muted: #9BA1B4;
            --surface: #1E212B;
            --shadow-1: rgba(0,0,0,.35);
            --shadow-2: rgba(0,0,0,.4);
            --shadow-3: rgba(0,0,0,.45);
            --shadow-4: rgba(0,0,0,.5);
            --shadow-5: rgba(0,0,0,.55);
        }
        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus { box-shadow: 0 0 0 .2rem rgba(255,255,255,.06); }
        [data-bs-theme="dark"] .badge.bg-light.text-dark {
            background: var(--mist) !important; color: var(--ink) !important; border-color: var(--line) !important;
        }
        [data-bs-theme="dark"] .topbar::after { opacity: .35; }
        [data-bs-theme="dark"] .topbar { box-shadow: 0 1px 0 rgba(255,255,255,.04); }
        [data-bs-theme="dark"] .sidebar { border-right-color: rgba(255,255,255,.06) !important; box-shadow: 1px 0 0 rgba(255,255,255,.03); }
        [data-bs-theme="dark"] .bottom-nav { border-top-color: rgba(255,255,255,.08); box-shadow: 0 -8px 24px -12px rgba(0,0,0,.6); }
        [data-bs-theme="dark"] .scan-fab { border-color: var(--mist); }
        [data-bs-theme="dark"] ::selection { background: rgba(219,154,61,.35); }

        /* ---------- Transisi halus saat ganti tema ---------- */
        body, .topbar, .sidebar, .bottom-nav, .card, .table, .form-control, .form-select,
        .badge, .alert, .dropdown-menu, .modal-content, .offcanvas, .navbar-collapse,
        .notif-menu, .toast-pop, .more-menu-item, .more-menu-ico, .icon-btn {
            transition: background-color .2s ease, border-color .2s ease, color .2s ease, box-shadow .2s ease;
        }

        /* ---------- Scrollbar modern ---------- */
        * { scrollbar-width: thin; scrollbar-color: var(--line) transparent; }
        *::-webkit-scrollbar { width: 8px; height: 8px; }
        *::-webkit-scrollbar-track { background: transparent; }
        *::-webkit-scrollbar-thumb { background-color: var(--line); border-radius: 50rem; }
        *::-webkit-scrollbar-thumb:hover { background-color: var(--muted); }

        /* ---------- Focus visible (aksesibilitas, konsisten dgn brand) ---------- */
        .sidebar .nav-link:focus-visible,
        .bottom-nav a:focus-visible,
        .topbar .icon-btn:focus-visible,
        .more-menu-item:focus-visible,
        .mobile-menu-link:focus-visible {
            outline: 2px solid var(--amber); outline-offset: 2px;
        }

        * { -webkit-tap-highlight-color: transparent; }
        html, body { height: 100%; }
        body {
            background-color: var(--mist);
            font-family: 'Inter', -apple-system, sans-serif;
            color: var(--ink);
        }
        h1, h2, h3, h4, h5, h6 { font-family: var(--display); font-weight: 700; letter-spacing: -0.01em; }
        a { text-decoration: none; }
        .font-mono { font-family: var(--mono); }

        /* ---------- Top bar ---------- */
        .topbar { background: var(--surface); border-bottom: 1px solid var(--line); position: relative; }
        .topbar::after {
            content: ''; position: absolute; left: 0; right: 0; bottom: -1px; height: 2px;
            background: linear-gradient(90deg, var(--amber) 0%, var(--teal) 45%, transparent 45%);
            opacity: .55;
        }
        .topbar .navbar-brand {
            color: var(--ink); font-family: var(--display); font-weight: 700;
            display: flex; align-items: center; gap: .65rem; font-size: 1.05rem;
        }
        .brand-mark {
            width: 38px; height: 38px; border-radius: 10px; flex: 0 0 auto;
            background: var(--ink-fixed);
            display: flex; align-items: center; justify-content: center;
            color: var(--amber); font-size: 1.05rem;
            box-shadow: inset 0 0 0 1px rgba(219,154,61,.35);
        }
        .topbar .live-clock {
            font-family: var(--mono); font-size: .82rem; font-weight: 600; color: var(--muted);
            letter-spacing: .02em; display: flex; align-items: center; gap: .4rem;
            padding: .35rem .7rem; border: 1px solid var(--line); border-radius: .6rem;
        }
        .topbar .live-clock i { color: var(--amber); font-size: .85rem; }
        .topbar .icon-btn {
            width: 40px; height: 40px; border-radius: .8rem; color: var(--muted);
            display: flex; align-items: center; justify-content: center; font-size: 1.15rem;
            position: relative; background: transparent;
        }
        .topbar .icon-btn:hover { background: var(--mist); color: var(--ink); }
        .notif-dot {
            position: absolute; top: 5px; right: 5px; min-width: 16px; height: 16px; padding: 0 3px;
            border-radius: 50rem; background: var(--coral); color: #fff; font-size: .62rem;
            display: flex; align-items: center; justify-content: center; font-weight: 700; line-height: 1;
        }
        .user-chip { display: flex; align-items: center; gap: .5rem; color: var(--ink); font-weight: 600; font-size: .9rem; }
        .role-pill {
            font-size: .66rem; font-weight: 700; padding: .2rem .55rem; border-radius: .4rem;
            background: var(--ink-fixed); color: var(--amber); text-transform: uppercase; letter-spacing: .05em;
            font-family: var(--mono);
        }

        /* ---------- Sidebar (desktop rail) ---------- */
        .sidebar { background: var(--ink-fixed); color: rgba(255,255,255,.6); border-right: 1px solid var(--ink-fixed) !important; }
        .sidebar .side-eyebrow {
            font-family: var(--mono); font-size: .66rem; text-transform: uppercase; letter-spacing: .12em;
            color: rgba(255,255,255,.35); padding: .3rem .9rem 1rem;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.62); font-weight: 600; font-size: .9rem;
            border-radius: .6rem; padding: .62rem .9rem; display: flex; align-items: center; gap: .75rem;
            border-left: 3px solid transparent; margin-bottom: .2rem; transition: background .15s ease, color .15s ease;
        }
        .sidebar .nav-link i { font-size: 1.05rem; width: 1.25rem; text-align: center; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(219,154,61,.12); color: #fff; border-left-color: var(--amber);
        }
        .sidebar .nav-link .badge { font-family: var(--mono); }

        /* ---------- Cards ---------- */
        .card { border: 1px solid var(--line); border-radius: 1rem; box-shadow: 0 1px 2px var(--shadow-1); }

        /* ---------- Buttons ---------- */
        .btn {
            border-radius: .65rem; font-weight: 600; font-size: .92rem;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease,
                        box-shadow .15s ease, transform .12s ease;
        }
        .btn:active:not(:disabled) { transform: translateY(1px); }
        .btn:disabled, .btn.disabled { opacity: .55; box-shadow: none; transform: none; }
        .btn-primary {
            background: var(--ink-fixed); border-color: var(--ink-fixed); color: #fff;
            --bs-btn-focus-shadow-rgb: 20,23,31;
        }
        .btn-primary:hover { box-shadow: 0 6px 16px -6px rgba(20,23,31,.35); }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active { background: var(--ink-fixed-2) !important; border-color: var(--ink-fixed-2) !important; color: var(--amber) !important; }
        .btn-outline-primary { color: var(--ink); border-color: var(--line); --bs-btn-focus-shadow-rgb: 20,23,31; }
        .btn-outline-primary:hover { background: var(--ink-fixed); border-color: var(--ink-fixed); color: #fff; }
        .btn-outline-secondary { border-color: var(--line); color: var(--muted); }
        .btn-outline-secondary:hover { background: var(--mist); color: var(--ink); border-color: var(--line); }
        .btn-outline-danger { color: var(--coral); border-color: #f3c6c8; --bs-btn-focus-shadow-rgb: 221,75,81; }
        .btn-outline-danger:hover { background: var(--coral); border-color: var(--coral); color: #fff; box-shadow: 0 6px 16px -6px rgba(221,75,81,.3); }
        .btn-outline-success { color: var(--teal-dark); border-color: #bfe3d8; --bs-btn-focus-shadow-rgb: 13,110,90; }
        .btn-outline-success:hover { background: var(--teal); border-color: var(--teal); color: #fff; box-shadow: 0 6px 16px -6px rgba(13,110,90,.3); }

        /* ---------- Forms ---------- */
        .form-control, .form-select { border-radius: .6rem; border-color: var(--line); padding: .55rem .8rem; font-size: .92rem; }
        .form-control:focus, .form-select:focus { border-color: var(--ink); box-shadow: 0 0 0 .2rem var(--mist); }
        .form-label { font-weight: 700; font-size: .8rem; color: var(--ink); }

        /* ---------- Tables ---------- */
        .table { --bs-table-hover-bg: var(--mist); font-size: .92rem; }
        .table thead th {
            font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted);
            border-bottom-width: 1px; font-weight: 700; background: transparent; font-family: var(--mono);
        }
        .table td, .table th { vertical-align: middle; border-color: var(--line); }

        /* ---------- Status badges (soft pill) ---------- */
        .badge { font-weight: 700; border-radius: .5rem; padding: .42em .75em; font-size: .7rem; letter-spacing: .01em; }
        .badge.bg-success { background: var(--teal-soft) !important; color: var(--teal-dark) !important; }
        .badge.bg-warning { background: var(--amber-soft) !important; color: var(--amber-ink) !important; }
        .badge.bg-danger { background: var(--coral-soft) !important; color: var(--coral) !important; }
        .badge.bg-info { background: var(--info-soft) !important; color: var(--info) !important; }
        .badge.bg-secondary { background: var(--mist) !important; color: var(--muted) !important; }

        /* ---------- Status timeline (riwayat pengajuan) ---------- */
        .status-timeline { list-style: none; margin: 0; padding: 0; }
        .status-timeline li { position: relative; padding: 0 0 1.35rem 2.4rem; }
        .status-timeline li:last-child { padding-bottom: 0; }
        .status-timeline li::before {
            content: ''; position: absolute; left: .6rem; top: 1.6rem; bottom: -.2rem; width: 2px;
            background: var(--line);
        }
        .status-timeline li:last-child::before { display: none; }
        .status-timeline .tl-dot {
            position: absolute; left: 0; top: 0; width: 1.3rem; height: 1.3rem; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: .68rem;
            background: var(--mist); color: var(--muted); border: 2px solid var(--surface);
            box-shadow: 0 0 0 1px var(--line);
        }
        .status-timeline li.is-approved .tl-dot { background: var(--teal-soft); color: var(--teal-dark); box-shadow: 0 0 0 1px var(--teal-soft); }
        .status-timeline li.is-processing .tl-dot { background: var(--info-soft); color: var(--info); box-shadow: 0 0 0 1px var(--info-soft); }
        .status-timeline li.is-rejected .tl-dot { background: var(--coral-soft); color: var(--coral); box-shadow: 0 0 0 1px var(--coral-soft); }
        .status-timeline li.is-pending .tl-dot { background: var(--amber-soft); color: var(--amber-ink); box-shadow: 0 0 0 1px var(--amber-soft); }
        .status-timeline .tl-title { font-weight: 700; font-size: .88rem; color: var(--ink); }
        .status-timeline .tl-meta { font-size: .74rem; color: var(--muted); font-family: var(--mono); margin-top: .1rem; }
        .status-timeline .tl-note { font-size: .8rem; color: var(--muted); margin-top: .35rem; background: var(--mist); border-radius: .6rem; padding: .5rem .7rem; }
        .status-timeline li.is-future .tl-title { color: var(--muted); }
        .status-timeline li.is-future .tl-dot { opacity: .5; }

        /* ---------- Document cards (dokumen karyawan) ---------- */
        .doc-card {
            border: 1px solid var(--line); border-radius: .9rem; padding: 1rem; height: 100%;
            display: flex; flex-direction: column; gap: .5rem;
        }
        .doc-card .doc-ico {
            width: 40px; height: 40px; border-radius: .7rem; background: var(--mist); color: var(--ink);
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex: 0 0 auto;
        }
        .doc-empty-slot {
            border: 1.5px dashed var(--line); border-radius: .9rem; padding: 1rem; height: 100%;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .4rem;
            color: var(--muted); text-align: center;
        }

        /* ---------- Alerts ---------- */
        .alert { border-radius: .8rem; border: 1px solid transparent; font-size: .9rem; }
        .alert-success { background: var(--teal-soft); color: var(--teal-dark); }
        .alert-danger { background: var(--coral-soft); color: var(--coral); }
        .alert-warning { background: var(--amber-soft); color: var(--amber-ink); }

        /* ---------- Bottom mobile nav ---------- */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 1030;
            background: var(--ink-fixed); border-top: 1px solid var(--ink-fixed);
            display: flex; align-items: center; justify-content: space-around;
            padding: .35rem .5rem calc(.35rem + env(safe-area-inset-bottom));
        }
        .bottom-nav a {
            color: rgba(255,255,255,.55); font-size: .64rem; text-align: center; flex: 1; padding: .25rem .1rem;
            display: flex; flex-direction: column; align-items: center; gap: .15rem; font-weight: 700;
        }
        .bottom-nav a i { font-size: 1.2rem; }
        .bottom-nav a.active { color: var(--amber); }
        .bottom-nav .scan-fab-wrap { flex: 0 0 auto; width: 64px; }
        .bottom-nav .scan-fab {
            width: 54px; height: 54px; border-radius: 50%; margin: -30px auto 0;
            background: var(--amber); color: var(--ink-fixed);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; box-shadow: 0 10px 22px rgba(219,154,61,.45);
            border: 4px solid var(--mist);
        }
        .bottom-nav .scan-label { color: var(--amber); margin-top: .15rem; }

        @media (max-width: 767.98px) {
            body { padding-bottom: 0; }
            /* Spacer block eksplisit di akhir setiap halaman: menjamin ada
               ruang kosong sebelum bottom-nav, apa pun struktur konten
               halamannya (tidak bergantung pada margin elemen terakhir). */
            .mobile-nav-spacer { display: block; height: 96px; width: 100%; }
        }

        @media print {
            .bottom-nav, .topbar { display: none !important; }
        }

        /* ---------- More menu (mobile offcanvas) ---------- */
        .more-menu-offcanvas {
            border-top-left-radius: 1.2rem; border-top-right-radius: 1.2rem;
            height: auto !important; max-height: calc(100vh - 96px);
            box-shadow: 0 -12px 32px -8px var(--shadow-5);
            border: 1px solid var(--line); border-bottom: 0;
        }
        .more-menu-offcanvas::before {
            content: ''; position: absolute; top: .55rem; left: 50%; transform: translateX(-50%);
            width: 36px; height: 4px; border-radius: 50rem; background: var(--line);
        }
        .more-menu-offcanvas .offcanvas-header { border-bottom: 1px solid var(--line); padding: 1.15rem 1.1rem .7rem; }
        .more-menu-offcanvas .offcanvas-title { font-family: var(--display); font-size: .96rem; }
        .more-menu-offcanvas .offcanvas-body { padding: .9rem 1.1rem calc(1.1rem + 82px + env(safe-area-inset-bottom)); overflow-y: visible; }
        .more-menu-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: .6rem; }
        .more-menu-item {
            position: relative; display: flex; flex-direction: column; align-items: center; gap: .4rem;
            padding: .8rem .3rem; border-radius: .9rem; background: var(--mist); color: var(--ink);
            font-size: .74rem; font-weight: 600; text-align: center; border: 1px solid var(--line);
            transition: transform .15s ease, background .15s ease;
        }
        .more-menu-item:active { transform: scale(.94); }
        .more-menu-item.active { background: var(--ink-fixed); color: #fff; border-color: var(--ink-fixed); }
        .more-menu-item.active .more-menu-ico { background: rgba(219,154,61,.18); color: var(--amber); }
        .more-menu-ico {
            width: 38px; height: 38px; border-radius: .7rem; background: var(--surface); color: var(--ink);
            display: flex; align-items: center; justify-content: center; font-size: 1.05rem;
            box-shadow: 0 1px 2px var(--shadow-2);
        }
        .more-menu-badge {
            position: absolute; top: .3rem; right: .3rem; background: var(--amber); color: var(--ink-fixed);
            font-family: var(--mono);
        }

        /* ---------- Mobile hamburger toggle ---------- */
        .navbar-toggler.app-toggler {
            width: 40px; height: 40px; border-radius: .8rem; padding: 0;
            display: flex; align-items: center; justify-content: center;
            background: transparent; box-shadow: none !important; transition: background .15s ease;
        }
        .navbar-toggler.app-toggler:hover { background: var(--mist); }
        .navbar-toggler.app-toggler:focus { box-shadow: none; }
        .app-toggler .toggler-icon {
            position: relative; width: 20px; height: 15px; display: block;
        }
        .app-toggler .toggler-icon span {
            position: absolute; left: 0; width: 100%; height: 2px; border-radius: 2px;
            background: var(--ink); transition: transform .25s ease, opacity .2s ease, top .25s ease;
        }
        .app-toggler .toggler-icon span:nth-child(1) { top: 0; }
        .app-toggler .toggler-icon span:nth-child(2) { top: 6.5px; width: 70%; }
        .app-toggler .toggler-icon span:nth-child(3) { top: 13px; }
        .app-toggler:not(.collapsed) .toggler-icon span:nth-child(1) { top: 6.5px; transform: rotate(45deg); background: var(--amber); }
        .app-toggler:not(.collapsed) .toggler-icon span:nth-child(2) { opacity: 0; }
        .app-toggler:not(.collapsed) .toggler-icon span:nth-child(3) { top: 6.5px; transform: rotate(-45deg); background: var(--amber); }

        /* ---------- Mobile responsive nav panel (replaces floating dropdowns) ---------- */
        @media (max-width: 767.98px) {
            .navbar-collapse {
                margin-top: .7rem; background: var(--surface); border: 1px solid var(--line); border-radius: 1rem;
                box-shadow: 0 12px 28px -10px var(--shadow-3); overflow: hidden;
            }
            .navbar-nav.ms-auto { margin-left: 0 !important; gap: 0 !important; }
        }
        .mobile-menu-section { padding: .9rem 1rem; border-bottom: 1px solid var(--line); }
        .mobile-menu-section:last-child { border-bottom: 0; }
        .mobile-menu-heading {
            display: flex; align-items: center; justify-content: space-between;
            font-family: var(--display); font-weight: 700; font-size: .85rem; color: var(--ink);
            margin-bottom: .6rem;
        }
        .mobile-menu-heading i { color: var(--amber); margin-right: .3rem; }
        .mobile-notif-list { max-height: 260px; overflow-y: auto; display: flex; flex-direction: column; gap: .5rem; }
        .mobile-notif-row {
            display: flex; align-items: flex-start; gap: .6rem; padding: .55rem .6rem;
            background: var(--mist); border-radius: .7rem;
        }
        .mobile-notif-row .notif-text { font-size: .8rem; color: var(--ink); line-height: 1.35; margin: 0; }
        .mobile-profile-card { display: flex; align-items: center; gap: .7rem; margin-bottom: .8rem; }
        .mobile-profile-card i.bi-person-circle { font-size: 2.5rem; color: var(--muted); }
        .mobile-profile-info { display: flex; flex-direction: column; gap: .25rem; }
        .mobile-profile-info strong { font-size: .95rem; color: var(--ink); }
        .mobile-menu-link {
            display: flex; align-items: center; gap: .6rem; padding: .65rem .7rem; border-radius: .7rem;
            background: var(--mist); color: var(--ink); font-weight: 600; font-size: .86rem; margin-bottom: .5rem;
        }
        .mobile-menu-link i { color: var(--teal-dark); }

        /* ---------- Toast pop-up notifications ---------- */
        .toast-stack {
            position: fixed; top: 84px; right: 18px; z-index: 1080;
            display: flex; flex-direction: column; gap: .6rem;
            width: min(360px, calc(100vw - 36px));
            pointer-events: none;
        }
        @media (max-width: 767.98px) {
            .toast-stack { top: auto; bottom: 96px; right: 12px; left: 12px; width: auto; }
        }
        .toast-pop {
            pointer-events: auto;
            position: relative; overflow: hidden;
            display: flex; align-items: flex-start; gap: .7rem;
            background: var(--surface); border: 1px solid var(--line); border-radius: .85rem;
            box-shadow: 0 12px 28px -8px var(--shadow-4), 0 2px 6px var(--shadow-2);
            padding: .8rem .9rem .85rem 1rem;
            opacity: 0; transform: translateX(24px) scale(.98);
            animation: toastIn .38s cubic-bezier(.2,.8,.25,1) forwards;
        }
        .toast-pop.is-leaving { animation: toastOut .28s ease forwards; }
        @keyframes toastIn { to { opacity: 1; transform: translateX(0) scale(1); } }
        @keyframes toastOut { to { opacity: 0; transform: translateX(16px) scale(.97); } }
        .toast-pop::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
            background: var(--toast-accent, var(--teal));
        }
        .toast-pop .toast-icon {
            flex: 0 0 auto; width: 34px; height: 34px; border-radius: .65rem;
            background: var(--toast-accent-soft, var(--teal-soft)); color: var(--toast-accent, var(--teal-dark));
            display: flex; align-items: center; justify-content: center; font-size: 1.05rem;
        }
        .toast-pop .toast-body { flex: 1 1 auto; min-width: 0; }
        .toast-pop .toast-title { font-weight: 700; font-size: .84rem; color: var(--ink); margin: 0 0 .1rem; }
        .toast-pop .toast-msg { font-size: .82rem; color: var(--muted); line-height: 1.35; word-wrap: break-word; }
        .toast-pop .toast-close {
            flex: 0 0 auto; border: 0; background: transparent; color: var(--muted);
            font-size: .95rem; line-height: 1; padding: .15rem; margin: -.1rem -.2rem 0 0; border-radius: .4rem;
        }
        .toast-pop .toast-close:hover { background: var(--mist); color: var(--ink); }
        .toast-pop .toast-progress {
            position: absolute; left: 0; bottom: 0; height: 2.5px; width: 100%;
            background: var(--toast-accent, var(--teal)); opacity: .35; transform-origin: left;
            animation: toastShrink linear forwards;
        }
        @keyframes toastShrink { from { transform: scaleX(1); } to { transform: scaleX(0); } }
        .toast-pop.toast-success { --toast-accent: var(--teal-dark); --toast-accent-soft: var(--teal-soft); }
        .toast-pop.toast-error   { --toast-accent: var(--coral);     --toast-accent-soft: var(--coral-soft); }
        .toast-pop.toast-warning { --toast-accent: var(--amber-ink); --toast-accent-soft: var(--amber-soft); }
        .toast-pop.toast-info    { --toast-accent: var(--info);      --toast-accent-soft: var(--info-soft); }

        /* ---------- Notification dropdown (bell) ---------- */
        .notif-menu { padding: 0 !important; overflow: hidden; border-radius: 1rem; border-color: var(--line); box-shadow: 0 16px 36px -12px var(--shadow-5); }
        .notif-menu .notif-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: .85rem 1rem .7rem; border-bottom: 1px solid var(--line); background: var(--mist);
        }
        .notif-menu .notif-head strong { font-family: var(--display); font-size: .88rem; }
        .notif-menu .notif-head .notif-count {
            font-family: var(--mono); font-size: .68rem; font-weight: 700; color: var(--muted);
            background: var(--surface); border: 1px solid var(--line); border-radius: 50rem; padding: .1rem .5rem;
        }
        .notif-menu .notif-list { max-height: 340px; overflow-y: auto; }
        .notif-menu .notif-row {
            display: flex; align-items: flex-start; gap: .65rem; padding: .7rem 1rem;
            border-bottom: 1px solid var(--line); transition: background .15s ease;
        }
        .notif-menu .notif-row:last-child { border-bottom: 0; }
        .notif-menu .notif-row:hover { background: var(--mist); }
        .notif-menu .notif-row .notif-ico {
            flex: 0 0 auto; width: 32px; height: 32px; border-radius: .65rem;
            display: flex; align-items: center; justify-content: center; font-size: .95rem;
        }
        .notif-menu .notif-row .notif-text { font-size: .82rem; color: var(--ink); line-height: 1.35; margin: 0; }
        .notif-menu .notif-empty { padding: 2.2rem 1rem; text-align: center; color: var(--muted); }
        .notif-menu .notif-empty i { font-size: 1.6rem; display: block; margin-bottom: .4rem; opacity: .5; }
        .notif-menu .notif-empty span { font-size: .82rem; }
        .notif-menu .notif-foot { padding: .55rem 1rem; text-align: center; border-top: 1px solid var(--line); background: var(--mist); }
        .notif-menu .notif-foot span { font-size: .74rem; color: var(--muted); font-weight: 600; }
        #notifDropdown { transition: transform .15s ease; }
        #notifDropdown:active { transform: scale(.92); }
        .notif-dot { animation: notifPulse 1.8s ease-in-out infinite; }
        @keyframes notifPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(221,75,81,.45); }
            50% { box-shadow: 0 0 0 4px rgba(221,75,81,0); }
        }

        /* ---------- Modal konfirmasi custom (pengganti confirm() bawaan browser) ---------- */
        .confirm-overlay {
            position: fixed; inset: 0; z-index: 2000;
            background: rgba(20,23,31,.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);
            display: flex; align-items: center; justify-content: center; padding: 1.1rem;
            opacity: 0; animation: confirmOverlayIn .2s ease forwards;
        }
        .confirm-overlay.is-leaving { animation: confirmOverlayOut .16s ease forwards; }
        @keyframes confirmOverlayIn { to { opacity: 1; } }
        @keyframes confirmOverlayOut { to { opacity: 0; } }
        .confirm-card {
            width: 100%; max-width: 392px; background: var(--surface); border: 1px solid var(--line);
            border-radius: 1.15rem; box-shadow: 0 28px 64px -14px var(--shadow-5);
            padding: 1.6rem 1.5rem 1.4rem; position: relative; overflow: hidden;
            transform: scale(.93) translateY(10px); opacity: 0;
            animation: confirmCardIn .3s cubic-bezier(.2,.8,.25,1) forwards;
        }
        .confirm-overlay.is-leaving .confirm-card { animation: confirmCardOut .16s ease forwards; }
        @keyframes confirmCardIn { to { transform: scale(1) translateY(0); opacity: 1; } }
        @keyframes confirmCardOut { to { transform: scale(.96) translateY(6px); opacity: 0; } }
        .confirm-card::before {
            content: ''; position: absolute; left: 0; top: 0; right: 0; height: 4px;
            background: var(--confirm-accent, var(--amber));
        }
        .confirm-icon {
            width: 52px; height: 52px; border-radius: .9rem; margin-bottom: 1rem;
            background: var(--confirm-accent-soft, var(--amber-soft)); color: var(--confirm-accent, var(--amber-ink));
            display: flex; align-items: center; justify-content: center; font-size: 1.35rem;
        }
        .confirm-title { font-family: var(--display); font-weight: 700; font-size: 1.08rem; color: var(--ink); margin: 0 0 .45rem; }
        .confirm-message { font-size: .87rem; color: var(--muted); line-height: 1.55; margin: 0 0 1.5rem; word-wrap: break-word; }
        .confirm-actions { display: flex; gap: .6rem; justify-content: flex-end; }
        .confirm-actions .btn { min-width: 92px; }
        .confirm-ok { color: #fff; border: none; }
        .confirm-ok-danger  { background: var(--coral); }
        .confirm-ok-danger:hover  { background: #c23e44; box-shadow: 0 6px 16px -6px rgba(221,75,81,.4); }
        .confirm-ok-warning { background: var(--amber); color: var(--ink-fixed); }
        .confirm-ok-warning:hover { background: #c68c35; box-shadow: 0 6px 16px -6px rgba(219,154,61,.4); }
        .confirm-ok-success { background: var(--teal); }
        .confirm-ok-success:hover { background: var(--teal-dark); box-shadow: 0 6px 16px -6px rgba(13,110,90,.4); }
        .confirm-ok-info    { background: var(--info); }
        .confirm-ok-info:hover    { background: #244d87; box-shadow: 0 6px 16px -6px rgba(47,95,163,.4); }
        .confirm-card.confirm-danger  { --confirm-accent: var(--coral);     --confirm-accent-soft: var(--coral-soft); }
        .confirm-card.confirm-warning { --confirm-accent: var(--amber-ink); --confirm-accent-soft: var(--amber-soft); }
        .confirm-card.confirm-success { --confirm-accent: var(--teal-dark); --confirm-accent-soft: var(--teal-soft); }
        .confirm-card.confirm-info    { --confirm-accent: var(--info);      --confirm-accent-soft: var(--info-soft); }
        @media (max-width: 420px) {
            .confirm-actions { flex-direction: column-reverse; }
            .confirm-actions .btn { width: 100%; }
        }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-md topbar sticky-top py-2">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->check() ? route('karyawan.dashboard') : '/') }}">
            <span class="brand-mark"><i class="bi bi-qr-code-scan"></i></span>
            <span>Sistem Absensi</span>
        </a>

        <button type="button" class="icon-btn theme-toggle ms-auto" id="themeToggleBtn" aria-label="Ganti mode gelap/terang" title="Ganti mode gelap/terang">
            <i class="bi bi-moon-stars-fill" id="themeToggleIcon"></i>
        </button>

        @auth
        <button class="navbar-toggler app-toggler collapsed border-0 d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Buka menu">
            <span class="toggler-icon"><span></span><span></span><span></span></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-md-center gap-2">
                <li class="nav-item d-none d-lg-flex align-items-center">
                    <span class="live-clock"><i class="bi bi-record-circle-fill"></i><span id="liveClock">--:--:--</span></span>
                </li>
                @include('layouts._notifications')
                <li class="nav-item dropdown d-none d-md-flex align-items-center">
                    <a class="nav-link dropdown-toggle user-chip px-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(auth()->user()->isKaryawan() && auth()->user()->employee)
                            <img src="{{ auth()->user()->employee->photo_url }}" alt="Foto" class="rounded-circle" width="30" height="30" style="object-fit: cover;">
                        @else
                            <i class="bi bi-person-circle fs-5"></i>
                        @endif
                        <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                        <span class="role-pill d-none d-lg-inline">{{ ucfirst(auth()->user()->role) }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li class="px-3 py-1 d-lg-none"><strong class="small">{{ auth()->user()->name }}</strong></li>
                        @if(auth()->user()->isKaryawan())
                            <li><a class="dropdown-item" href="{{ route('karyawan.profile.edit') }}"><i class="bi bi-person-gear me-1"></i> Profil Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                        @endif
                        <li class="px-3 py-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger w-100" type="submit">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>

                {{-- Mobile: kartu profil statis + tombol keluar, tanpa dropdown --}}
                <li class="nav-item d-md-none mobile-menu-section">
                    <div class="mobile-profile-card">
                        @if(auth()->user()->isKaryawan() && auth()->user()->employee)
                            <img src="{{ auth()->user()->employee->photo_url }}" alt="Foto" class="rounded-circle" width="42" height="42" style="object-fit: cover;">
                        @else
                            <i class="bi bi-person-circle"></i>
                        @endif
                        <div class="mobile-profile-info">
                            <strong>{{ auth()->user()->name }}</strong>
                            <span class="role-pill">{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                    </div>
                    @if(auth()->user()->isKaryawan())
                        <a href="{{ route('karyawan.profile.edit') }}" class="mobile-menu-link">
                            <i class="bi bi-person-gear"></i> Profil Saya
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button class="btn btn-outline-danger w-100" type="submit">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        @endauth
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        @hasSection('sidebar')
            <div class="col-md-3 col-lg-2 d-none d-md-block p-3 pt-4 sidebar" style="min-height: calc(100vh - 61px);">
                <div class="side-eyebrow">Menu</div>
                @yield('sidebar')
            </div>
            <main class="col-12 col-md-9 col-lg-10 p-3 p-md-4 page-main">
                @yield('content')
                <div class="mobile-nav-spacer d-md-none" aria-hidden="true"></div>
            </main>
        @else
            <main class="col-12 p-3 p-md-4 page-main">
                @yield('content')
                <div class="mobile-nav-spacer d-md-none" aria-hidden="true"></div>
            </main>
        @endif
    </div>
</div>

@auth
    @include('layouts._bottom-nav')
@endauth

<div class="toast-stack" id="toastStack"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var el = document.getElementById('liveClock');
        if (!el) return;
        function tick() {
            el.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });
        }
        tick();
        setInterval(tick, 1000);
    })();
</script>

<script>
    // ---------- Toggle mode gelap / terang ----------
    (function () {
        var btn = document.getElementById('themeToggleBtn');
        var icon = document.getElementById('themeToggleIcon');
        if (!btn || !icon) return;

        function applyIcon(theme) {
            icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
        }

        applyIcon(document.documentElement.getAttribute('data-bs-theme') || 'light');

        btn.addEventListener('click', function () {
            var current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
            var next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            applyIcon(next);
            try { localStorage.setItem('theme', next); } catch (e) {}
        });
    })();
</script>

<script>
    // ---------- Professional pop-up toast engine ----------
    window.AppToast = (function () {
        var stack = document.getElementById('toastStack');
        var ICONS = {
            success: 'bi-check-circle-fill',
            error:   'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info:    'bi-info-circle-fill',
        };
        var TITLES = {
            success: 'Berhasil',
            error:   'Terjadi Kesalahan',
            warning: 'Perhatian',
            info:    'Informasi',
        };

        function show(message, type, opts) {
            type = type || 'info';
            opts = opts || {};
            var duration = opts.duration || 4500;
            var title = opts.title || TITLES[type] || TITLES.info;

            var el = document.createElement('div');
            el.className = 'toast-pop toast-' + type;
            el.setAttribute('role', 'alert');
            el.innerHTML =
                '<span class="toast-icon"><i class="bi ' + (ICONS[type] || ICONS.info) + '"></i></span>' +
                '<div class="toast-body">' +
                    '<p class="toast-title">' + title + '</p>' +
                    '<div class="toast-msg"></div>' +
                '</div>' +
                '<button type="button" class="toast-close" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>' +
                '<span class="toast-progress" style="animation-duration:' + duration + 'ms"></span>';
            el.querySelector('.toast-msg').textContent = message;

            stack.appendChild(el);

            var timer = setTimeout(function () { dismiss(el); }, duration);
            el.querySelector('.toast-close').addEventListener('click', function () {
                clearTimeout(timer);
                dismiss(el);
            });
            el.addEventListener('mouseenter', function () { clearTimeout(timer); });
            el.addEventListener('mouseleave', function () {
                timer = setTimeout(function () { dismiss(el); }, 1500);
            });
        }

        function dismiss(el) {
            el.classList.add('is-leaving');
            el.addEventListener('animationend', function () { el.remove(); }, { once: true });
        }

        return { show: show };
    })();
    </script>

    <script>
    // ---------- Modal konfirmasi custom (pengganti window.confirm) ----------
    window.AppConfirm = (function () {
        var ICONS = {
            danger:  'bi-trash3-fill',
            warning: 'bi-exclamation-triangle-fill',
            success: 'bi-check-circle-fill',
            info:    'bi-question-circle-fill',
        };
        var TITLES = {
            danger:  'Konfirmasi Hapus',
            warning: 'Konfirmasi Tindakan',
            success: 'Konfirmasi Persetujuan',
            info:    'Konfirmasi',
        };

        function show(opts) {
            opts = opts || {};
            var type = opts.type || 'warning';
            var title = opts.title || TITLES[type] || TITLES.warning;
            var message = opts.message || 'Apakah Anda yakin?';
            var confirmText = opts.confirmText || 'Ya, Lanjutkan';
            var cancelText = opts.cancelText || 'Batal';

            return new Promise(function (resolve) {
                var overlay = document.createElement('div');
                overlay.className = 'confirm-overlay';
                overlay.innerHTML =
                    '<div class="confirm-card confirm-' + type + '" role="alertdialog" aria-modal="true">' +
                        '<div class="confirm-icon"><i class="bi ' + (ICONS[type] || ICONS.warning) + '"></i></div>' +
                        '<h3 class="confirm-title"></h3>' +
                        '<p class="confirm-message"></p>' +
                        '<div class="confirm-actions">' +
                            '<button type="button" class="btn btn-outline-secondary confirm-cancel"></button>' +
                            '<button type="button" class="btn confirm-ok confirm-ok-' + type + '"></button>' +
                        '</div>' +
                    '</div>';
                overlay.querySelector('.confirm-title').textContent = title;
                overlay.querySelector('.confirm-message').textContent = message;
                var cancelBtn = overlay.querySelector('.confirm-cancel');
                var okBtn = overlay.querySelector('.confirm-ok');
                cancelBtn.textContent = cancelText;
                okBtn.textContent = confirmText;

                document.body.appendChild(overlay);
                var prevOverflow = document.body.style.overflow;
                document.body.style.overflow = 'hidden';
                var prevFocused = document.activeElement;
                okBtn.focus();

                function onKey(e) {
                    if (e.key === 'Escape') close(false);
                    if (e.key === 'Tab') {
                        var focusables = [cancelBtn, okBtn];
                        var idx = focusables.indexOf(document.activeElement);
                        e.preventDefault();
                        var next = e.shiftKey ? (idx <= 0 ? focusables.length - 1 : idx - 1) : (idx === focusables.length - 1 ? 0 : idx + 1);
                        focusables[next].focus();
                    }
                }

                function close(result) {
                    document.removeEventListener('keydown', onKey);
                    document.body.style.overflow = prevOverflow;
                    overlay.classList.add('is-leaving');
                    overlay.addEventListener('animationend', function handler(e) {
                        if (e.target !== overlay) return;
                        overlay.removeEventListener('animationend', handler);
                        overlay.remove();
                    });
                    if (prevFocused && prevFocused.focus) prevFocused.focus();
                    resolve(result);
                }

                document.addEventListener('keydown', onKey);
                overlay.addEventListener('mousedown', function (e) { if (e.target === overlay) close(false); });
                cancelBtn.addEventListener('click', function () { close(false); });
                okBtn.addEventListener('click', function () { close(true); });
            });
        }

        return { show: show };
    })();
    </script>

    <script>
    // ---------- Intersep otomatis: <form data-confirm="..."> memakai modal custom, bukan confirm() bawaan ----------
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;
        if (form.dataset.confirmed === '1') return;
        e.preventDefault();
        window.AppConfirm.show({
            type: form.dataset.confirmType || 'warning',
            title: form.dataset.confirmTitle,
            message: form.getAttribute('data-confirm'),
            confirmText: form.dataset.confirmOk,
            cancelText: form.dataset.confirmCancel,
        }).then(function (ok) {
            if (!ok) return;
            form.dataset.confirmed = '1';
            if (form.requestSubmit) form.requestSubmit();
            else form.submit();
        });
    });
    </script>

    @php
        $__flashToasts = array_filter([
            'success' => session('success'),
            'error'   => session('error'),
            'warning' => session('warning'),
            'info'    => session('status'),
        ]);
    @endphp
    <script>
    // Auto-surface Laravel session flash messages as pop-up toasts
    document.addEventListener('DOMContentLoaded', function () {
        var flashes = @json($__flashToasts);
        Object.keys(flashes).forEach(function (type, i) {
            setTimeout(function () {
                window.AppToast.show(flashes[type], type === 'info' ? 'info' : type);
            }, i * 180);
        });
    });
    </script>
@stack('scripts')
</body>
</html>
