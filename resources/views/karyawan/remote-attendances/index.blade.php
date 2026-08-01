@extends('layouts.app')
@section('title', 'Absen Luar Kantor')

@section('content')
    <div class="d-flex justify-content-between align-items-start align-items-md-center mb-4 flex-column flex-md-row gap-2">
        <div>
            <h4 class="mb-1">Absen Luar Kantor</h4>
            <p class="text-muted mb-0">Riwayat pengajuan absen dinas/lapangan/WFH kamu.</p>
        </div>
        <a href="{{ route('karyawan.remote-attendances.create') }}" class="btn btn-primary">
            <i class="bi bi-geo-alt"></i> Ajukan Absen Luar
        </a>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
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
                            <td>{{ $r->date->format('d-m-Y') }} <br><small class="text-muted font-mono">{{ \Illuminate\Support\Carbon::parse($r->time)->format('H:i') }}</small></td>
                            <td>{{ $r->punch_type_label }}</td>
                            <td><span class="badge bg-secondary"><i class="bi {{ $r->category_icon }}"></i> {{ $r->category_label }}</span></td>
                            <td class="text-truncate small text-muted" style="max-width: 180px;" title="{{ $r->note }}">{{ $r->note ?? '-' }}</td>
                            <td><span class="badge bg-{{ $r->status_badge }}"><i class="bi {{ $r->status_icon }}"></i> {{ $r->status_label }}</span></td>
                            <td class="text-muted small">{{ $r->admin_note ?? '-' }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#remote-timeline-{{ $r->id }}">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                @include('partials._timeline-modal', ['id' => 'remote-timeline-'.$r->id, 'title' => 'Riwayat Absen Luar Kantor', 'model' => $r])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Belum ada pengajuan absen luar kantor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $remoteAttendances->links() }}</div>
    </div>
@endsection
