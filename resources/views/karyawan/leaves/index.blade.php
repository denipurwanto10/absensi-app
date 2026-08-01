@extends('layouts.app')
@section('title', 'Pengajuan Izin')

@section('content')
    <div class="d-flex justify-content-between align-items-start align-items-md-center mb-4 flex-column flex-md-row gap-2">
        <div>
            <h4 class="mb-1">Izin / Sakit / Cuti</h4>
            <p class="text-muted mb-0">Riwayat pengajuan dan statusnya.</p>
        </div>
        <a href="{{ route('karyawan.leaves.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Ajukan Izin
        </a>
    </div>

    <div class="card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted small">Sisa Kuota Cuti Tahun {{ now()->year }}</span>
            <span class="fw-semibold">{{ $cutiRemaining }} / {{ $cutiQuota }} hari</span>
        </div>
        @php($__cutiPct = $cutiQuota > 0 ? min(100, round($cutiUsed / $cutiQuota * 100)) : 0)
        <div class="progress" style="height: 8px;">
            <div class="progress-bar" role="progressbar" style="width: {{ $__cutiPct }}%; background: var(--teal);"></div>
        </div>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Durasi</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                        <th class="text-end">Riwayat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $l)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $l->type_label }}</span></td>
                            <td>{{ $l->start_date->format('d-m-Y') }} &ndash; {{ $l->end_date->format('d-m-Y') }}</td>
                            <td>{{ $l->duration }} hari</td>
                            <td class="text-truncate" style="max-width: 220px;" title="{{ $l->reason }}">{{ $l->reason }}</td>
                            <td><span class="badge bg-{{ $l->status_badge }}"><i class="bi {{ $l->status_icon }}"></i> {{ $l->status_label }}</span></td>
                            <td class="text-muted small">{{ $l->admin_note ?? '-' }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leave-timeline-{{ $l->id }}">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                @include('partials._timeline-modal', ['id' => 'leave-timeline-'.$l->id, 'title' => 'Riwayat Pengajuan '.$l->type_label, 'model' => $l])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Belum ada pengajuan izin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $leaves->links() }}</div>
    </div>
@endsection
