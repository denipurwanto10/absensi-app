<ul class="nav nav-pills flex-md-column gap-1">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}">
            <i class="bi bi-people"></i> Data Karyawan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.attendances.scanner') ? 'active' : '' }}" href="{{ route('admin.attendances.scanner') }}">
            <i class="bi bi-qr-code-scan"></i> Scan Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.attendances.index') ? 'active' : '' }}" href="{{ route('admin.attendances.index') }}">
            <i class="bi bi-clipboard-data"></i> Laporan Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.remote-attendances.*') ? 'active' : '' }}" href="{{ route('admin.remote-attendances.index') }}">
            <i class="bi bi-geo-alt"></i> Absen Luar Kantor
            @php($__pendingRemote = \App\Models\RemoteAttendance::where('status', 'pending')->count())
            @if($__pendingRemote)
                <span class="badge ms-auto" style="background:var(--amber); color:var(--ink);">{{ $__pendingRemote }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}" href="{{ route('admin.leaves.index') }}">
            <i class="bi bi-envelope-paper"></i> Pengajuan Izin
            @php($__pendingLeaves = \App\Models\Leave::where('status', 'pending')->count())
            @if($__pendingLeaves)
                <span class="badge ms-auto" style="background:var(--amber); color:var(--ink);">{{ $__pendingLeaves }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.corrections.*') ? 'active' : '' }}" href="{{ route('admin.corrections.index') }}">
            <i class="bi bi-pencil-square"></i> Koreksi Absensi
            @php($__pendingCorrections = \App\Models\AttendanceCorrection::where('status', 'pending')->count())
            @if($__pendingCorrections)
                <span class="badge ms-auto" style="background:var(--amber); color:var(--ink);">{{ $__pendingCorrections }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}" href="{{ route('admin.documents.index') }}">
            <i class="bi bi-folder2-open"></i> Dokumen Karyawan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
            <i class="bi bi-megaphone"></i> Pengumuman
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}" href="{{ route('admin.holidays.index') }}">
            <i class="bi bi-calendar-heart"></i> Kalender Libur
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">
            <i class="bi bi-gear"></i> Pengaturan
        </a>
    </li>
</ul>
