@extends('layouts.app')
@section('title', 'Koreksi Absensi')

@section('content')
    <div class="d-flex justify-content-between align-items-start align-items-md-center mb-4 flex-column flex-md-row gap-2">
        <div>
            <h4 class="mb-1">Koreksi Absensi</h4>
            <p class="text-muted mb-0">Riwayat pengajuan koreksi jam masuk/pulang.</p>
        </div>
        <a href="{{ route('karyawan.corrections.create') }}" class="btn btn-primary">
            <i class="bi bi-pencil-square"></i> Ajukan Koreksi
        </a>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Perubahan Diminta</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                        <th class="text-end">Riwayat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($corrections as $c)
                        <tr>
                            <td>{{ $c->date->format('d-m-Y') }}</td>
                            <td class="font-mono small">{{ $c->field_summary }}</td>
                            <td class="text-truncate" style="max-width: 220px;" title="{{ $c->reason }}">{{ $c->reason }}</td>
                            <td><span class="badge bg-{{ $c->status_badge }}"><i class="bi {{ $c->status_icon }}"></i> {{ $c->status_label }}</span></td>
                            <td class="text-muted small">{{ $c->admin_note ?? '-' }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#corr-timeline-{{ $c->id }}">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                @include('partials._timeline-modal', ['id' => 'corr-timeline-'.$c->id, 'title' => 'Riwayat Koreksi Absensi', 'model' => $c])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Belum ada pengajuan koreksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $corrections->links() }}</div>
    </div>
@endsection
