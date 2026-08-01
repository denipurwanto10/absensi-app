@if(auth()->user()->isAdmin())
    <nav class="bottom-nav d-md-none">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('admin.employees.index') }}" class="{{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Karyawan
        </a>
        <div class="scan-fab-wrap">
            <a href="{{ route('admin.attendances.scanner') }}" class="p-0">
                <span class="scan-fab"><i class="bi bi-qr-code-scan"></i></span>
                <span class="scan-label">Scan</span>
            </a>
        </div>
        <a href="{{ route('admin.attendances.index') }}" class="{{ request()->routeIs('admin.attendances.index') ? 'active' : '' }}">
            <i class="bi bi-clipboard-data"></i> Laporan
        </a>
        <a href="#" class="{{ request()->routeIs(['admin.leaves.*','admin.corrections.*','admin.documents.*','admin.announcements.*','admin.holidays.*','admin.settings.*']) ? 'active' : '' }}" data-bs-toggle="offcanvas" data-bs-target="#moreMenuOffcanvas">
            <i class="bi bi-grid-3x3-gap"></i> Lainnya
        </a>
    </nav>

    <div class="offcanvas offcanvas-bottom more-menu-offcanvas" tabindex="-1" id="moreMenuOffcanvas" aria-labelledby="moreMenuLabel">
        <div class="offcanvas-header">
            <h6 class="offcanvas-title" id="moreMenuLabel">Menu Lainnya</h6>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <div class="more-menu-grid">
                <a href="{{ route('admin.leaves.index') }}" class="more-menu-item {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-envelope-paper"></i></span>
                    <span>Pengajuan Izin</span>
                    @php($__pendingLeaves = \App\Models\Leave::where('status', 'pending')->count())
                    @if($__pendingLeaves)
                        <span class="badge more-menu-badge">{{ $__pendingLeaves }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.corrections.index') }}" class="more-menu-item {{ request()->routeIs('admin.corrections.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-pencil-square"></i></span>
                    <span>Koreksi Absensi</span>
                    @php($__pendingCorrections = \App\Models\AttendanceCorrection::where('status', 'pending')->count())
                    @if($__pendingCorrections)
                        <span class="badge more-menu-badge">{{ $__pendingCorrections }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.remote-attendances.index') }}" class="more-menu-item {{ request()->routeIs('admin.remote-attendances.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-geo-alt"></i></span>
                    <span>Absen Luar Kantor</span>
                    @php($__pendingRemote = \App\Models\RemoteAttendance::where('status', 'pending')->count())
                    @if($__pendingRemote)
                        <span class="badge more-menu-badge">{{ $__pendingRemote }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.documents.index') }}" class="more-menu-item {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-folder2-open"></i></span>
                    <span>Dokumen Karyawan</span>
                </a>
                <a href="{{ route('admin.announcements.index') }}" class="more-menu-item {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-megaphone"></i></span>
                    <span>Pengumuman</span>
                </a>
                <a href="{{ route('admin.holidays.index') }}" class="more-menu-item {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-calendar-heart"></i></span>
                    <span>Kalender Libur</span>
                </a>
                <a href="{{ route('admin.settings.edit') }}" class="more-menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-gear"></i></span>
                    <span>Pengaturan</span>
                </a>
            </div>
        </div>
    </div>
@else
    <nav class="bottom-nav d-md-none">
        <a href="{{ route('karyawan.dashboard') }}" class="{{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i> Beranda
        </a>
        <a href="{{ route('karyawan.leaves.index') }}" class="{{ request()->routeIs('karyawan.leaves.*') ? 'active' : '' }}">
            <i class="bi bi-envelope-paper"></i> Izin
        </a>
        <div class="scan-fab-wrap">
            <a href="{{ route('karyawan.attendances.scanner') }}" class="p-0">
                <span class="scan-fab"><i class="bi bi-qr-code-scan"></i></span>
                <span class="scan-label">Absen</span>
            </a>
        </div>
        <a href="{{ route('karyawan.announcements.index') }}" class="{{ request()->routeIs('karyawan.announcements.*') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i> Info
        </a>
        <a href="#" class="{{ request()->routeIs(['karyawan.corrections.*','karyawan.holidays.*','karyawan.profile.*','karyawan.documents.*']) ? 'active' : '' }}" data-bs-toggle="offcanvas" data-bs-target="#moreMenuOffcanvas">
            <i class="bi bi-grid-3x3-gap"></i> Lainnya
        </a>
    </nav>

    <div class="offcanvas offcanvas-bottom more-menu-offcanvas" tabindex="-1" id="moreMenuOffcanvas" aria-labelledby="moreMenuLabel">
        <div class="offcanvas-header">
            <h6 class="offcanvas-title" id="moreMenuLabel">Menu Lainnya</h6>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <div class="more-menu-grid">
                <a href="{{ route('karyawan.corrections.index') }}" class="more-menu-item {{ request()->routeIs('karyawan.corrections.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-pencil-square"></i></span>
                    <span>Koreksi Absensi</span>
                </a>
                <a href="{{ route('karyawan.remote-attendances.index') }}" class="more-menu-item {{ request()->routeIs('karyawan.remote-attendances.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-geo-alt"></i></span>
                    <span>Absen Luar Kantor</span>
                </a>
                <a href="{{ route('karyawan.documents.index') }}" class="more-menu-item {{ request()->routeIs('karyawan.documents.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-folder2-open"></i></span>
                    <span>Dokumen Saya</span>
                </a>
                <a href="{{ route('karyawan.holidays.index') }}" class="more-menu-item {{ request()->routeIs('karyawan.holidays.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-calendar-heart"></i></span>
                    <span>Kalender Libur</span>
                </a>
                <a href="{{ route('karyawan.profile.edit') }}" class="more-menu-item {{ request()->routeIs('karyawan.profile.*') ? 'active' : '' }}">
                    <span class="more-menu-ico"><i class="bi bi-person-gear"></i></span>
                    <span>Profil Saya</span>
                </a>
            </div>
        </div>
    </div>
@endif
