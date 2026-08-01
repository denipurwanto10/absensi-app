@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <span class="font-mono text-uppercase small text-muted" style="letter-spacing:.06em;">{{ now()->format('H:i') }} WIB</span>
        <h4 class="mb-1 mt-1">Dashboard</h4>
        <p class="text-muted mb-0">Ringkasan absensi hari ini, {{ now()->translatedFormat('l, d F Y') }}.</p>
    </div>

    @if($isHolidayToday)
        <div class="alert d-flex align-items-center gap-2 mb-4" style="background:var(--coral-soft); color:var(--coral); border:none;">
            <i class="bi bi-calendar-heart fs-5"></i>
            <div>Hari ini <strong>tanggal merah</strong>{{ $holidayNameToday ? " ({$holidayNameToday})" : '' }}. Notifikasi "belum absen" otomatis nonaktif.</div>
            <a href="{{ route('admin.holidays.index') }}" class="ms-auto small text-decoration-none" style="color:var(--coral);">Lihat kalender <i class="bi bi-arrow-right"></i></a>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:46px;height:46px;background:var(--teal-soft);">
                        <i class="bi bi-people fs-5" style="color:var(--teal-dark);"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-mono">{{ $totalKaryawan }}</h3>
                        <small class="text-muted">Total Karyawan</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:46px;height:46px;background:var(--teal-soft);">
                        <i class="bi bi-check-circle fs-5" style="color:var(--teal-dark);"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-mono">{{ $hadirHariIni }}</h3>
                        <small class="text-muted">Hadir Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:46px;height:46px;background:var(--amber-soft);">
                        <i class="bi bi-clock-history fs-5" style="color:var(--amber-ink);"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-mono">{{ $telatHariIni }}</h3>
                        <small class="text-muted">Telat Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:46px;height:46px;background:var(--coral-soft);">
                        <i class="bi bi-exclamation-circle fs-5" style="color:var(--coral);"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-mono">{{ $belumAbsen }}</h3>
                        <small class="text-muted">Belum Absen</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.leaves.index') }}" class="card p-3 h-100 text-decoration-none d-block">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:46px;height:46px;background:var(--amber-soft);">
                        <i class="bi bi-envelope-paper fs-5" style="color:var(--amber-ink);"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-mono" style="color:var(--ink);">{{ $izinPending }}</h3>
                        <small class="text-muted">Izin Menunggu</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.remote-attendances.index') }}" class="card p-3 h-100 text-decoration-none d-block">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:46px;height:46px;background:var(--teal-soft);">
                        <i class="bi bi-geo-alt fs-5" style="color:var(--teal-dark);"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-mono" style="color:var(--ink);">{{ $remotePending }}</h3>
                        <small class="text-muted">Absen Luar Menunggu</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Tren Kehadiran 14 Hari Terakhir</h6>
                </div>
                <div style="position: relative; height: 260px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card p-3 h-100">
                <h6 class="mb-3">Top Keterlambatan Bulan Ini</h6>
                @forelse($topTelat as $i => $t)
                    <div class="d-flex align-items-center gap-2 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}" style="border-color: var(--line) !important;">
                        <span class="font-mono small text-muted" style="width: 1.2rem;">{{ $i + 1 }}</span>
                        <img src="{{ $t->employee->photo_url }}" class="rounded-circle" width="30" height="30" style="object-fit:cover;" alt="">
                        <div class="flex-grow-1">
                            <div class="small fw-semibold">{{ $t->employee->user->name }}</div>
                            <small class="text-muted">{{ $t->employee->nip }}</small>
                        </div>
                        <span class="badge bg-warning">{{ $t->total }}x</span>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-emoji-smile fs-3 d-block mb-1"></i>
                        Belum ada keterlambatan bulan ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card p-3 mb-3 mb-md-0">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Absensi Hari Ini</h6>
            <a href="{{ route('admin.attendances.index') }}" class="small text-decoration-none" style="color:var(--teal-dark);">
                Lihat semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensiHariIni as $a)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $a->employee->photo_url }}" class="rounded-circle" width="30" height="30" style="object-fit:cover;" alt="">
                                    <span>{{ $a->employee->user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $a->check_in ? \Illuminate\Support\Carbon::parse($a->check_in)->format('H:i') : '-' }}</td>
                            <td>{{ $a->check_out ? \Illuminate\Support\Carbon::parse($a->check_out)->format('H:i') : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $a->status_badge }}">
                                    {{ $a->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Belum ada data absensi hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        var ctx = document.getElementById('trendChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($trendLabels),
                datasets: [
                    {
                        label: 'Hadir',
                        data: @json($trendHadir),
                        backgroundColor: '#0D6E5A',
                        borderRadius: 4,
                        stack: 'a',
                    },
                    {
                        label: 'Telat',
                        data: @json($trendTelat),
                        backgroundColor: '#DB9A3D',
                        borderRadius: 4,
                        stack: 'a',
                    },
                    {
                        label: 'Izin/Sakit/Cuti',
                        data: @json($trendIzin),
                        backgroundColor: '#8CA3C9',
                        borderRadius: 4,
                        stack: 'a',
                    },
                    {
                        label: 'Alpha',
                        data: @json($trendAlpha),
                        backgroundColor: '#DD4B51',
                        borderRadius: 4,
                        stack: 'a',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { stacked: true, beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } },
                },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                },
            },
        });
    })();
</script>
@endpush
