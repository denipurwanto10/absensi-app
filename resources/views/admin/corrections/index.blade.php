@extends('layouts.app')
@section('title', 'Koreksi Absensi')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Koreksi Absensi</h4>
        <p class="text-muted mb-0">Tinjau permintaan koreksi jam masuk/pulang dari karyawan.</p>
    </div>

    <ul class="nav nav-pills gap-1 mb-3">
        @foreach(['pending' => 'Menunggu', 'processing' => 'Diproses', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'semua' => 'Semua'] as $key => $label)
            <li class="nav-item">
                <a href="{{ route('admin.corrections.index', ['status' => $key]) }}"
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
                        <th>Tanggal</th>
                        <th>Perubahan Diminta</th>
                        <th>Alasan</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                        <th class="text-end">Riwayat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($corrections as $c)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $c->employee->photo_url }}" class="rounded-circle" width="30" height="30" style="object-fit:cover;" alt="">
                                    <div>
                                        <div>{{ $c->employee->user->name }}</div>
                                        <small class="text-muted">{{ $c->employee->nip }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $c->date->format('d-m-Y') }}</td>
                            <td class="font-mono small">{{ $c->field_summary }}</td>
                            <td class="text-truncate" style="max-width: 200px;" title="{{ $c->reason }}">
                                {{ $c->reason }}
                                @if($c->attachment)
                                    <br><a href="{{ $c->attachment_url }}" target="_blank" class="small"><i class="bi bi-paperclip"></i> Lampiran</a>
                                @endif
                            </td>
                            <td>
                                @if($c->has_location)
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="showLocationMap({{ $c->submit_lat }}, {{ $c->submit_lng }}, '{{ addslashes($c->employee->user->name) }}', 'Saat mengajukan koreksi')">
                                        <i class="bi bi-map"></i> Lihat Peta
                                    </button>
                                @else
                                    <span class="text-muted small">Tidak tersedia</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $c->status_badge }}"><i class="bi {{ $c->status_icon }}"></i> {{ $c->status_label }}</span></td>
                            <td class="text-end">
                                @if(in_array($c->status, ['pending', 'processing']))
                                    @if($c->status === 'pending')
                                        <form action="{{ route('admin.corrections.process', $c) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-arrow-repeat"></i> Proses
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.corrections.approve', $c) }}" method="POST" class="d-inline" data-confirm="Data absensi karyawan akan diperbarui sesuai koreksi yang diajukan." data-confirm-type="success" data-confirm-title="Setujui Koreksi Ini?" data-confirm-ok="Ya, Setujui">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-corr-{{ $c->id }}">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>

                                    <div class="modal fade" id="reject-corr-{{ $c->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.corrections.reject', $c) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h6 class="modal-title">Tolak Koreksi {{ $c->employee->user->name }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Alasan penolakan</label>
                                                        <textarea name="admin_note" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak Koreksi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">{{ $c->admin_note ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#corr-timeline-{{ $c->id }}">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                @include('partials._timeline-modal', ['id' => 'corr-timeline-'.$c->id, 'title' => 'Riwayat Koreksi '.$c->employee->user->name, 'model' => $c])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Tidak ada pengajuan koreksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $corrections->links() }}</div>
    </div>

    @include('partials._location-map-modal')
@endsection
