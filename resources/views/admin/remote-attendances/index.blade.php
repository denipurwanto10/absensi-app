@extends('layouts.app')
@section('title', 'Absen Luar Kantor')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Absen Luar Kantor</h4>
        <p class="text-muted mb-0">Tinjau pengajuan absen dinas/lapangan/WFH — cek foto & titik lokasi sebelum menyetujui.</p>
    </div>

    <ul class="nav nav-pills gap-1 mb-3">
        @foreach(['pending' => 'Menunggu', 'processing' => 'Diproses', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'semua' => 'Semua'] as $key => $label)
            <li class="nav-item">
                <a href="{{ route('admin.remote-attendances.index', ['status' => $key]) }}"
                   class="nav-link {{ $status === $key ? 'active' : '' }}" style="font-size:.85rem;">
                    {{ $label }}
                    @if($key === 'pending' && $pendingCount)
                        <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Karyawan</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                        <th class="text-end">Riwayat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($remoteAttendances as $r)
                        <tr>
                            <td>
                                <a href="{{ $r->photo_url }}" target="_blank">
                                    <img src="{{ $r->photo_url }}" width="42" height="42" class="rounded-3" style="object-fit:cover;" alt="Foto bukti">
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $r->employee->photo_url }}" class="rounded-circle" width="30" height="30" style="object-fit:cover;" alt="">
                                    <div>
                                        <div>{{ $r->employee->user->name }}</div>
                                        <small class="text-muted">{{ $r->employee->nip }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono small">{{ $r->date->format('d-m-Y') }}<br>{{ \Illuminate\Support\Carbon::parse($r->time)->format('H:i') }}</td>
                            <td>{{ $r->punch_type_label }}</td>
                            <td><span class="badge bg-secondary"><i class="bi {{ $r->category_icon }}"></i> {{ $r->category_label }}</span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="showLocationMap({{ $r->lat }}, {{ $r->lng }}, '{{ addslashes($r->employee->user->name) }}', '{{ addslashes($r->category_label) }}')">
                                    <i class="bi bi-map"></i> Lihat Peta
                                </button>
                            </td>
                            <td class="text-truncate small" style="max-width: 160px;" title="{{ $r->note }}">{{ $r->note ?? '-' }}</td>
                            <td><span class="badge bg-{{ $r->status_badge }}"><i class="bi {{ $r->status_icon }}"></i> {{ $r->status_label }}</span></td>
                            <td class="text-end">
                                @if(in_array($r->status, ['pending', 'processing']))
                                    @if($r->status === 'pending')
                                        <form action="{{ route('admin.remote-attendances.process', $r) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-arrow-repeat"></i> Proses
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.remote-attendances.approve', $r) }}" method="POST" class="d-inline" data-confirm="Data absen luar kantor ini akan masuk ke laporan absensi karyawan." data-confirm-type="success" data-confirm-title="Setujui Absen Luar Kantor?" data-confirm-ok="Ya, Setujui">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-remote-{{ $r->id }}">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>

                                    <div class="modal fade" id="reject-remote-{{ $r->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.remote-attendances.reject', $r) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h6 class="modal-title">Tolak Pengajuan {{ $r->employee->user->name }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Alasan penolakan</label>
                                                        <textarea name="admin_note" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">{{ $r->admin_note ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#remote-timeline-{{ $r->id }}">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                @include('partials._timeline-modal', ['id' => 'remote-timeline-'.$r->id, 'title' => 'Riwayat Absen Luar Kantor '.$r->employee->user->name, 'model' => $r])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Tidak ada pengajuan absen luar kantor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $remoteAttendances->links() }}</div>
    </div>

    @include('partials._location-map-modal')
@endsection
