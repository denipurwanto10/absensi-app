@extends('layouts.app')
@section('title', 'Dashboard Karyawan')

@push('styles')
<style>
    .id-badge {
        background: var(--ink-fixed); color: #fff; border-radius: 1.1rem; overflow: hidden;
        position: relative; border: 1px solid var(--ink-fixed);
        box-shadow: 0 10px 30px -12px var(--shadow-4);
    }
    .id-badge .badge-clip {
        width: 46px; height: 12px; background: rgba(255,255,255,.15); border-radius: 0 0 8px 8px;
        margin: 0 auto; position: relative; top: 0;
    }
    .id-badge .badge-top { padding: .6rem 1.25rem 1.1rem; text-align: center; position: relative; }
    .id-badge .badge-top::before {
        content: ''; position: absolute; inset: 0; opacity: .5; pointer-events: none;
        background:
            radial-gradient(circle at 12% 20%, rgba(219,154,61,.18), transparent 45%),
            radial-gradient(circle at 88% 85%, rgba(13,110,90,.25), transparent 45%);
    }
    .id-badge .badge-photo {
        width: 78px; height: 78px; border-radius: 50%; object-fit: cover;
        border: 3px solid rgba(219,154,61,.55); margin: .6rem auto .55rem;
        position: relative; z-index: 1; box-shadow: 0 0 0 4px rgba(255,255,255,.06);
    }
    .id-badge .badge-name { font-family: var(--display); font-weight: 700; font-size: 1.08rem; margin: 0; position: relative; z-index: 1; }
    .id-badge .badge-role { font-size: .72rem; color: rgba(255,255,255,.55); margin: .15rem 0 .4rem; position: relative; z-index: 1; }
    .id-badge .badge-nip {
        font-family: var(--mono); font-size: .72rem; color: var(--amber); letter-spacing: .04em;
        position: relative; z-index: 1; display: inline-flex; align-items: center; gap: .35rem;
        background: rgba(219,154,61,.12); padding: .2rem .6rem; border-radius: .4rem;
    }
    .id-badge .badge-stub {
        background: var(--surface); color: var(--ink); border-radius: 1.1rem 1.1rem 0 0; margin-top: 1rem;
        padding: 1.35rem 1.1rem 1.1rem; position: relative;
    }
    .id-badge .badge-stub::before {
        content: ''; position: absolute; top: -9px; left: 0; right: 0; height: 18px;
        background: radial-gradient(circle, var(--ink-fixed) 5px, transparent 5.5px);
        background-size: 22px 18px; background-repeat: repeat-x; background-position: center top;
    }
    .qr-wrap { display: flex; justify-content: center; padding: .35rem 0 .6rem; }
    .qr-wrap canvas, .qr-wrap img { border-radius: .5rem; }
    .badge-hint { font-size: .74rem; color: var(--muted); text-align: center; display: block; }

    .punch-panel { border-radius: 1rem; }
    .punch-slot {
        border: 1px solid var(--line); border-radius: .85rem; padding: .9rem 1rem;
        display: flex; align-items: center; gap: .8rem; flex: 1; transition: border-color .15s ease;
    }
    .punch-icon {
        width: 42px; height: 42px; border-radius: .7rem; flex: 0 0 auto;
        display: flex; align-items: center; justify-content: center; font-size: 1.15rem;
    }
    .punch-time { font-family: var(--mono); font-weight: 600; font-size: 1.35rem; line-height: 1; letter-spacing: -.01em; }
    .punch-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); font-family: var(--mono); }

    .greet-eyebrow { font-family: var(--mono); font-size: .74rem; letter-spacing: .06em; color: var(--muted); text-transform: uppercase; }

    /* ---------- Monthly stat strip ---------- */
    .stat-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem; }
    @media (max-width: 767.98px) { .stat-strip { grid-template-columns: repeat(2, 1fr); } }
    .stat-card {
        border: 1px solid var(--line); border-radius: .9rem; padding: .85rem .95rem; background: var(--surface);
        display: flex; align-items: center; gap: .7rem;
    }
    .stat-card .stat-icon {
        width: 38px; height: 38px; border-radius: .65rem; flex: 0 0 auto;
        display: flex; align-items: center; justify-content: center; font-size: 1rem;
    }
    .stat-card .stat-value { font-family: var(--display); font-weight: 700; font-size: 1.25rem; line-height: 1.1; }
    .stat-card .stat-label { font-size: .68rem; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; font-family: var(--mono); }

    /* ---------- Quick actions ---------- */
    .quick-actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: .6rem; }
    .quick-action {
        border: 1px solid var(--line); border-radius: .8rem; padding: .7rem .8rem; color: var(--ink);
        display: flex; flex-direction: column; gap: .5rem; background: var(--surface); transition: border-color .15s ease, transform .15s ease;
    }
    .quick-action:hover { border-color: var(--ink); transform: translateY(-1px); color: var(--ink); }
    .quick-action .qa-icon {
        width: 34px; height: 34px; border-radius: .6rem; display: flex; align-items: center;
        justify-content: center; font-size: .95rem; background: var(--mist); color: var(--ink);
    }
    .quick-action .qa-label { font-size: .82rem; font-weight: 600; }

    .section-title {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: .9rem;
    }
    .section-title h6 { margin: 0; }
    .section-title .view-all { font-size: .78rem; color: var(--muted); font-weight: 600; }
    .section-title .view-all:hover { color: var(--ink); }

    /* ---------- Mobile refinements ---------- */
    @media (max-width: 767.98px) {
        /* Quick actions become a single-column list: avoids uneven card
           heights when labels wrap to 2 lines on narrow screens. */
        .quick-actions { grid-template-columns: 1fr; gap: .5rem; }
        .quick-action {
            flex-direction: row; align-items: center; text-align: left;
            padding: .65rem .8rem; gap: .75rem;
        }
        .quick-action .qa-icon { flex: 0 0 auto; }
        .quick-action .qa-label { flex: 1 1 auto; }
        .quick-action::after {
            content: '\F285'; font-family: 'bootstrap-icons'; font-size: .8rem; color: var(--muted);
        }

        .id-badge .badge-top { padding: .5rem 1.1rem .9rem; }
        .id-badge .badge-photo { width: 64px; height: 64px; margin: .5rem auto .45rem; }
        .id-badge .badge-stub { padding: 1.1rem 1rem .9rem; }
        .qr-wrap canvas, .qr-wrap img { width: 130px; height: 130px; }
    }

    @media (max-width: 380px) {
        .stat-card { padding: .7rem .7rem; gap: .55rem; }
        .stat-card .stat-icon { width: 32px; height: 32px; font-size: .85rem; }
        .stat-card .stat-value { font-size: 1.08rem; }
        .stat-card .stat-label { font-size: .62rem; }
    }
