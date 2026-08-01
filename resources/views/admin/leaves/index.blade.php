@extends('layouts.app')
@section('title', 'Pengajuan Izin')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Pengajuan Izin / Sakit / Cuti</h4>
        <p class="text-muted mb-0">Tinjau dan setujui pengajuan dari karyawan.</p>
    </div>

    <ul class="nav nav-pills gap-1 mb-3">
        @foreach(['pending' => 'Menunggu', 'processing' => 'Diproses', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'semua' => 'Semua'] as $key => $label)
            <li class="nav-item">
                <a href="{{ route('admin.leaves.index', ['status' => $key]) }}"
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
                        <th>Karyawan</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                        <th class="text-end">Riwayat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $l)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $l->employee->photo_url }}" class="rounded-circle" width="30" height="30" style="object-fit:cover;" alt="">
                                    <div>
                                        <div>{{ $l->employee->user->name }}</div>
                                        <small class="text-muted">{{ $l->employee->nip }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary">{{ $l->type_label }}</span></td>
                            <td>
                                {{ $l->start_date->format('d-m-Y') }} &ndash; {{ $l->end_date->format('d-m-Y') }} <br>
                                <small class="text-muted">{{ $l->duration }} hari</small>
                                @if($l->type === 'cuti')
                                    <br><small class="text-muted">Sisa kuota: {{ $l->employee->cutiRemaining() }} / {{ $l->employee->leave_quota }} hari</small>
                                @endif
                            </td>
                            <td class="text-truncate" style="max-width: 200px;" title="{{ $l->reason }}">
                                {{ $l->reason }}
                                @if($l->attachment)
                                    <br><a href="{{ $l->attachment_url }}" target="_blank" class="small"><i class="bi bi-paperclip"></i> Lampiran</a>
                                @endif
                            </td>
                            <td>
                                @if($l->has_location)
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="showLocationMap({{ $l->submit_lat }}, {{ $l->submit_lng }}, '{{ addslashes($l->employee->user->name) }}', 'Saat mengajukan {{ $l->type_label }}')">
                                        <i class="bi bi-map"></i> Lihat Peta
                                    </button>
                                @else
                                    <span class="text-muted small">Tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $l->status_badge }}"><i class="bi {{ $l->status_icon }}"></i> {{ $l->status_label }}</span>
                            </td>
                            <td class="text-end">
                                @if(in_array($l->status, ['pending', 'processing']))
                                    @if($l->status === 'pending')
                                        <form action="{{ route('admin.leaves.process', $l) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-arrow-repeat"></i> Proses
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.leaves.approve', $l) }}" method="POST" class="d-inline" data-confirm="Pengajuan cuti/izin karyawan ini akan disetujui." data-confirm-type="success" data-confirm-title="Setujui Pengajuan Ini?" data-confirm-ok="Ya, Setujui">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-{{ $l->id }}">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>

                                    <div class="modal fade" id="reject-{{ $l->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.leaves.reject', $l) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h6 class="modal-title">Tolak Pengajuan {{ $l->employee->user->name }}</h6>
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
                                    <span class="text-muted small">{{ $l->admin_note ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leave-timeline-{{ $l->id }}">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                @include('partials._timeline-modal', ['id' => 'leave-timeline-'.$l->id, 'title' => 'Riwayat Pengajuan '.$l->employee->user->name, 'model' => $l])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Tidak ada pengajuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $leaves->links() }}</div>
    </div>

    @include('partials._location-map-modal')
@endsection
