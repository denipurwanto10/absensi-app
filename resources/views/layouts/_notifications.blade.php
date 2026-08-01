@php
    $__notifUser = auth()->user();
    $__notifItems = collect();
    $__batasTelat = '08:00';
    $__isHolidayToday = \App\Models\Holiday::isRedDate(now());

    if ($__notifUser->isAdmin()) {
        $__todayAttendances = \App\Models\Attendance::with('employee.user')
            ->whereDate('date', now()->toDateString())
            ->get();

        foreach ($__todayAttendances->where('status', 'telat') as $__t) {
            $__notifItems->push([
                'icon' => 'bi-clock-history text-warning',
                'text' => ($__t->employee->user->name ?? 'Karyawan').' telat masuk hari ini.',
            ]);
        }

        if (! $__isHolidayToday && now()->format('H:i') > $__batasTelat) {
            $__hadirIds = $__todayAttendances->pluck('employee_id');
            $__belumAbsen = \App\Models\Employee::with('user')->whereNotIn('id', $__hadirIds)->get();

            foreach ($__belumAbsen as $__b) {
                $__notifItems->push([
                    'icon' => 'bi-exclamation-circle text-danger',
                    'text' => ($__b->user->name ?? 'Karyawan').' belum absen hari ini.',
                ]);
            }
        }

        $__pendingLeaves = \App\Models\Leave::with('employee.user')->where('status', 'pending')->latest()->get();
        foreach ($__pendingLeaves as $__pl) {
            $__notifItems->push([
                'icon' => 'bi-envelope-paper text-primary',
                'text' => ($__pl->employee->user->name ?? 'Karyawan').' mengajukan '.$__pl->type_label.', menunggu persetujuan.',
            ]);
        }

        $__pendingCorrections = \App\Models\AttendanceCorrection::with('employee.user')->where('status', 'pending')->latest()->get();
        foreach ($__pendingCorrections as $__pc) {
            $__notifItems->push([
                'icon' => 'bi-pencil-square text-primary',
                'text' => ($__pc->employee->user->name ?? 'Karyawan').' mengajukan koreksi absensi tanggal '.$__pc->date->format('d-m-Y').'.',
            ]);
        }
    } elseif ($__notifUser->isKaryawan() && $__notifUser->employee) {
        $__todayAttendance = $__notifUser->employee->todayAttendance();

        if (! $__isHolidayToday && ! $__todayAttendance && now()->format('H:i') > $__batasTelat) {
            $__notifItems->push([
                'icon' => 'bi-exclamation-circle text-danger',
                'text' => 'Kamu belum absen hari ini!',
            ]);
        } elseif ($__todayAttendance && $__todayAttendance->status === 'telat') {
            $__notifItems->push([
                'icon' => 'bi-clock-history text-warning',
                'text' => 'Kamu tercatat telat masuk hari ini pukul '.\Illuminate\Support\Carbon::parse($__todayAttendance->check_in)->format('H:i').'.',
            ]);
        }

        $__reviewedLeaves = $__notifUser->employee->leaves()
            ->whereIn('status', ['approved', 'rejected'])
            ->where('reviewed_at', '>=', now()->subDays(3))
            ->latest('reviewed_at')
            ->get();
        foreach ($__reviewedLeaves as $__rl) {
            $__notifItems->push([
                'icon' => $__rl->status === 'approved' ? 'bi-check-circle text-success' : 'bi-x-circle text-danger',
                'text' => 'Pengajuan '.$__rl->type_label.' kamu telah '.($__rl->status === 'approved' ? 'disetujui' : 'ditolak').'.',
            ]);
        }

        $__reviewedCorrections = $__notifUser->employee->corrections()
            ->whereIn('status', ['approved', 'rejected'])
            ->where('reviewed_at', '>=', now()->subDays(3))
            ->latest('reviewed_at')
            ->get();
        foreach ($__reviewedCorrections as $__rc) {
            $__notifItems->push([
                'icon' => $__rc->status === 'approved' ? 'bi-check-circle text-success' : 'bi-x-circle text-danger',
                'text' => 'Koreksi absensi tanggal '.$__rc->date->format('d-m-Y').' kamu telah '.($__rc->status === 'approved' ? 'disetujui' : 'ditolak').'.',
            ]);
        }
    }

    $__recentAnnouncements = \App\Models\Announcement::where('created_at', '>=', now()->subDays(5))->latest()->get();
    foreach ($__recentAnnouncements as $__an) {
        $__notifItems->push([
            'icon' => 'bi-megaphone text-primary',
            'text' => 'Pengumuman: '.$__an->title,
        ]);
    }

    $__notifShown = $__notifItems->take(6);
@endphp

<li class="nav-item dropdown d-none d-md-block">
    <a class="icon-btn" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell"></i>
        @if($__notifItems->count())
            <span class="notif-dot">{{ $__notifItems->count() > 9 ? '9+' : $__notifItems->count() }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end notif-menu" aria-labelledby="notifDropdown" style="min-width: 300px; max-width: 340px;">
        <div class="notif-head">
            <strong>Notifikasi</strong>
            @if($__notifItems->count())
                <span class="notif-count">{{ $__notifItems->count() }} baru</span>
            @endif
        </div>
        <div class="notif-list">
            @forelse($__notifShown as $__n)
                <div class="notif-row">
                    <span class="notif-ico" style="background: var(--mist);"><i class="bi {{ $__n['icon'] }}"></i></span>
                    <p class="notif-text">{{ $__n['text'] }}</p>
                </div>
            @empty
                <div class="notif-empty">
                    <i class="bi bi-bell-slash"></i>
                    <span>Tidak ada notifikasi baru.</span>
                </div>
            @endforelse
        </div>
        @if($__notifItems->count() > $__notifShown->count())
            <div class="notif-foot">
                <span>+ {{ $__notifItems->count() - $__notifShown->count() }} notifikasi lainnya</span>
            </div>
        @endif
    </div>
</li>

{{-- Mobile: panel notifikasi statis (tanpa dropdown melayang) di dalam menu --}}
<li class="nav-item d-md-none mobile-menu-section">
    <div class="mobile-menu-heading">
        <span><i class="bi bi-bell"></i> Notifikasi</span>
        @if($__notifItems->count())
            <span class="notif-count">{{ $__notifItems->count() }} baru</span>
        @endif
    </div>
    <div class="mobile-notif-list">
        @forelse($__notifShown as $__n)
            <div class="mobile-notif-row">
                <span class="notif-ico" style="background: var(--mist);"><i class="bi {{ $__n['icon'] }}"></i></span>
                <p class="notif-text">{{ $__n['text'] }}</p>
            </div>
        @empty
            <div class="notif-empty py-3">
                <i class="bi bi-bell-slash"></i>
                <span>Tidak ada notifikasi baru.</span>
            </div>
        @endforelse
    </div>
    @if($__notifItems->count() > $__notifShown->count())
        <div class="text-center">
            <span class="text-muted" style="font-size:.74rem;font-weight:600;">+ {{ $__notifItems->count() - $__notifShown->count() }} notifikasi lainnya</span>
        </div>
    @endif
</li>