</style>
@endpush

@section('content')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <div>
            <span class="greet-eyebrow">{{ now()->translatedFormat('l, d F Y') }}</span>
            <h4 class="mb-0 mt-1">Halo, {{ explode(' ', auth()->user()->name)[0] }} 👋</h4>
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-{{ ($today?->status_badge) ?? 'secondary' }} fs-6">
                {{ $today?->status_label ?? 'Belum absen hari ini' }}
            </span>
        </div>
    </div>

    @if($isHolidayToday)
        <div class="alert d-flex align-items-center gap-2 mb-3" style="background:var(--coral-soft); color:var(--coral); border:none;">
            <i class="bi bi-calendar-heart fs-5"></i>
            <div>Hari ini <strong>tanggal merah</strong>{{ $holidayNameToday ? " ({$holidayNameToday})" : '' }}, tidak wajib absen.</div>
            <a href="{{ route('karyawan.holidays.index') }}" class="ms-auto small text-decoration-none" style="color:var(--coral);">Lihat kalender <i class="bi bi-arrow-right"></i></a>
        </div>
    @endif

    {{-- Monthly summary strip --}}
    <div class="stat-strip mb-3">
        <div class="stat-card">
            <span class="stat-icon" style="background:var(--teal-soft); color:var(--teal-dark);"><i class="bi bi-check2-circle"></i></span>
            <div>
                <div class="stat-value">{{ $hadirCount }}</div>
                <div class="stat-label">Hadir</div>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background:var(--amber-soft); color:var(--amber-ink);"><i class="bi bi-clock-history"></i></span>
            <div>
                <div class="stat-value">{{ $telatCount }}</div>
                <div class="stat-label">Telat</div>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background:var(--info-soft); color:var(--info);"><i class="bi bi-envelope-paper"></i></span>
            <div>
                <div class="stat-value">{{ $izinCount }}</div>
                <div class="stat-label">Izin/Cuti</div>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background:var(--coral-soft); color:var(--coral);"><i class="bi bi-x-circle"></i></span>
            <div>
                <div class="stat-value">{{ $alphaCount }}</div>
                <div class="stat-label">Alpha</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- ID badge / QR --}}
        <div class="col-12 col-lg-4">
            <div class="id-badge d-flex flex-column mb-3">
                <div class="badge-top">
                    <div class="badge-clip"></div>
                    @if($employee)
                        <img src="{{ $employee->photo_url }}" alt="Foto profil" class="badge-photo">
                    @endif
                    <p class="badge-name">{{ auth()->user()->name }}</p>
                    <div class="badge-role">{{ $employee->position ?? 'Karyawan' }}</div>
                    <span class="badge-nip"><i class="bi bi-credit-card-2-front"></i> NIP {{ $employee->nip ?? '-' }}</span>
                </div>
                <div class="badge-stub flex-grow-1 d-flex flex-column">
                    <div class="qr-wrap">
                        <div id="qrcode"></div>
                    </div>
                    <span class="badge-hint">Tunjukkan QR ini ke petugas / scanner untuk absen</span>
                </div>
            </div>

            <div class="card p-3">
                <div class="section-title">
                    <h6><i class="bi bi-lightning-charge me-1" style="color: var(--amber-ink);"></i> Akses Cepat</h6>
                </div>
                <div class="quick-actions">
                    <a href="{{ route('karyawan.attendances.scanner') }}" class="quick-action">
                        <span class="qa-icon" style="background:var(--teal-soft); color:var(--teal-dark);"><i class="bi bi-qr-code-scan"></i></span>
                        <span class="qa-label">Absen Sekarang</span>
                    </a>
                    <a href="{{ route('karyawan.leaves.create') }}" class="quick-action">
                        <span class="qa-icon" style="background:var(--info-soft); color:var(--info);"><i class="bi bi-envelope-paper"></i></span>
                        <span class="qa-label">Izin/Sakit/Cuti</span>
                    </a>
                    <a href="{{ route('karyawan.remote-attendances.create') }}" class="quick-action">
                        <span class="qa-icon" style="background:var(--teal-soft); color:var(--teal-dark);"><i class="bi bi-geo-alt"></i></span>
                        <span class="qa-label">Absen Luar Kantor</span>
                    </a>
                    {{-- Koreksi & Kalender disembunyikan di mobile karena sudah ada di menu "Lainnya" (bottom-nav); tetap tampil di desktop karena karyawan tidak punya sidebar --}}
                    <a href="{{ route('karyawan.corrections.create') }}" class="quick-action d-none d-md-flex">
                        <span class="qa-icon" style="background:var(--amber-soft); color:var(--amber-ink);"><i class="bi bi-pencil-square"></i></span>
                        <span class="qa-label">Koreksi Absen</span>
                    </a>
                    <a href="{{ route('karyawan.holidays.index') }}" class="quick-action d-none d-md-flex">
                        <span class="qa-icon" style="background:var(--coral-soft); color:var(--coral);"><i class="bi bi-calendar-heart"></i></span>
                        <span class="qa-label">Kalender Libur</span>
                    </a>
                    <a href="{{ route('karyawan.profile.edit') }}" class="quick-action d-none d-md-flex">
                        <span class="qa-icon"><i class="bi bi-person-gear"></i></span>
                        <span class="qa-label">Profil Saya</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Today's punches + history --}}
        <div class="col-12 col-lg-8 d-flex flex-column gap-3">
            <div class="row g-3">
                <div class="col-12 col-sm-7">
                    <div class="card punch-panel p-3 h-100">
                        <h6 class="mb-3">Absensi Hari Ini</h6>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <div class="punch-slot">
                                <span class="punch-icon" style="background:var(--teal-soft); color:var(--teal-dark);">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </span>
                                <div>
                                    <span class="punch-label">Masuk</span>
                                    <div class="punch-time">{{ $today?->check_in ? \Illuminate\Support\Carbon::parse($today->check_in)->format('H:i') : '--:--' }}</div>
                                </div>
                            </div>
                            <div class="punch-slot">
                                <span class="punch-icon" style="background:var(--coral-soft); color:var(--coral);">
                                    <i class="bi bi-box-arrow-left"></i>
                                </span>
                                <div>
                                    <span class="punch-label">Pulang</span>
                                    <div class="punch-time">{{ $today?->check_out ? \Illuminate\Support\Carbon::parse($today->check_out)->format('H:i') : '--:--' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-md-none mt-3">
                            <span class="badge bg-{{ ($today?->status_badge) ?? 'secondary' }}">
                                {{ $today?->status_label ?? 'Belum absen hari ini' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-5">
                    <a href="{{ route('karyawan.leaves.index') }}" class="card p-3 h-100 text-decoration-none d-block" style="color: var(--ink);">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="punch-label">Sisa Kuota Cuti</span>
                            <i class="bi bi-airplane" style="color: var(--teal-dark);"></i>
                        </div>
                        <div class="punch-time mt-1">{{ $cutiRemaining }} <span class="fs-6 fw-normal text-muted">/ {{ $cutiQuota }} hari</span></div>
                        @php($__cutiPct = $cutiQuota > 0 ? min(100, round($cutiUsed / $cutiQuota * 100)) : 0)
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $__cutiPct }}%; background: var(--teal);"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">{{ $cutiUsed }} hari terpakai tahun {{ now()->year }}</small>
                    </a>
                </div>
            </div>

            <div class="card p-3 flex-grow-1 mb-3 mb-md-0" id="riwayat">
                <div class="section-title">
                    <h6><i class="bi bi-clock-history me-1" style="color: var(--muted);"></i> Riwayat Absensi</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($histories as $h)
                                <tr>
                                    <td>{{ $h->date->format('d-m-Y') }}</td>
                                    <td class="font-mono">{{ $h->check_in ? \Illuminate\Support\Carbon::parse($h->check_in)->format('H:i') : '-' }}</td>
                                    <td class="font-mono">{{ $h->check_out ? \Illuminate\Support\Carbon::parse($h->check_out)->format('H:i') : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $h->status_badge }}">
                                            {{ $h->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                        Belum ada riwayat absensi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $histories->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $employee->qr_token ?? '' }}",
        width: 150,
        height: 150,
        colorDark: "#14171F",
        colorLight: "#ffffff",
    });
</script>
@endpush
